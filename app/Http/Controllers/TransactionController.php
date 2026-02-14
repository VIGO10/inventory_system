<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\TransactionInbound;
use App\Models\TransactionInboundItem;
use App\Models\Supplier;
use App\Models\Vendor;
use App\Models\Catalog;
use App\Models\CatalogSupplier;
use App\Models\TransactionOutbound;
use App\Models\TransactionOutboundItem;
use App\Models\OtherCost;

class TransactionController extends Controller
{
    /**
     * Show the transaction management page.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'inbound');

        // Common query builder depending on tab
        $query = $tab === 'inbound'
            ? TransactionInbound::with('supplier')
            : TransactionOutbound::with('vendor');

        // Search filter (reference number or supplier/vendor name)
        if ($search = $request->query('search')) {
            $search = '%' . trim($search) . '%';

            $query->where(function ($q) use ($search, $tab) {
                $q->where('reference_number', 'like', $search);

                if ($tab === 'inbound') {
                    $q->orWhereHas('supplier', function ($sub) use ($search) {
                        $sub->where('name', 'like', $search);
                    });
                } else {
                    $q->orWhereHas('vendor', function ($sub) use ($search) {
                        $sub->where('name', 'like', $search);
                    });
                }
            });
        }

        // Separate month and year filters
        if ($month = $request->query('month')) {
            $query->whereMonth('created_date', $month);
        }

        if ($year = $request->query('year')) {
            $query->whereYear('created_date', $year);
        }

        // Status filter
        if ($status = $request->query('status')) {
            switch ($status) {
                case 'draft':
                    $query->where('is_published', false);
                    break;
                case 'published':
                    $query->where('is_published', true)
                          ->where('is_completed', false);
                    break;
                case 'completed':
                    $query->where('is_completed', true)
                          ->where('is_paid', false);
                    break;
                case 'paid':
                    $query->where('is_paid', true);
                    break;
                case 'overdue':
                    $query->where('is_paid', false)
                          ->where('deadline_payment_date', '<', now())
                          ->where('is_completed', false);
                    break;
            }
        }

        // Order by latest created_date and paginate
        $items = $query->latest('created_date')->paginate(12)->appends($request->query());

        // Counts (for tabs)
        $counts = [
            'inbound'  => TransactionInbound::count(),
            'outbound' => TransactionOutbound::count(),
        ];

        // Pass the correct collection to the view
        $inbounds = $tab === 'inbound' ? $items : collect();
        $outbounds = $tab === 'outbound' ? $items : collect();

        return view('admin.transaction.index', compact(
            'inbounds',
            'outbounds',
            'items',
            'counts',
            'tab'
        ));
    }

    // Inbound Transaction

    /**
     * Show the details of an inbound transaction.
     */
    public function detailInboundTransaction(TransactionInbound $transaction)
    {
        return view('admin.transaction.inbound.detail', compact('transaction'));
    }

    public function createNewInboundTransaction()
    {
        // Fetch Suppliers, CatalogSuppliers, and Catalogs for the form
        $suppliers = Supplier::all();
        $catalogs = Catalog::all();

        return view('admin.transaction.inbound.create', compact('suppliers', 'catalogs'));
    }

