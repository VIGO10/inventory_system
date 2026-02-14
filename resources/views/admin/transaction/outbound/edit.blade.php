<x-guest-layout>

    @if (session('fail'))
        <div class="alert alert-danger">{{ session('fail') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="padding: 2rem 1rem; max-width: 1400px; margin: 0 auto;">

        <!-- Header -->
        <div style="display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 3rem;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #111827;">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transaction.index', ['tab' => 'outbound']) }}" style="color: #111827;">Transactions</a></li>
                    <li class="breadcrumb-item" style="color: black;"><a href="{{ route('admin.transaction.outbound.detail', $transaction->reference_number) }}">Sale #{{ $transaction->reference_number }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1;">
                        Edit Sale #{{ $transaction->reference_number }}
                    </li>
                </ol>
            </nav>
            <h1 style="font-size: 2.5rem; font-weight: 700; color: #111827; margin: 0 0 0.75rem 0; text-align: center; width: 100%;">
                Edit Sale #{{ $transaction->reference_number }}
            </h1>
            <p style="color: #6b7280; font-size: 1.2rem; margin: 0 auto; text-align: center; max-width: 700px;">
                Update items, payment deadline, discount, photo, etc.
            </p>
        </div>

        <!-- Form Card -->
        <div style="background: white; border-radius: 1rem; box-shadow: 0 10px 30px -8px rgba(0,0,0,0.12); border: 1px solid #e5e7eb; padding: 3rem 2.5rem; max-width: 900px; margin: 0 auto;">

            <form action="{{ route('admin.transaction.outbound.update', $transaction->reference_number) }}" method="POST" enctype="multipart/form-data" id="sale-form">
                @csrf
                @method('PUT')

                <div style="display: flex; flex-direction: column; gap: 3.5rem; align-items: center;">

                    <!-- Vendor / Customer -->
                    <div style="width: 100%; max-width: 600px;">
                        <label for="vendor_id" style="display: block; margin-bottom: 0.9rem; font-weight: 600; color: #374151; font-size: 1.2rem; text-align: center;">
                            Vendor <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="vendor_id" id="vendor_id" required disabled
                                style="width: 100%; padding: 1rem 1.2rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1.1rem; background: #f3f4f6; color: #111827; text-align: center;">
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $transaction->vendor_id == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->prefix }} {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="vendor_id" value="{{ $transaction->vendor_id }}">
                        @error('vendor_id')
                            <p style="color: #ef4444; font-size: 0.95rem; margin-top: 0.5rem; text-align: center;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Deadline -->
                    <div style="width: 100%; max-width: 600px;">
                        <label for="deadline_payment_date" style="display: block; margin-bottom: 0.9rem; font-weight: 600; color: #374151; font-size: 1.2rem; text-align: center;">
                            Payment Deadline <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" name="deadline_payment_date" id="deadline_payment_date"
                               value="{{ old('deadline_payment_date', $transaction->deadline_payment_date?->format('Y-m-d')) }}" required
                               style="width: 100%; padding: 1rem 1.2rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1.1rem; color: #111827; text-align: center;">
                        @error('deadline_payment_date')
                            <p style="color: #ef4444; font-size: 0.95rem; margin-top: 0.5rem; text-align: center;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo -->
                    <div style="width: 100%; max-width: 600px;">
                        <label style="display: block; margin-bottom: 0.9rem; font-weight: 600; color: #374151; font-size: 1.2rem; text-align: center;">
                            Invoice / Delivery Note Photo <small style="font-weight: normal; color: #6b7280;">(optional, max 4MB)</small>
                        </label>

                        <div id="image-preview" style="width: 280px; height: 280px; border: 2px dashed #d1d5db; border-radius: 1rem; display: flex; align-items: center; justify-content: center; background: #f9fafb; overflow: hidden; margin: 0 auto 1.25rem;">
                            @if ($transaction->transaction_image && Storage::disk('public')->exists($transaction->transaction_image))
                                <img src="{{ asset('storage/' . $transaction->transaction_image) }}" alt="Current invoice"
                                     style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="text-align: center; color: #9ca3af;">
                                    <div style="font-size: 4.5rem;">📄</div>
                                    <p style="font-size: 1.1rem; margin: 0.9rem 0 0;">No image uploaded</p>
                                </div>
                            @endif
                        </div>

                        <input type="file" name="transaction_image" id="transaction_image" accept="image/*"
                               style="width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 0.75rem; text-align: center; color: #111827;">
                        <small style="color: #6b7280; display: block; text-align: center; margin-top: 0.5rem;">
                            Upload new image to replace current one (leave empty to keep existing)
                        </small>
                        @error('transaction_image')
                            <p style="color: #ef4444; font-size: 0.95rem; margin-top: 0.5rem; text-align: center;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Items Section -->
                    <div style="width: 100%; border-top: 2px solid #e5e7eb; padding-top: 3rem;">

                        <h2 style="font-size: 1.9rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0; text-align: center;">
                            Sale Items
                        </h2>

                        <div style="display: flex; justify-content: center; margin-bottom: 2rem;">
                            <button type="button" id="add-item-btn"
                                    style="background: #10b981; color: white; border: none; padding: 1rem 2rem; border-radius: 0.75rem; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.2s;">
                                + Add New Item
                            </button>
                        </div>

                        <div id="items-container" style="display: flex; flex-direction: column; gap: 2rem; max-width: 900px; margin: 0 auto;">
                            @forelse ($transaction->items as $index => $item)
                                <div class="item-row" style="border:1px solid #e5e7eb; border-radius:0.9rem; padding:1.75rem; background:#fafafa; position:relative;">
                                    <button type="button" class="remove-item"
                                            style="position:absolute; top:1.2rem; right:1.2rem; background:#ef4444; color:white; border:none; width:36px; height:36px; border-radius:50%; font-size:1.4rem; cursor:pointer; line-height:1;">
                                        ×
                                    </button>

                                    <div>
                                        <div>
                                            <label style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center; width:100%;">
                                                Product <span style="color:#ef4444;">*</span>
                                            </label>
                                            <select name="items[{{$index}}][catalog_id]" class="catalog-select" required
                                                    data-current-value="{{ old("items.$index.catalog_id", $item->catalog_id ?? '') }}"
                                                    style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; color:#111827;">
                                                <option value="">-- Loading products... --</option>
                                            </select>
                                            <p class="min-order-info" style="margin-top:0.5rem; font-size:0.9rem; color:#6b7280; text-align:center; min-height:1.2em;"></p>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.75rem;">
                                        <div>
                                            <label class="title1-label" style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                                                {{ $item->title_1_name ?? 'Variant 1' }} Qty
                                            </label>
                                            <input type="number" name="items[{{$index}}][title_1_qty]" class="title1-qty" min="0" step="1"
                                                   value="{{ old("items.$index.title_1_qty", $item->title_1_qty ?? 0) }}" required
                                                   style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; text-align:center; color:#111827;">
                                        </div>

                                        <div>
                                            <label class="title2-label" style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                                                {{ $item->title_2_name ?? 'Variant 2' }} Qty
                                            </label>
                                            <input type="number" name="items[{{$index}}][title_2_qty]" class="title2-qty" min="0" step="1"
                                                   value="{{ old("items.$index.title_2_qty", $item->title_2_qty ?? 0) }}"
                                                   style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; text-align:center; color:#111827;">
                                        </div>

                                        <div>
                                            <label style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                                                Total Price
                                            </label>
                                            <div class="row-total"
                                                 style="padding:0.9rem; background:white; border:1px solid #d1d5db; border-radius:0.6rem; font-weight:700; font-size:1.25rem; color:#10b981; text-align:center;">
                                                Rp 0
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item Discount -->
                                    <div style="max-width: 400px; margin: 1.5rem auto 0;">
                                        <label style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                                            Discount (Rp)
                                        </label>
                                        <input type="text" class="discount-display"
                                               value="{{ old("items.$index.discount") ? 'Rp ' . number_format(old("items.$index.discount"), 0, ',', '.') : ($item->discount ? 'Rp ' . number_format($item->discount, 0, ',', '.') : 'Rp 0') }}"
                                               placeholder="Rp 0"
                                               style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; text-align:center; background:white; color:#111827;">
                                        <input type="hidden" name="items[{{$index}}][discount]" class="discount-hidden"
                                               value="{{ old("items.$index.discount", $item->discount ?? 0) }}">
                                    </div>

                                    <!-- Hidden fields -->
                                    <input type="hidden" name="items[{{$index}}][id]" value="{{ $item->id ?? '' }}">
                                    <input type="hidden" class="price-hidden"
                                           data-price1="{{ old("items.$index.price1", $item->title_1_price ?? 0) }}"
                                           data-price2="{{ old("items.$index.price2", $item->title_2_price ?? 0) }}"
                                           value="0">
                                    <input type="hidden" name="items[{{$index}}][type]" class="item-type" value="mixed">
                                </div>
                            @empty
                                <div id="no-items" style="text-align: center; color: #9ca3af; padding: 4rem 1.5rem; border: 2px dashed #d1d5db; border-radius: 1rem; background: #f9fafb; font-size: 1.15rem;">
                                    No items yet — click "Add New Item"
                                </div>
                            @endforelse
                        </div>

                        <!-- Transaction Discount -->
                        <div style="margin: 3rem auto 1.5rem; max-width: 900px;">
                            <label for="discount_display" style="display: block; margin-bottom: 0.9rem; font-weight: 600; color: #374151; font-size: 1.2rem; text-align: center;">
                                Transaction Discount (Rp)
                            </label>
                            <input type="text" id="discount_display" placeholder="Rp 0"
                                   value="{{ old('discount') ? 'Rp ' . number_format(old('discount'), 0, ',', '.') : ($transaction->discount ? 'Rp ' . number_format($transaction->discount, 0, ',', '.') : 'Rp 0') }}"
                                   style="width: 100%; max-width: 500px; padding: 1rem 1.2rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1.1rem; color: #111827; background: white; margin: 0 auto; display: block;">
                            <input type="hidden" name="discount" id="discount_hidden" value="{{ old('discount', $transaction->discount ?? 0) }}">
                            @error('discount')
                                <p style="color: #ef4444; font-size: 0.95rem; margin-top: 0.5rem; text-align: center;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Totals Preview -->
                        <div style="padding: 2rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #e5e7eb; max-width: 900px; margin: 0 auto;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-size: 1.15rem;">
                                <span style="font-weight: 500; color: #374151;">Subtotal</span>
                                <span id="subtotal-preview" style="font-weight: 600; color: #111827;">Rp 0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-size: 1.15rem;">
                                <span style="font-weight: 500; color: #374151;">Discount</span>
                                <span id="discount-preview" style="font-weight: 600; color: #ef4444;">Rp 0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 700; border-top: 1px solid #d1d5db; padding-top: 1.25rem;">
                                <span>Grand Total</span>
                                <span id="total-preview" style="color: #10b981; font-weight: 700;">Rp 0</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 4rem; display: flex; gap: 1.5rem; justify-content: center;">
                    <a href="{{ route('admin.transaction.outbound.detail', $transaction->reference_number) }}"
                       style="padding: 1rem 2.5rem; background: #6b7280; color: white; border-radius: 0.75rem; text-decoration: none; font-weight: 600; font-size: 1.1rem;">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn"
                            style="background: #6366f1; color: white; padding: 1rem 2.75rem; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; font-size: 1.1rem;">
                        Update Sale
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ITEM ROW TEMPLATE -->
    <template id="item-template">
        <div class="item-row" style="border:1px solid #e5e7eb; border-radius:0.9rem; padding:1.75rem; background:#fafafa; position:relative;">
            <button type="button" class="remove-item"
                    style="position:absolute; top:1.2rem; right:1.2rem; background:#ef4444; color:white; border:none; width:36px; height:36px; border-radius:50%; font-size:1.4rem; cursor:pointer; line-height:1;">
                ×
            </button>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.75rem;">
                <div>
                    <label style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                        Product <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="items[][catalog_id]" class="catalog-select" required
                            style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; color:#111827;">
                        <option value="">-- Select product --</option>
                    </select>
                    <p class="min-order-info" style="margin-top:0.5rem; font-size:0.9rem; color:#6b7280; text-align:center; min-height:1.2em;"></p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.75rem;">
                <div>
                    <label class="title1-label" style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                        Variant 1 Qty
                    </label>
                    <input type="number" name="items[][title_1_qty]" class="title1-qty" min="0" step="1" value="0" required
                           style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; text-align:center; color:#111827;">
                </div>

                <div>
                    <label class="title2-label" style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                        Variant 2 Qty
                    </label>
                    <input type="number" name="items[][title_2_qty]" class="title2-qty" min="0" step="1" value="0"
                           style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; text-align:center; color:#111827;">
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                        Total Price
                    </label>
                    <div class="row-total"
                         style="padding:0.9rem; background:white; border:1px solid #d1d5db; border-radius:0.6rem; font-weight:700; font-size:1.25rem; color:#10b981; text-align:center;">
                        Rp 0
                    </div>
                </div>
            </div>

            <div style="max-width: 400px; margin: 1.5rem auto 0;">
                <label style="display:block; margin-bottom:0.7rem; font-weight:600; color:#374151; font-size:1.05rem; text-align:center;">
                    Discount (Rp)
                </label>
                <input type="text" class="discount-display" placeholder="Rp 0"
                       style="width:100%; padding:0.9rem; border:1px solid #d1d5db; border-radius:0.6rem; font-size:1rem; text-align:center; background:white; color:#111827;">
                <input type="hidden" name="items[][discount]" class="discount-hidden" value="0">
            </div>

            <input type="hidden" class="price-hidden" data-price1="0" data-price2="0" value="0">
            <input type="hidden" name="items[][type]" class="item-type" value="mixed">
        </div>
    </template>

    <script>
        // Helpers
        function formatRupiah(angka) {
            let num = angka.toString().replace(/[^,\d]/g, '');
            let split = num.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
            rupiah = split[1] ? rupiah + ',' + split[1] : rupiah;
            return rupiah ? 'Rp ' + rupiah : 'Rp 0';
        }

        function cleanRupiah(str) {
            return parseFloat(str.replace(/Rp\s*/g, '').replace(/\./g, '').replace(/,/g, '.')) || 0;
        }

        // Row Total
        function updateRowTotal(row) {
            const qty1 = parseInt(row.querySelector('.title1-qty')?.value) || 0;
            const qty2 = parseInt(row.querySelector('.title2-qty')?.value) || 0;
            const price1 = parseFloat(row.querySelector('.price-hidden')?.dataset.price1) || 0;
            const price2 = parseFloat(row.querySelector('.price-hidden')?.dataset.price2) || 0;

            const subtotal = (qty1 * price1) + (qty2 * price2);
            const discount = parseFloat(row.querySelector('.discount-hidden')?.value) || 0;
            const finalTotal = Math.max(0, subtotal - discount);

            row.querySelector('.row-total').textContent = formatRupiah(finalTotal);
            updateGrandTotals();
        }

        // Grand Totals
        function updateGrandTotals() {
            let subtotal = 0;
            const transDisc = parseFloat(document.getElementById('discount_hidden')?.value || 0);

            document.querySelectorAll('.item-row').forEach(row => {
                const totalText = row.querySelector('.row-total')?.textContent || 'Rp 0';
                subtotal += cleanRupiah(totalText);
            });

            const grand = Math.max(0, subtotal - transDisc);

            document.getElementById('subtotal-preview').textContent = formatRupiah(subtotal);
            document.getElementById('discount-preview').textContent = formatRupiah(transDisc);
            document.getElementById('total-preview').textContent    = formatRupiah(grand);
        }

        // Attach input/change listeners (qty + discount + catalog)
        function attachRowListeners(row) {
            row.querySelectorAll('.title1-qty, .title2-qty').forEach(input => {
                input.addEventListener('input', () => updateRowTotal(row));
            });

            const discDisplay = row.querySelector('.discount-display');
            if (discDisplay) {
                discDisplay.addEventListener('input', () => {
                    let val = cleanRupiah(discDisplay.value);
                    discDisplay.value = formatRupiah(val);
                    row.querySelector('.discount-hidden').value = val;
                    updateRowTotal(row);
                });
            }

            const catalogSelect = row.querySelector('.catalog-select');
            if (catalogSelect) {
                catalogSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    if (!opt.value) return;

                    row.querySelector('.price-hidden').dataset.price1 = parseFloat(opt.dataset.price1) || 0;
                    row.querySelector('.price-hidden').dataset.price2 = parseFloat(opt.dataset.price2) || 0;

                    row.querySelector('.title1-label').textContent = (opt.dataset.title1 || 'Variant 1') + ' Qty';
                    row.querySelector('.title2-label').textContent = (opt.dataset.title2 || 'Variant 2') + ' Qty';

                    updateRowTotal(row);
                });
            }
        }

        // Attach REMOVE button listener
        function attachRemoveListener(row) {
            const btn = row.querySelector('.remove-item');
            if (!btn) return;

            btn.addEventListener('click', () => {
                row.remove();
                const container = document.getElementById('items-container');
                if (container.querySelectorAll('.item-row').length === 0) {
                    document.getElementById('no-items')?.style.setProperty('display', 'block');
                }
                updateGrandTotals();
            });
        }

        // Load catalog products (adjust URL to your real endpoint)
        async function loadCatalogProducts(row) {
            const select = row.querySelector('.catalog-select');
            if (!select) return;

            const currentVal = select.dataset.currentValue || select.value || '';

            try {
                // ← Change this URL to match your actual route
                const res = await fetch(`/admin/catalog/getAvailableCatalog`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!res.ok) throw new Error('Failed');

                const data = await res.json();

                select.innerHTML = '<option value="">-- Select product --</option>';

                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.name || item.title || 'Product';
                    opt.dataset.price1  = item.title_1_price  || 0;
                    opt.dataset.price2  = item.title_2_price  || 0;
                    opt.dataset.title1  = item.title_1_name   || 'Variant 1';
                    opt.dataset.title2  = item.title_2_name   || 'Variant 2';

                    if (String(item.id) === String(currentVal)) opt.selected = true;
                    select.appendChild(opt);
                });

                if (select.value) select.dispatchEvent(new Event('change'));
            } catch (err) {
                console.error(err);
                select.innerHTML = '<option value="">Error loading products...</option>';
            }
        }

        // ────────────────────────────────────────────────
        // Initialization
        // ────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.item-row').forEach(row => {
                attachRemoveListener(row);      // ← crucial fix
                attachRowListeners(row);

                // Format existing discount display
                const discDisplay = row.querySelector('.discount-display');
                if (discDisplay && discDisplay.value.trim() !== 'Rp 0') {
                    discDisplay.value = formatRupiah(cleanRupiah(discDisplay.value));
                }

                updateRowTotal(row);
            });

            // Load catalogs for existing rows
            document.querySelectorAll('.item-row').forEach(loadCatalogProducts);

            // Transaction-level discount handler
            const transDiscDisplay = document.getElementById('discount_display');
            if (transDiscDisplay) {
                if (transDiscDisplay.value.trim() !== 'Rp 0') {
                    transDiscDisplay.value = formatRupiah(cleanRupiah(transDiscDisplay.value));
                }
                transDiscDisplay.addEventListener('input', function() {
                    let val = cleanRupiah(this.value);
                    this.value = formatRupiah(val);
                    document.getElementById('discount_hidden').value = val;
                    updateGrandTotals();
                });
            }

            updateGrandTotals();
        });

        // ────────────────────────────────────────────────
        // Add New Item
        // ────────────────────────────────────────────────
        let itemIndex = {{ $transaction->items->count() ?? 0 }};

        document.getElementById('add-item-btn')?.addEventListener('click', () => {
            document.getElementById('no-items')?.style.setProperty('display', 'none');

            const container = document.getElementById('items-container');
            const template = document.getElementById('item-template').content.cloneNode(true);

            template.querySelectorAll('[name^="items[]"]').forEach(el => {
                const name = el.getAttribute('name').replace('items[]', `items[${itemIndex}]`);
                el.setAttribute('name', name);
            });

            const newRow = template.firstElementChild;
            container.appendChild(newRow);

            loadCatalogProducts(newRow);
            attachRemoveListener(newRow);     // ← crucial for new rows
            attachRowListeners(newRow);
            updateRowTotal(newRow);

            itemIndex++;
        });

        // Image preview
        document.getElementById('transaction_image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            if (this.files?.[0]) {
                preview.innerHTML = '';
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>

</x-guest-layout>