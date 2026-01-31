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
                    <li class="breadcrumb-item" style="color: black;">
                        <a href="{{ route('admin.supplier.catalog.index', $supplier->slug) }}">{{ $supplier->prefix ? $supplier->prefix . ' ' : '' }}{{ $supplier->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                        Create New Catalog
                    </li>
                </ol>
            </nav>

            <h1 style="
                font-size: 2.25rem;
                font-weight: 700;
                color: #111827;
                margin: 0 0 0.5rem 0;
            ">
                Add New Catalog Product
            </h1>
            <p style="color: #6b7280; font-size: 1rem; margin: 0;">
                Fill in product details for {{ $supplier->prefix ? $supplier->prefix . ' ' : '' }}{{ $supplier->name }}
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
            <form action="{{ route('admin.supplier.catalog.store', $supplier->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; @media (max-width: 768px) { grid-template-columns: 1fr; }">

                    <!-- Left column -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        <!-- Product Name -->
                        <div>
                            <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Product Name <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Plastik Sampah"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            @error('name')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Description <small style="color: #6b7280;">(optional)</small>
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Short description, specifications or additional information..."
                                      style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; resize: vertical; color: #111827;">{{ old('description') }}</textarea>
                            @error('description')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit 1 -->
                        <div>
                            <label for="title_1" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Unit 1 (Small) <span style="color: #ef4444;">*</span>
                                <small style="display: block; color: #6b7280; font-size: 0.85rem;">Example: pcs, gram, sheet, ml</small>
                            </label>
                            <input type="text" name="title_1" id="title_1" value="{{ old('title_1') }}" required
                                   placeholder="pcs"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            @error('title_1')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price Unit 1 -->
                        <div>
                            <label for="title_1_price" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Price Unit 1 (Rp) <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="title_1_price_display" id="title_1_price_display" 
                                   value="{{ old('title_1_price') ? 'Rp ' . number_format(old('title_1_price'), 0, ',', '.') : '' }}" 
                                   placeholder="e.g. Rp 12.500"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            <!-- Hidden real number field -->
                            <input type="hidden" name="title_1_price" id="title_1_price" value="{{ old('title_1_price') }}">
                            @error('title_1_price')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Right column -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        <!-- Unit 2 -->
                        <div>
                            <label for="title_2" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Unit 2 (Large) <span style="color: #ef4444;">*</span>
                                <small style="display: block; color: #6b7280; font-size: 0.85rem;">Example: box, kg, pack, carton</small>
                            </label>
                            <input type="text" name="title_2" id="title_2" value="{{ old('title_2') }}" required
                                   placeholder="box"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            @error('title_2')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Value per Unit 2 -->
                        <div>
                            <label for="value_per_title_2" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Units of Unit 1 per Unit 2
                                <small style="color: #6b7280; display: block; font-size: 0.85rem;">Example: 24 (pcs per box)</small>
                            </label>
                            <input type="number" name="value_per_title_2" id="value_per_title_2" value="{{ old('value_per_title_2') }}" min="1" step="1" required
                                   placeholder="24"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            @error('value_per_title_2')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price Unit 2 -->
                        <div>
                            <label for="title_2_price" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Price Unit 2 (Rp) <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="title_2_price_display" id="title_2_price_display" 
                                   value="{{ old('title_2_price') ? 'Rp ' . number_format(old('title_2_price'), 0, ',', '.') : '' }}" 
                                   placeholder="e.g. Rp 280.000"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                            <!-- Hidden real number field -->
                            <input type="hidden" name="title_2_price" id="title_2_price" value="{{ old('title_2_price') }}">
                            @error('title_2_price')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Product Image -->
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                                Product Photo <small style="color: #6b7280;">(optional, max 2MB)</small>
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
                            ">
                                <div style="text-align: center; color: #9ca3af;">
                                    <div style="font-size: 2.5rem;">📦</div>
                                    <p style="font-size: 0.875rem; margin: 0.5rem 0 0;">Preview</p>
                                </div>
                            </div>

                            <input type="file" name="product_image" id="product_image" accept="image/*"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; color: #111827;">
                            @error('product_image')
                                <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Minimum Order & Status -->
                <div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr auto auto; gap: 1.5rem; @media (max-width: 768px) { grid-template-columns: 1fr; }">

                    <div>
                        <label for="minimum_order_title" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                            Minimum Order Based On
                        </label>
                        <select name="minimum_order_title" id="minimum_order_title" required
                                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; background: white; color: #111827;">
                            <option value="title_1" {{ old('minimum_order_title') == 'title_1' ? 'selected' : '' }}>Unit 1 ({{ old('title_1', 'pcs') }})</option>
                            <option value="title_2" {{ old('minimum_order_title') == 'title_2' ? 'selected' : '' }}>Unit 2 ({{ old('title_2', 'box') }})</option>
                        </select>
                        @error('minimum_order_title')
                            <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="minimum_order_qty" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                            Minimum Quantity <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="number" name="minimum_order_qty" id="minimum_order_qty" value="{{ old('minimum_order_qty', 1) }}" min="1" required
                               placeholder="1"
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">
                        @error('minimum_order_qty')
                            <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label style="font-weight: 500; color: #374151; cursor: pointer;">
                            <input type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}
                                   style="width: 1.2rem; height: 1.2rem; accent-color: #10b981;">
                            Available / Active
                        </label>
                        @error('is_available')
                            <p style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                <!-- Buttons -->
                <div style="margin-top: 2.5rem; display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap;">
                    <a href="{{ route('admin.supplier.catalog.index', $supplier->slug) }}"
                       style="
                           padding: 0.75rem 1.5rem;
                           background: #6b7280;
                           color: white;
                           border-radius: 0.5rem;
                           text-decoration: none;
                           font-weight: 500;
                           transition: background 0.2s;
                       "
                       onmouseover="this.style.background='#4b5563'"
                       onmouseout="this.style.background='#6b7280'">
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
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Image preview
        document.getElementById('product_image').addEventListener('change', function(e) {
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
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.innerHTML = `
                    <div style="text-align: center; color: #9ca3af;">
                        <div style="font-size: 2.5rem;">📦</div>
                        <p style="font-size: 0.875rem; margin: 0.5rem 0 0;">Preview</p>
                    </div>
                `;
            }
        });

        // Format price as Rupiah
        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split       = number_string.split(',');
            let sisa        = split[0].length % 3;
            let rupiah      = split[0].substr(0, sisa);
            let ribuan      = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah ? 'Rp ' + rupiah : '';
        }

        function cleanRupiah(str) {
            return str.replace(/Rp\s/g, '').replace(/\./g, '').replace(/,/g, '.');
        }

        // Apply to both price fields
        ['title_1_price_display', 'title_2_price_display'].forEach(id => {
            const displayInput = document.getElementById(id);
            const hiddenInput = document.getElementById(id.replace('_display', ''));

            displayInput.addEventListener('input', function(e) {
                let val = cleanRupiah(this.value);
                this.value = formatRupiah(val);
                hiddenInput.value = val || '';
            });

            // Initial format on load
            if (displayInput.value) {
                displayInput.value = formatRupiah(cleanRupiah(displayInput.value));
            }
        });

        // Dynamic min order label update
        function updateMinOrderLabels() {
            const t1 = document.getElementById('title_1').value.trim() || 'pcs';
            const t2 = document.getElementById('title_2').value.trim() || 'box';
            document.querySelector('#minimum_order_title option[value="title_1"]').textContent = `Unit 1 (${t1})`;
            document.querySelector('#minimum_order_title option[value="title_2"]').textContent = `Unit 2 (${t2})`;
        }

        ['title_1', 'title_2'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateMinOrderLabels);
        });
        updateMinOrderLabels();
    </script>
</x-guest-layout>