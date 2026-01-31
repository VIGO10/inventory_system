<x-guest-layout>
    @if (Session::get('fail'))
        <div class="alert alert-danger">
            {{ Session::get('fail') }}
        </div>
    @endif
    @if (Session::get('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif

    <div style="padding: 2rem 1rem; max-width: 1400px; margin: 0 auto;">
        <!-- Header -->
        <div style="
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 2rem;
        ">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="color: black;">
                        <a href="{{ route('admin.dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item" style="color: black;">
                        <a href="{{ route('admin.supplier.index') }}">Supplier Management</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                        Create New Supplier
                    </li>
                </ol>
            </nav>

            <h1 style="
                font-size: 2.25rem;
                font-weight: 700;
                color: #111827;
                margin: 0 0 0.5rem 0;
            ">
                Add New Supplier
            </h1>
            <p style="color: #6b7280; font-size: 1rem; margin: 0;">
                Fill in the supplier details below
            </p>
        </div>

        <!-- Form Card -->
        <div style="
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            padding: 2rem;
        ">
            <form action="{{ route('admin.supplier.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">

                    <!-- Left column -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        <!-- Prefix -->
                        <div>
                            <label for="prefix" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Prefix / Business Type
                            </label>
                            <select name="prefix" id="prefix"
                                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; background: white; color: #111827;">
                                <option value="Tk." {{ old('prefix') === 'Tk.' ? 'selected' : '' }}>Tk.</option>
                                <option value="CV." {{ old('prefix') === 'CV.' ? 'selected' : '' }}>CV.</option>
                                <option value="PT." {{ old('prefix') === 'PT.' ? 'selected' : '' }}>PT.</option>
                            </select>
                            @error('prefix')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Supplier Name <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Elektronik Jaya"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            @error('name')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Phone Number
                            </label>
                            <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                   placeholder="0812-3456-7890"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            @error('phone_number')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Right column -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        <!-- Address -->
                        <div>
                            <label for="address" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Address
                            </label>
                            <textarea name="address" id="address" rows="4"
                                      placeholder="Jl. Sudirman No. 45, Kel. Margahayu, Kec. Bekasi Timur, Kota Bekasi, Jawa Barat 17113"
                                      style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; resize: vertical; color: #111827;">{{ old('address') }}</textarea>
                            @error('address')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier Image -->
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Supplier Logo / Photo <small style="color: #6b7280;">(optional)</small>
                            </label>

                            <div id="image-preview" style="
                                width: 180px;
                                height: 180px;
                                border: 2px dashed #d1d5db;
                                border-radius: 0.75rem;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                background: #f9fafb;
                                margin-bottom: 1rem;
                                overflow: hidden;
                                position: relative;
                            ">
                                <div style="text-align: center; color: #9ca3af;">
                                    <div style="font-size: 2.5rem;">🖼️</div>
                                    <p style="font-size: 0.875rem; margin: 0.5rem 0 0;">Preview</p>
                                </div>
                            </div>

                            <input type="file" name="supplier_image" id="supplier_image" accept="image/*"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; color: #111827;">
                            @error('s')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 2.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="{{ route('admin.supplier.index') }}"
                    style="
                        padding: 0.75rem 1.5rem;
                        background: #ef4444;
                        color: white;
                        border-radius: 0.5rem;
                        text-decoration: none;
                        font-weight: 500;
                        transition: background 0.2s;
                    "
                    onmouseover="this.style.background='#dc2626'"
                    onmouseout="this.style.background='#ef4444'">
                        Cancel
                    </a>

                    <button type="submit"
                            style="
                                background: #6366f1;
                                color: white;
                                padding: 0.75rem 1.5rem;
                                border: none;
                                border-radius: 0.5rem;
                                font-weight: 500;
                                cursor: pointer;
                                transition: background 0.2s;
                            "
                            onmouseover="this.style.background='#4f46e5'"
                            onmouseout="this.style.background='#6366f1'">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image preview script -->
    <script>
        document.getElementById('supplier_image').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.innerHTML = `
                    <div style="text-align: center; color: #9ca3af;">
                        <div style="font-size: 2.5rem;">🖼️</div>
                        <p style="font-size: 0.875rem; margin: 0.5rem 0 0;">Preview</p>
                    </div>
                `;
            }
        });
    </script>
</x-guest-layout>