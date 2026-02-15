<x-guest-layout>

    @if (Session::get('fail'))
        <div class="alert alert-danger">{{ Session::get('fail') }}</div>
    @endif
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    <div style="padding: 2rem 1rem; max-width: 1400px; margin: 0 auto;">

        <!-- Header + Filters + Create Button -->
        <div style="display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem; width: 100%;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="margin: 0 0 0.5rem 0;">
                        <li class="breadcrumb-item" style="color: black;">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                            Other Costs
                        </li>
                    </ol>
                </nav>
                <h1 style="font-size: 2.25rem; font-weight: 700; color: #111827; margin: 0;">
                    Other Costs
                </h1>
                <p style="color: #6b7280; margin: 0.25rem 0 0 0;">Manage additional / miscellaneous costs here.</p>
            </div>

            <!-- Filters + Create -->
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start; width: 100%; justify-content: space-between;">
                <a href="#" onclick="openAddCostModal(); return false;"
                   style="background: #6366f1; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 500; white-space: nowrap; box-shadow: 0 4px 6px -1px rgba(99,102,241,0.2); transition: all 0.2s;"
                   onmouseover="this.style.background='#4f46e5'; this.style.transform='translateY(-1px)';"
                   onmouseout="this.style.background='#6366f1'; this.style.transform='translateY(0)';">
                    + Add New Cost
                </a>
                
                <!-- Left: Filters Form -->
                <form action="{{ route('admin.other-cost.index') }}" method="GET" style="flex: 1; min-width: 500px;">
                    <input type="hidden" name="tab" value="{{ $tab ?? 'in' }}">

                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <!-- Search Input -->
                        <input type="text" name="search" placeholder="Search by cost name..."
                               value="{{ $search ?? '' }}"
                               style="flex: 1; min-width: 260px; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">

                        <!-- Month Select -->
                        <select name="month" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 140px;">
                            <option value="">All months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>

                        <!-- Year Select -->
                        <select name="year" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 120px;">
                            <option value="">All years</option>
                            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>

                        <!-- Filter Button -->
                        <button type="submit"
                                style="background: #6366f1; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: background 0.2s;"
                                onmouseover="this.style.background='#4f46e5'"
                                onmouseout="this.style.background='#6366f1'">
                            Filter
                        </button>

                        <!-- Clear Link -->
                        @if($search || $month || $year)
                            <a href="{{ route('admin.other-cost.index', ['tab' => $tab ?? 'in']) }}"
                               style="color: #ef4444; font-weight: 500; text-decoration: none; white-space: nowrap; padding: 0.5rem 0; align-self: center;">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

            </div>
        </div>

        <!-- Tabs: In / Out -->
        <div style="margin-bottom: 2rem; display: flex; border-bottom: 2px solid #e5e7eb;">
            <a href="{{ route('admin.other-cost.index', ['tab' => 'in']) }}"
               style="padding: 0.75rem 1.5rem; font-weight: {{ ($tab ?? 'in') === 'in' ? '600' : '500'}}; color: {{ ($tab ?? 'in') === 'in' ? '#10b981' : '#4b5563'}}; text-decoration: none; border-bottom: {{ ($tab ?? 'in') === 'in' ? '3px solid #10b981' : 'none'}}; transition: all 0.2s;">
                In ({{ $counts['in'] ?? 0 }})
            </a>
            <a href="{{ route('admin.other-cost.index', ['tab' => 'out']) }}"
               style="padding: 0.75rem 1.5rem; font-weight: {{ ($tab ?? 'in') === 'out' ? '600' : '500'}}; color: {{ ($tab ?? 'in') === 'out' ? '#ef4444' : '#4b5563'}}; text-decoration: none; border-bottom: {{ ($tab ?? 'in') === 'out' ? '3px solid #ef4444' : 'none'}}; transition: all 0.2s;">
                Out ({{ $counts['out'] ?? 0 }})
            </a>
        </div>

        <!-- Table -->
        <div style="overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151;">Name</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600; color: #374151;">Amount (Rp)</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151;">Type</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151;">Date</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($costs as $item)
                        <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.15s;">
                            <td style="padding: 1rem; color: #111827;">{{ $item->name }}</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 500; color: #111827;">
                                {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.875rem; font-weight: 500;
                                             background: {{ $item->type === 'in' ? '#d1fae5' : '#fee2e2' }};
                                             color: {{ $item->type === 'in' ? '#065f46' : '#991b1b' }};">
                                    {{ $item->type === 'in' ? 'IN' : 'OUT' }}
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #4b5563; font-size: 0.95rem;">
                                {{ $item->date->format('d M Y') }}
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                                    <form action="{{ route('admin.other-cost.delete', $item->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Delete {{ addslashes($item->name) }}? This action cannot be undone.')"
                                                style="
                                                    background: #ef4444;
                                                    color: white;
                                                    padding: 0.5rem 1rem;
                                                    border: none;
                                                    border-radius: 0.5rem;
                                                    font-size: 0.875rem;
                                                    font-weight: 500;
                                                    cursor: pointer;
                                                ">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 4rem 1rem; text-align: center; color: #6b7280;">
                                <div style="font-size: 4.5rem; margin-bottom: 1rem; opacity: 0.5;">💸</div>
                                <p style="font-size: 1.3rem; font-weight: 600; margin: 0 0 0.75rem 0;">
                                    No costs found
                                </p>
                                <p>@if($search || $month || $year) Try adjusting filters or clear them @else Add new cost using button above @endif</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($costs->hasPages())
            <div style="margin-top: 2.5rem; text-align: center;">
                {{ $costs->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

    <!-- ──────────────────────────────────────────────── -->
    <!--               ADD COST MODAL                     -->
    <!-- ──────────────────────────────────────────────── -->

    <div id="addCostModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center;">
        <div style="background:white; border-radius:1rem; width:100%; max-width:480px; margin:1rem; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); overflow:hidden;">
            
            <div style="padding:1.5rem 2rem; background:#f8fafc; border-bottom:1px solid #e5e7eb;">
                <h3 style="margin:0; font-size:1.5rem; font-weight:600; color:#111827;">Add New Cost</h3>
            </div>

            <form action="{{ route('admin.other-cost.store') }}" method="POST" style="padding:2rem;">
                @csrf

                <!-- Name -->
                <div style="margin-bottom:1.5rem;">
                    <label for="name" style="display:block; margin-bottom:0.5rem; font-weight:500; color:#111827;">
                        Cost Name <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                        placeholder="e.g. Biaya Transportasi" 
                        style="width:100%; padding:0.75rem 1rem; border:1px solid #d1d5db; border-radius:0.5rem; font-size:1rem; color:#111827; background:white;">
                    @error('name')
                        <p style="color:#ef4444; font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div style="margin-bottom:1.5rem;">
                    <label for="price_display" style="display:block; margin-bottom:0.5rem; font-weight:500; color:#111827;">
                        Amount (Rp) <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="price_display" placeholder="e.g. Rp 1.250.000"
                        style="width:100%; padding:0.75rem 1rem; border:1px solid #d1d5db; border-radius:0.5rem; font-size:1rem; color:#111827; background:white;">
                    <input type="hidden" name="price" id="price_hidden" required>
                    @error('price')
                        <p style="color:#ef4444; font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date (NEW) -->
                <div style="margin-bottom:1.5rem;">
                    <label for="date" style="display:block; margin-bottom:0.5rem; font-weight:500; color:#111827;">
                        Date <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="date" name="date" id="date" required
                        style="width:100%; padding:0.75rem 1rem; border:1px solid #d1d5db; border-radius:0.5rem; font-size:1rem; color:#111827; background:white;">
                    @error('date')
                        <p style="color:#ef4444; font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div style="margin-bottom:2rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500; color:#111827;">
                        Type <span style="color:#ef4444;">*</span>
                    </label>
                    <div style="display:flex; gap:2rem; color:#111827;">
                        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                            <input type="radio" name="type" value="in" checked required> In (Income)
                        </label>
                        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                            <input type="radio" name="type" value="out"> Out (Expense)
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display:flex; gap:1rem; justify-content:flex-end;">
                    <button type="button" onclick="closeAddCostModal()"
                            style="padding:0.75rem 1.5rem; background:#6b7280; color:white; border:none; border-radius:0.5rem; cursor:pointer; font-weight:500;">
                        Cancel
                    </button>
                    <button type="submit"
                            style="padding:0.75rem 1.5rem; background:#6366f1; color:white; border:none; border-radius:0.5rem; cursor:pointer; font-weight:500;">
                        Save Cost
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for modal + Rupiah format -->
    <script>
        function openAddCostModal() {
            document.getElementById('addCostModal').style.display = 'flex';
            
            // Reset form fields
            document.getElementById('name').value = '';
            document.getElementById('price_display').value = '';
            document.getElementById('price_hidden').value = '';
            document.getElementById('date').value = '';           // ← NEW: reset date
            document.querySelector('input[name="type"][value="in"]').checked = true;
        }

        function closeAddCostModal() {
            document.getElementById('addCostModal').style.display = 'none';
        }

        // Rupiah formatting
        const priceDisplay = document.getElementById('price_display');
        const priceHidden  = document.getElementById('price_hidden');

        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
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

        priceDisplay.addEventListener('input', function() {
            let val = cleanRupiah(this.value);
            this.value = formatRupiah(val);
            priceHidden.value = val || '';
        });
    </script>

</x-guest-layout>