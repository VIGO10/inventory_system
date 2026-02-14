<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\CatalogSupplier;
use App\Models\Supplier;

class CatalogSupplierController extends Controller
{
    /**
     * Show the catalog supplier management page.
     */
    public function index(Request $request, Supplier $supplier)
    {
        $tab = $request->query('tab', 'available');
        $search = $request->query('search');

        $query = $supplier->catalogSuppliers()->latest();

        // Apply tab filter
        if ($tab === 'not-available') {
            $query->where('is_available', false);
        } else {
            $query->where('is_available', true);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $catalogs = $query->paginate(12)->appends($request->query());

        // Counts for tabs (you can optimize this later with cached counts if needed)
        $counts = [
            'available'     => $supplier->catalogSuppliers()->where('is_available', true)->count(),
            'not_available' => $supplier->catalogSuppliers()->where('is_available', false)->count(),
        ];

        return view('admin.supplier.catalog.index', compact('supplier', 'catalogs', 'counts', 'search'));
    }

    /**
     * Show the create catalog supplier page.
     */
    public function createNewCatalogSupplier(Supplier $supplier)
    {
        return view('admin.supplier.catalog.create', compact('supplier'));
    }

    /**
     * Store a newly created catalog supplier (product) for the given supplier.
     */
    public function _createNewCatalogSupplier(Request $request, Supplier $supplier)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'description'         => 'nullable|string',
            'title_1'             => 'required|string|max:100',
            'title_1_price'       => 'required|numeric|min:0',
            'title_2'             => 'required|string|max:100',
            'title_2_price'       => 'required|numeric|min:0',
            'value_per_title_2'   => 'required|integer|min:1',
            'minimum_order_title' => 'required|in:title_1,title_2',
            'minimum_order_qty'   => 'required|integer|min:1',
            'is_available'        => 'boolean',
            'product_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        // 2. Handle product image upload
        if ($request->hasFile('product_image') && $request->file('product_image')->isValid()) {
            $path = $request->file('product_image')->store('products', 'public');
            $validated['product_image'] = $path;
        }

        // 3. Set default values if needed
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['supplier_id']  = $supplier->id;

        if ($validated['minimum_order_title'] === 'title_1') {
            $validated['minimum_order_title'] = $validated['title_1'];
        } elseif ($validated['minimum_order_title'] === 'title_2') {
            $validated['minimum_order_title'] = $validated['title_2'];
        }

        // 4. Create the new CatalogSupplier record
        $catalogItem = CatalogSupplier::create($validated);

        // 5. Optional: success message + redirect
        return redirect()
            ->route('admin.supplier.catalog.index', $supplier->slug)
            ->with('success', "Product \"{$catalogItem->name}\" has been successfully added.");
    }

    /**
     * Show the edit catalog supplier page.
     */
    public function editCatalogSupplier(Supplier $supplier, CatalogSupplier $catalogSupplier)
    {
        $catalog = $catalogSupplier;
        return view('admin.supplier.catalog.edit', compact('supplier', 'catalog'));
    }

    public function _editCatalogSupplier(Request $request, Supplier $supplier, CatalogSupplier $catalogSupplier)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'description'         => 'nullable|string',
            'title_1'             => 'required|string|max:100',
            'title_1_price'       => 'required|numeric|min:0',
            'title_2'             => 'required|string|max:100',
            'title_2_price'       => 'required|numeric|min:0',
            'value_per_title_2'   => 'required|integer|min:1',
            'minimum_order_title' => 'required|in:title_1,title_2',
            'minimum_order_qty'   => 'required|integer|min:1',
            'is_available'        => 'boolean',
            'product_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('product_image')) {
            // Delete old image
            if ($catalogSupplier->product_image) {
                Storage::disk('public')->delete($catalogSupplier->product_image);
            }
            $validated['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        $catalogSupplier->update($validated);

        return redirect()
            ->route('admin.supplier.catalog.index', $supplier->slug)
            ->with('success', "Product \"{$catalogSupplier->name}\" updated successfully.");
    }

    /**
     * Activate or Deactivate a catalog supplier (product).
     */
    public function _toggleCatalogSupplierStatus(Supplier $supplier, CatalogSupplier $catalogSupplier)
    {
        // Toggle the is_available status
        if ($catalogSupplier->is_available) {
            $catalogSupplier->is_available = false;
            $message = 'Successfully deactivated ' . $catalogSupplier->name;
        } else {
            $catalogSupplier->is_available = true;
            $message = 'Successfully activated ' . $catalogSupplier->name;
        }
        
        $catalogSupplier->save();

        return redirect()
            ->route('admin.supplier.catalog.index', $supplier->slug)
            ->with('success', $message);
    }

    /**
     * Delete a catalog supplier (and its associated image if exists).
     */
    public function _deleteCatalogSupplier(Supplier $supplier, CatalogSupplier $catalogSupplier)
    {
        // 1. Check if there's an image and delete it from storage
        if ($catalogSupplier->product_image) {
            if (Storage::disk('public')->exists($catalogSupplier->product_image)) {
                Storage::disk('public')->delete($catalogSupplier->product_image);
            }
        }

        // 2. Delete the supplier record from database
        $catalogSupplier->delete();

        // 3. Redirect with success message
        return redirect()
            ->route('admin.supplier.catalog.index', $supplier->slug)
            ->with('success', 'Catalog Supplier ' . e($catalogSupplier->name) . ' has been deleted successfully.');
    }

    /**
     * Get available products by supplier (for AJAX requests).
     */
    public function getBySupplier($supplierId)
    {
        $products = CatalogSupplier::where('supplier_id', $supplierId)
            ->where('is_available', true)
            ->get([
                'id',
                'name',
                'title_1',
                'title_2',
                'value_per_title_2',
                'minimum_order_title',
                'minimum_order_qty',
                'title_1_price',
                'title_2_price'
            ]);

        return response()->json($products);
    }
}