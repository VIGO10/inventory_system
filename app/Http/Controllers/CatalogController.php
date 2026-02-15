<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Catalog;

class CatalogController extends Controller
{
    /**
     * Show the catalog management page.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'available');
        $search = $request->query('search');

        $query = Catalog::latest();

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
            'available'     => Catalog::where('is_available', true)->count(),
            'not_available' => Catalog::where('is_available', false)->count(),
        ];

        return view('admin.catalog.index', compact('catalogs', 'counts', 'search'));
    }

    /**
     * Show the create catalog page.
     */
    public function createNewCatalog()
    {
        return view('admin.catalog.create');
    }

    /**
     * Store a newly created catalog (product) for the given supplier.
     */
    public function _createNewCatalog(Request $request)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'description'         => 'nullable|string',
            'title_1'             => 'required|string|max:100',
            'title_1_price'       => 'required|numeric|min:0',
            'title_1_qty'         => 'required|integer|min:0',
            'title_2'             => 'required|string|max:100',
            'title_2_price'       => 'required|numeric|min:0',
            'title_2_qty'         => 'required|integer|min:0',
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

        if ($validated['minimum_order_title'] === 'title_1') {
            $validated['minimum_order_title'] = $validated['title_1'];
        } elseif ($validated['minimum_order_title'] === 'title_2') {
            $validated['minimum_order_title'] = $validated['title_2'];
        }

        // 4. Create the new Catalog record
        $catalogItem = Catalog::create($validated);

        // 5. Optional: success message + redirect
        return redirect()
            ->route('admin.catalog.index')
            ->with('success', "Product \"{$catalogItem->name}\" has been successfully added.");
    }

    /**
     * Show the edit catalog page.
     */
    public function editCatalog(Catalog $catalog)
    {
        return view('admin.catalog.edit', compact('catalog'));
    }

    public function _editCatalog(Request $request, Catalog $catalog)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'description'         => 'nullable|string',
            'title_1'             => 'required|string|max:100',
            'title_1_price'       => 'required|numeric|min:0',
            'title_1_qty'         => 'required|integer|min:0',
            'title_2'             => 'required|string|max:100',
            'title_2_price'       => 'required|numeric|min:0',
            'title_2_qty'         => 'required|integer|min:0',
            'value_per_title_2'   => 'required|integer|min:1',
            'minimum_order_title' => 'required|in:title_1,title_2',
            'minimum_order_qty'   => 'required|integer|min:1',
            'is_available'        => 'boolean',
            'product_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('product_image')) {
            // Delete old image
            if ($catalog->product_image) {
                Storage::disk('public')->delete($catalog->product_image);
            }
            $validated['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        $catalog->update($validated);

        return redirect()
            ->route('admin.catalog.index')
            ->with('success', "Product \"{$catalog->name}\" updated successfully.");
    }

    /**
     * Activate or Deactivate a catalog (product).
     */
    public function _toggleCatalogStatus(Catalog $catalog)
    {
        // Toggle the is_available status
        if ($catalog->is_available) {
            $catalog->is_available = false;
            $message = 'Successfully deactivated ' . $catalog->name;
        } else {
            $catalog->is_available = true;
            $message = 'Successfully activated ' . $catalog->name;
        }
        
        $catalog->save();

        return redirect()
            ->route('admin.catalog.index')
            ->with('success', $message);
    }

    /**
     * Delete a catalog (and its associated image if exists).
     */
    public function _deleteCatalog(Catalog $catalog)
    {
        // 1. Check if there's an image and delete it from storage
        if ($catalog->product_image) {
            if (Storage::disk('public')->exists($catalog->product_image)) {
                Storage::disk('public')->delete($catalog->product_image);
            }
        }

        // 2. Delete the catalog record from database
        $catalog->delete();

        // 3. Redirect with success message
        return redirect()
            ->route('admin.catalog.index')
            ->with('success', 'Catalog ' . e($catalog->name) . ' has been deleted successfully.');
    }

    public function getAvailableCatalog()
    {
        $products = Catalog::where('is_available', true)
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