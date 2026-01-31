<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Show the supplier management page.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->search($search);  // ← uses your scopeSearch()
            })
            ->orderBy('created_at', 'asc')
            ->paginate(15)
            ->appends($request->query()); // keeps ?search=... in pagination links

        return view('admin.supplier.index', compact('suppliers', 'search'));
    }

    /**
     * Show the create supplier page.
     */
    public function createNewSupplier()
    {
        return view('admin.supplier.create');
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function _createNewSupplier(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'prefix'       => 'nullable|string|max:20',
            'name'         => 'required|string|max:150|unique:suppliers,name',
            'phone_number' => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:500',
            'supplier_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        // 2. Generate slug (unique)
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        while (Supplier::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $validated['slug'] = $slug;

        // 3. Handle image upload (optional)
        if ($request->hasFile('supplier_image') && $request->file('supplier_image')->isValid()) {
            $path = $request->file('supplier_image')->store('suppliers', 'public');
            $validated['supplier_image'] = $path;
        }

        // 4. Create record
        Supplier::create($validated);

        // 5. Redirect with success message
        return redirect()
            ->route('admin.supplier.index')
            ->with('success', 'Supplier ' . e($validated['name']) . ' berhasil ditambahkan.');
    }

    /**
     * Show the edit supplier page.
     */
    public function editSupplier(Supplier $supplier)
    {
        return view('admin.supplier.edit', compact('supplier'));
    }

    public function _editSupplier(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'prefix'       => 'nullable|string|max:20',
            'name'         => 'required|string|max:150|unique:suppliers,name,' . $supplier->id,
            'phone_number' => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:500',
            'supplier_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Update slug if name changed
        if ($supplier->name !== $validated['name']) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Supplier::where('slug', $slug)->where('id', '!=', $supplier->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Handle image replacement
        if ($request->hasFile('supplier_image') && $request->file('supplier_image')->isValid()) {
            // Delete old image if exists
            if ($supplier->supplier_image) {
                Storage::disk('public')->delete($supplier->supplier_image);
            }
            $validated['supplier_image'] = $request->file('supplier_image')->store('suppliers', 'public');
        }

        $supplier->update($validated);

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Delete a supplier (and its associated image if exists).
     */
    public function _deleteSupplier(Supplier $supplier)
    {
        // 1. Check if there's an image and delete it from storage
        if ($supplier->supplier_image) {
            // Storage::disk('public') → matches the disk you used during upload
            if (Storage::disk('public')->exists($supplier->supplier_image)) {
                Storage::disk('public')->delete($supplier->supplier_image);
            }
        }

        // 2. Delete the supplier record from database
        $supplier->delete();

        // 3. Redirect with success message
        return redirect()
            ->route('admin.supplier.index')
            ->with('success', 'Supplier ' . e($supplier->name) . ' has been deleted successfully.');
    }
}