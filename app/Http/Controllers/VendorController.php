<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Vendor;

class VendorController extends Controller
{
    /**
     * Show the vendor management page.
     */
    public function index()
    {
        $vendors = Vendor::query()
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.vendor.index', compact('vendors'));
    }

    /**
     * Show the create vendor page.
     */
    public function createNewVendor()
    {
        return view('admin.vendor.create');
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function _createNewVendor(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'prefix'       => 'nullable|string|max:20',
            'name'         => 'required|string|max:150|unique:vendors,name',
            'phone_number' => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:500',
            'vendor_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        // 2. Generate slug (unique)
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $validated['slug'] = $slug;

        // 3. Handle image upload (optional)
        if ($request->hasFile('vendor_image') && $request->file('vendor_image')->isValid()) {
            $path = $request->file('vendor_image')->store('vendors', 'public');
            $validated['vendor_image'] = $path;
        }

        // 4. Create record
        Vendor::create($validated);

        // 5. Redirect with success message
        return redirect()
            ->route('admin.vendor.index')
            ->with('success', 'Vendor ' . e($validated['name']) . ' berhasil ditambahkan.');
    }

    /**
     * Show the edit vendor page.
     */
    public function editVendor(Vendor $vendor)
    {
        return view('admin.vendor.edit', compact('vendor'));
    }

    /**
     * Delete a vendor (and its associated image if exists).
     */
    public function _deleteVendor(Vendor $vendor)
    {
        // 1. Check if there's an image and delete it from storage
        if ($vendor->vendor_image) {
            // Storage::disk('public') → matches the disk you used during upload
            if (Storage::disk('public')->exists($vendor->vendor_image)) {
                Storage::disk('public')->delete($vendor->vendor_image);
            }
        }

        // 2. Delete the vendor record from database
        $vendor->delete();

        // 3. Redirect with success message
        return redirect()
            ->route('admin.vendor.index')
            ->with('success', 'Vendor ' . e($vendor->name) . ' has been deleted successfully.');
    }
}