    public function _createNewInboundTransaction(Request $request)
    {
        // ─── VALIDATION ───────────────────────────────────────────────────────
        $validated = $request->validate([
            'supplier_id'             => 'required|exists:suppliers,id',
            'deadline_payment_date'   => 'required|date|after_or_equal:today',
            'transaction_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // 4MB max
            'discount'                => 'nullable|numeric|min:0',
            'items'                   => 'required|array|min:1',
            'items.*.catalog_supplier_id' => 'required|exists:catalog_suppliers,id',
            'items.*.catalog_id'      => 'required|exists:catalogs,id',
            'items.*.title_1_qty'     => 'nullable|integer|min:0',
            'items.*.title_2_qty'     => 'nullable|integer|min:0',
            'items.*.discount'        => 'nullable|numeric|min:0',
            'other_costs'             => 'nullable|array|min:1',
            'other_costs.*.name'     => 'required|string|max:255',
            'other_costs.*.price'   => 'required|numeric|min:0',
        ]);

        $hasValidQty = collect($validated['items'])->some(function ($item) {
            return ($item['title_1_qty'] ?? 0) > 0 || ($item['title_2_qty'] ?? 0) > 0;
        });

        if (!$hasValidQty) {
            throw ValidationException::withMessages([
                'items' => 'At least one item must have quantity > 0.'
            ]);
        }

        // Make Transaction Inbound
        // 1. Handle image upload (if any)
        $imagePath = null;
        if ($request->hasFile('transaction_image') && $request->file('transaction_image')->isValid()) {
            $imagePath = $request->file('transaction_image')
                ->store('transaction-inbound-invoices', 'public');
        }

        // 2. Create main transaction
        $transaction = TransactionInbound::create([
            'reference_number'     => 'INB-' . now()->format('dmy') . '-' . str_pad(TransactionInbound::count() + 1, 4, '0', STR_PAD_LEFT),
            'supplier_id'          => $validated['supplier_id'],
            'discount'             => $validated['discount'] ?? 0,
            'deadline_payment_date'=> $validated['deadline_payment_date'],
            'transaction_image'    => $imagePath,
            'created_date'         => now(),
            'is_published'         => false,     // adjust logic if you auto-publish
            'is_completed'         => false,
            'is_paid'              => false,
        ]);

        // 3. Create items + calculate totals
        $subtotal = 0;

        foreach ($validated['items'] as $itemData) {

            // Get price information from catalog_supplier
            $catalogSupplier = CatalogSupplier::findOrFail($itemData['catalog_supplier_id']);

            $title1Price = $catalogSupplier->title_1_price ?? 0;
            $title2Price = $catalogSupplier->title_2_price ?? 0;

            $qty1   = (int) ($itemData['title_1_qty'] ?? 0);
            $qty2   = (int) ($itemData['title_2_qty'] ?? 0);
            $itemDiscount = (float) ($itemData['discount'] ?? 0);

            $itemTotalBeforeDiscount = ($qty1 * $title1Price) + ($qty2 * $title2Price);
            $itemFinalTotal = max(0, $itemTotalBeforeDiscount - $itemDiscount);

            $subtotal += $itemFinalTotal;

            TransactionInboundItem::create([
                'transaction_inbound_id' => $transaction->id,
                'catalog_supplier_id'    => $itemData['catalog_supplier_id'],
                'catalog_id'             => $itemData['catalog_id'],
                'supplier_id'            => $validated['supplier_id'], // denormalized for easier queries
                'price'                  => $itemFinalTotal,
                'title_1_qty'            => $qty1,
                'title_1_price'          => $title1Price,
                'title_2_qty'            => $qty2,
                'title_2_price'          => $title2Price,
                'discount'               => $itemDiscount,
            ]);
        }

        // 4. Update transaction total (after discount)
        $grandTotal = max(0, $subtotal - ($validated['discount'] ?? 0));

        $transaction->update([
            'total_price' => $grandTotal,
        ]);

        // Save the other cost
        foreach ($validated['other_costs'] as $otherCost) {
            OtherCost::create([
              'name' => $otherCost['name'],
              'price' => $otherCost['price'],
              'type' => 'out',
            ]);
        }

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'inbound'])
            ->with('success', 'Purchase transaction created successfully.');

    }

    public function editInboundTransaction (TransactionInbound $transaction)
    {
        $suppliers = Supplier::all();
        $catalogs = Catalog::all();

        return view('admin.transaction.inbound.edit', compact('transaction', 'suppliers', 'catalogs'));
    }

    public function _editInboundTransaction(Request $request, TransactionInbound $transaction)
    {
        // ─── VALIDATION ───────────────────────────────────────────────────────
        $validated = $request->validate([
            'supplier_id'             => 'required|exists:suppliers,id',
            'deadline_payment_date'   => 'required|date|after_or_equal:today',
            'transaction_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'discount'                => 'nullable|numeric|min:0',
            'items'                   => 'required|array|min:1',
            'items.*.catalog_supplier_id' => 'required|exists:catalog_suppliers,id',
            'items.*.catalog_id'      => 'required|exists:catalogs,id',
            'items.*.title_1_qty'     => 'nullable|integer|min:0',
            'items.*.title_2_qty'     => 'nullable|integer|min:0',
            'items.*.discount'        => 'nullable|numeric|min:0',
        ]);

        // At least one item must have qty > 0
        $hasValidQty = collect($validated['items'])->some(fn($item) => 
            ($item['title_1_qty'] ?? 0) > 0 || ($item['title_2_qty'] ?? 0) > 0
        );

        if (!$hasValidQty) {
            throw ValidationException::withMessages([
                'items' => 'At least one item must have qty > 0.'
            ]);
        }

        // ─── 1. Handle image ─────────────────────────────────────────────────
        $imagePath = $transaction->transaction_image;

        if ($request->hasFile('transaction_image') && $request->file('transaction_image')->isValid()) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('transaction_image')
                ->store('transaction-inbound-invoices', 'public');
        }

        // ─── 2. Update main transaction ──────────────────────────────────────
        $transaction->update([
            'supplier_id'          => $validated['supplier_id'],
            'discount'             => $validated['discount'] ?? 0,
            'deadline_payment_date'=> $validated['deadline_payment_date'],
            'transaction_image'    => $imagePath,
        ]);

        // ─── 3. DELETE ALL OLD ITEMS ─────────────────────────────────────────
        $transaction->items()->delete();

        // ─── 4. CREATE ALL ITEMS FROM REQUEST ────────────────────────────────
        $subtotal = 0;

        foreach ($validated['items'] as $itemData) {
            $catalogSupplier = CatalogSupplier::findOrFail($itemData['catalog_supplier_id']);

            $title1Price = $catalogSupplier->title_1_price ?? 0;
            $title2Price = $catalogSupplier->title_2_price ?? 0;

            $qty1         = (int) ($itemData['title_1_qty'] ?? 0);
            $qty2         = (int) ($itemData['title_2_qty'] ?? 0);
            $itemDiscount = (float) ($itemData['discount'] ?? 0);

            $itemTotalBeforeDiscount = ($qty1 * $title1Price) + ($qty2 * $title2Price);
            $itemFinalTotal = max(0, $itemTotalBeforeDiscount - $itemDiscount);

            $subtotal += $itemFinalTotal;

            TransactionInboundItem::create([
                'transaction_inbound_id' => $transaction->id,
                'catalog_supplier_id'    => $itemData['catalog_supplier_id'],
                'catalog_id'             => $itemData['catalog_id'],
                'supplier_id'            => $validated['supplier_id'],
                'title_1_qty'            => $qty1,
                'title_1_price'          => $title1Price,
                'title_2_qty'            => $qty2,
                'title_2_price'          => $title2Price,
                'discount'               => $itemDiscount,
            ]);
        }

        // ─── 5. Update grand total ───────────────────────────────────────────
        $grandTotal = max(0, $subtotal - ($validated['discount'] ?? 0));

        $transaction->update([
            'total_price' => $grandTotal,
        ]);

        return redirect()
            ->route('admin.transaction.inbound.detail', ['transaction' => $transaction->reference_number])
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)) . ' updated successfully.');
    }

    public function _publishInboundTransaction(TransactionInbound $transaction)
    {
        // Set published the transaction
        $transaction->update([
            'is_published' => true,
            'published_date' => now(),
        ]);
        
        $transaction->save();

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'inbound'])
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)) . ' published successfully.');
    }

    public function _completeInboundTransaction(TransactionInbound $transaction)
    {
        // Set completed the transaction
        $transaction->update([
            'is_completed' => true,
            'completed_date' => now(),
        ]);
        
        $transaction->save();

        // Update catalog stock
        foreach ($transaction->items as $item) {
            Catalog::where('id', $item->catalog_id)->increment('title_1_qty', $item->title_1_qty);
            Catalog::where('id', $item->catalog_id)->increment('title_2_qty', $item->title_2_qty);
        }

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'inbound'])
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)) . ' completed successfully.');
    }

    public function _setInboundTransactionPaid(TransactionInbound $transaction)
    {
        // Set paid the transaction
        $transaction->update([
            'is_paid' => true,
            'paid_date' => now(),
        ]);
        
        $transaction->save();

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'inbound'])
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)) . ' paid successfully.');
    }

    public function _deleteInboundTransaction(TransactionInbound $transaction)
    {
        $transaction->delete();
        return redirect()
            ->route('admin.transaction.index', ['tab' => 'inbound'])
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)). ' has been deleted successfully.');
    }

    // Outbound Transaction

    public function detailOutboundTransaction(TransactionOutbound $transaction)
    {
        return view('admin.transaction.outbound.detail', compact('transaction'));
    }

    public function createNewOutboundTransaction()
    {
        // Fetch Vendor
        $vendors = Vendor::all();

        return view('admin.transaction.outbound.create', compact('vendors'));
    }

    public function _createNewOutboundTransaction(Request $request)
    {
        // ─── VALIDATION ───────────────────────────────────────────────────────
        $validated = $request->validate([
            'vendor_id'             => 'required|exists:vendors,id',
            'deadline_payment_date'   => 'required|date|after_or_equal:today',
            'transaction_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // 4MB max
            'discount'                => 'nullable|numeric|min:0',
            'items'                   => 'required|array|min:1',
            'items.*.catalog_id'      => 'required|exists:catalogs,id',
            'items.*.title_1_qty'     => 'nullable|integer|min:0',
            'items.*.title_2_qty'     => 'nullable|integer|min:0',
            'items.*.discount'        => 'nullable|numeric|min:0',
        ]);

        $hasValidQty = collect($validated['items'])->some(function ($item) {
            return ($item['title_1_qty'] ?? 0) > 0 || ($item['title_2_qty'] ?? 0) > 0;
        });

        if (!$hasValidQty) {
            throw ValidationException::withMessages([
                'items' => 'At least one item must have quantity > 0.'
            ]);
        }

        // Make Transaction Outbound
        // 1. Handle image upload (if any)
        $imagePath = null;
        if ($request->hasFile('transaction_image') && $request->file('transaction_image')->isValid()) {
            $imagePath = $request->file('transaction_image')
                ->store('transaction-outbound-invoices', 'public');
        }

        // 2. Create main transaction
        $transaction = TransactionOutbound::create([
            'reference_number'     => 'OUTB-' . now()->format('dmy') . '-' . str_pad(TransactionOutbound::count() + 1, 4, '0', STR_PAD_LEFT),
            'vendor_id'          => $validated['vendor_id'],
            'discount'             => $validated['discount'] ?? 0,
            'deadline_payment_date'=> $validated['deadline_payment_date'],
            'transaction_image'    => $imagePath,
            'created_date'         => now(),
            'is_published'         => false,     // adjust logic if you auto-publish
            'is_completed'         => false,
            'is_paid'              => false,
        ]);

        // 3. Create items + calculate totals
        $subtotal = 0;
        $netProfit = 0;

        foreach ($validated['items'] as $itemData) {

            // Get price information from catalog
            $catalog = Catalog::findOrFail($itemData['catalog_id']);

            $title1Price = $catalog->title_1_price ?? 0;
            $title2Price = $catalog->title_2_price ?? 0;

            $qty1   = (int) ($itemData['title_1_qty'] ?? 0);
            $qty2   = (int) ($itemData['title_2_qty'] ?? 0);
            $itemDiscount = (float) ($itemData['discount'] ?? 0);

            $itemTotalBeforeDiscount = ($qty1 * $title1Price) + ($qty2 * $title2Price);
            $itemFinalTotal = max(0, $itemTotalBeforeDiscount - $itemDiscount);

            $subtotal += $itemFinalTotal;

            // Calculate the buy price, search from transaction inbound
            $transactionInboundItem = TransactionInboundItem::where('catalog_id', $itemData['catalog_id'])
                ->whereHas('transaction', function ($q) {
                    $q->where('is_completed', 1);
                })
                ->orderByDesc('transaction_inbound_id')
                ->first();

            $buyPrice = ($qty1 * $transactionInboundItem->title_1_price) + ($qty2 * $transactionInboundItem->title_2_price);

            // Calculate the net_profit
            $netProfit += $itemFinalTotal - $buyPrice;

            TransactionOutboundItem::create([
                'transaction_outbound_id' => $transaction->id,
                'catalog_id'             => $itemData['catalog_id'],
                'price'                  => $itemFinalTotal,
                'buy_price'              => $buyPrice,
                'title_1_qty'            => $qty1,
                'title_1_price'          => $title1Price,
                'title_2_qty'            => $qty2,
                'title_2_price'          => $title2Price,
                'discount'               => $itemDiscount,
            ]);
        }

        // 4. Update transaction total (after discount)
        $grandTotal = max(0, $subtotal - ($validated['discount'] ?? 0));

        $transaction->update([
            'total_price' => $grandTotal,
            'net_profit'  => $netProfit,
        ]);

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'outbound'])
            ->with('success', 'Sale transaction created successfully.');

    }

    public function editOutboundTransaction (TransactionOutbound $transaction)
    {
        // Fetch Vendor
        $vendors = Vendor::all();

        return view('admin.transaction.outbound.edit', compact('transaction', 'vendors'));
    }

    public function _editOutboundTransaction(Request $request, TransactionOutbound $transaction)
    {
        // ─── VALIDATION ───────────────────────────────────────────────────────
        $validated = $request->validate([
            'vendor_id'             => 'required|exists:vendors,id',
            'deadline_payment_date'   => 'required|date|after_or_equal:today',
            'transaction_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // 4MB max
            'discount'                => 'nullable|numeric|min:0',
            'items'                   => 'required|array|min:1',
            'items.*.catalog_id'      => 'required|exists:catalogs,id',
            'items.*.title_1_qty'     => 'nullable|integer|min:0',
            'items.*.title_2_qty'     => 'nullable|integer|min:0',
            'items.*.discount'        => 'nullable|numeric|min:0',
        ]);

        // At least one item must have qty > 0
        $hasValidQty = collect($validated['items'])->some(fn($item) => 
            ($item['title_1_qty'] ?? 0) > 0 || ($item['title_2_qty'] ?? 0) > 0
        );

        if (!$hasValidQty) {
            throw ValidationException::withMessages([
                'items' => 'At least one item must have qty > 0.'
            ]);
        }

        // ─── 1. Handle image ─────────────────────────────────────────────────
        $imagePath = $transaction->transaction_image;

        if ($request->hasFile('transaction_image') && $request->file('transaction_image')->isValid()) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('transaction_image')
                ->store('transaction-outbound-invoices', 'public');
        }

        // ─── 2. Update main transaction ──────────────────────────────────────
        $transaction->update([
            'supplier_id'          => $validated['vendor_id'],
            'discount'             => $validated['discount'] ?? 0,
            'deadline_payment_date'=> $validated['deadline_payment_date'],
            'transaction_image'    => $imagePath,
        ]);

        // ─── 3. DELETE ALL OLD ITEMS ─────────────────────────────────────────
        $transaction->items()->delete();

        // ─── 4. CREATE ALL ITEMS FROM REQUEST ────────────────────────────────
        $subtotal = 0;
        $netProfit = 0;

        foreach ($validated['items'] as $itemData) {

            // Get price information from catalog
            $catalog = Catalog::findOrFail($itemData['catalog_id']);

            $title1Price = $catalog->title_1_price ?? 0;
            $title2Price = $catalog->title_2_price ?? 0;

            $qty1   = (int) ($itemData['title_1_qty'] ?? 0);
            $qty2   = (int) ($itemData['title_2_qty'] ?? 0);
            $itemDiscount = (float) ($itemData['discount'] ?? 0);

            $itemTotalBeforeDiscount = ($qty1 * $title1Price) + ($qty2 * $title2Price);
            $itemFinalTotal = max(0, $itemTotalBeforeDiscount - $itemDiscount);

            $subtotal += $itemFinalTotal;

            // Calculate the buy price, search from transaction inbound
            $transactionInboundItem = TransactionInboundItem::where('catalog_id', $itemData['catalog_id'])
                ->whereHas('transaction', function ($q) {
                    $q->where('is_completed', 1);
                })
                ->orderByDesc('transaction_inbound_id')
                ->first();

            $buyPrice = ($qty1 * $transactionInboundItem->title_1_price) + ($qty2 * $transactionInboundItem->title_2_price);

            // Calculate the net_profit
            $netProfit += $itemFinalTotal - $buyPrice;

            TransactionOutboundItem::create([
                'transaction_outbound_id' => $transaction->id,
                'catalog_id'             => $itemData['catalog_id'],
                'price'                  => $itemFinalTotal,
                'buy_price'              => $buyPrice,
                'title_1_qty'            => $qty1,
                'title_1_price'          => $title1Price,
                'title_2_qty'            => $qty2,
                'title_2_price'          => $title2Price,
                'discount'               => $itemDiscount,
            ]);
        }

        // 4. Update transaction total (after discount)
        $grandTotal = max(0, $subtotal - ($validated['discount'] ?? 0));

        $transaction->update([
            'total_price' => $grandTotal,
            'net_profit'  => $netProfit,
        ]);

        return redirect()
            ->route('admin.transaction.outbound.detail', $transaction->reference_number)
            ->with('success', 'Sale transaction ' . (e($transaction->reference_number)) . ' updated successfully.');
    }

    public function _publishOutboundTransaction(TransactionOutbound $transaction)
    {
        // Set published the transaction
        $transaction->update([
            'is_published' => true,
            'published_date' => now(),
        ]);
        
        $transaction->save();

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'outbound'])
            ->with('success', 'Sale Transaction ' . (e($transaction->reference_number)) . ' published successfully.');
    }

    public function _completeOutboundTransaction(TransactionOutbound $transaction)
    {
        // Set completed the transaction
        $transaction->update([
            'is_completed' => true,
            'completed_date' => now(),
        ]);
        
        $transaction->save();

        // Update catalog stock
        foreach ($transaction->items as $item) {
            Catalog::where('id', $item->catalog_id)->increment('title_1_qty', $item->title_1_qty);
            Catalog::where('id', $item->catalog_id)->increment('title_2_qty', $item->title_2_qty);
        }

        return redirect()
            ->route('admin.transaction.index')
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)) . ' completed successfully.');
    }

    public function _setOutboundTransactionPaid(TransactionOutbound $transaction)
    {
        // Set paid the transaction
        $transaction->update([
            'is_paid' => true,
            'paid_date' => now(),
        ]);
        
        $transaction->save();

        return redirect()
            ->route('admin.transaction.index', ['tab' => 'outbound'])
            ->with('success', 'Sale Transaction ' . (e($transaction->reference_number)) . ' paid successfully.');
    }

    public function _deleteOutboundTransaction(TransactionOutbound $transaction)
    {
        $transaction->delete();
        return redirect()
            ->route('admin.transaction.index', ['tab' => 'outbound'])
            ->with('success', 'Purchase Transaction ' . (e($transaction->reference_number)). ' has been deleted successfully.');
    }
}