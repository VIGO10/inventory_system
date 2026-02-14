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

        <!-- Header + Filters + Create Button -->
        <div style="display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem; width: 100%;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="margin: 0 0 0.5rem 0;">
                        <li class="breadcrumb-item" style="color: black;">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                            Transactions
                        </li>
                    </ol>
                </nav>
                <h1 style="font-size: 2.25rem; font-weight: 700; color: #111827; margin: 0;">
                    Transactions
                </h1>
            </div>

            <!-- Search + Filters + Create -->
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start; width: 100%; justify-content: space-between;">
                <!-- Left: Filters Form -->
                <form action="{{ route('admin.transaction.index') }}" method="GET" style="flex: 1; min-width: 500px;">
                    <input type="hidden" name="tab" value="{{ request('tab', 'inbound') }}">

                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <!-- Search Input -->
                        <input type="text" name="search" placeholder="Search reference # or supplier/vendor name..."
                               value="{{ request('search') }}"
                               style="flex: 1; min-width: 260px; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827;">

                        <!-- Month Select -->
                        <select name="month" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 140px;">
                            <option value="">All months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ request('month') === sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>

                        <!-- Year Select (separate field) -->
                        <select name="year" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 120px;">
                            <option value="">All years</option>
                            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++) <!-- Adjust year range as needed, e.g., -5 for past 5 years -->
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>

                        <!-- Status Select -->
                        <select name="status" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 150px;">
                            <option value="">All status</option>
                            <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="paid"      {{ request('status') === 'paid'      ? 'selected' : '' }}>Paid</option>
                            <option value="overdue"   {{ request('status') === 'overdue'   ? 'selected' : '' }}>Overdue</option>
                        </select>

                        <!-- Filter Button -->
                        <button type="submit"
                                style="background: #6366f1; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: background 0.2s;"
                                onmouseover="this.style.background='#4f46e5'"
                                onmouseout="this.style.background='#6366f1'">
                            Filter
                        </button>

                        <!-- Clear Link -->
                        @if(request('search') || request('month') || request('status'))
                            <a href="{{ route('admin.transaction.index', ['tab' => request('tab', 'inbound')]) }}"
                               style="color: #ef4444; font-weight: 500; text-decoration: none; white-space: nowrap; padding: 0.5rem 0;">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Right: Create Button -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    @if(request('tab', 'inbound') === 'inbound')
                        <a href="{{ route('admin.transaction.inbound.create') }}"
                           style="background: #10b981; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 500; box-shadow: 0 4px 6px -1px rgba(16,185,129,0.2); transition: all 0.2s;"
                           onmouseover="this.style.background='#059669'; this.style.transform='translateY(-1px)';"
                           onmouseout="this.style.background='#10b981'; this.style.transform='translateY(0)';">
                            + New Purchase
                        </a>
                    @endif

                    @if(request('tab', 'inbound') === 'outbound')
                        <a href="{{ route('admin.transaction.outbound.create') }}"
                           style="background: #6366f1; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 500; box-shadow: 0 4px 6px -1px rgba(99,102,241,0.2); transition: all 0.2s;"
                           onmouseover="this.style.background='#4f46e5'; this.style.transform='translateY(-1px)';"
                           onmouseout="this.style.background='#6366f1'; this.style.transform='translateY(0)';">
                            + New Sale
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div style="margin-bottom: 2rem; display: flex; border-bottom: 2px solid #e5e7eb;">
            <a href="{{ route('admin.transaction.index', ['tab' => 'inbound']) }}"
               style="padding: 0.75rem 1.5rem; font-weight: {{ request('tab', 'inbound') === 'inbound' ? '600' : '500'}}; color: {{ request('tab', 'inbound') === 'inbound' ? '#10b981' : '#4b5563'}}; text-decoration: none; border-bottom: {{ request('tab', 'inbound') === 'inbound' ? '3px solid #10b981' : 'none'}}; transition: all 0.2s;">
                Purchases ({{ $counts['inbound'] ?? 0 }})
            </a>
            <a href="{{ route('admin.transaction.index', ['tab' => 'outbound']) }}"
               style="padding: 0.75rem 1.5rem; font-weight: {{ request('tab', 'inbound') === 'outbound' ? '600' : '500'}}; color: {{ request('tab', 'inbound') === 'outbound' ? '#6366f1' : '#4b5563'}}; text-decoration: none; border-bottom: {{ request('tab', 'inbound') === 'outbound' ? '3px solid #6366f1' : 'none'}}; transition: all 0.2s;">
                Sales ({{ $counts['outbound'] ?? 0 }})
            </a>
        </div>

        <!-- Cards Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">

            @if(request('tab', 'inbound') === 'inbound')
                @forelse ($inbounds as $item)
                    <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; transition: transform 0.2s, box-shadow 0.2s;"
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.12)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">

                        <!-- Header -->
                        <div style="padding: 1.25rem 1.25rem 0.75rem; background: #f0fdf4; border-bottom: 1px solid #dcfce7;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #065f46; margin: 0;">
                                    {{ $item->reference_number }}
                                </h3>
                                <span style="font-size: 0.875rem; font-weight: 600; color: {{ $item->status_color ?? '#6b7280' }};">
                                    {{ $item->display_status ?? 'Draft' }}
                                </span>
                            </div>
                            <div style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">
                                {{ $item->supplier->prefix ?? '' }} {{ $item->supplier->name ?? 'Unknown Supplier' }}
                            </div>
                        </div>

                        <!-- Body -->
                        <div style="padding: 1.25rem;">
                            <div style="font-size: 0.95rem; color: #4b5563; margin-bottom: 1rem;">
                                <div><strong>Total:</strong> Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}</div>
                                @if(($item->discount ?? 0) > 0)
                                    <div><strong>Discount:</strong> Rp {{ number_format($item->discount, 0, ',', '.') }}</div>
                                @endif
                            </div>

                            <div style="font-size: 0.875rem; color: #6b7280;">
                                <div><strong>Created:</strong> {{ $item->created_date?->format('d M Y H:i') }}</div>
                                @if($item->deadline_payment_date)
                                    <div><strong>Payment due:</strong> {{ $item->deadline_payment_date->format('d M Y') }}</div>
                                @endif
                                @if($item->is_paid)
                                    <div style="color: #10b981;"><strong>Paid on:</strong> {{ $item->paid_date?->format('d M Y H:i') }}</div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                <a href="{{ route('admin.transaction.inbound.detail', $item->reference_number) }}"
                                   style="background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                                    Detail
                                </a>

                                @if(!$item->is_published)
                                    <form action="{{ route('admin.transaction.inbound.publish', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Publish
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.transaction.inbound.delete', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Delete
                                        </button>
                                    </form>
                                @endif

                                @if($item->is_published && !$item->is_completed)
                                    <form action="{{ route('admin.transaction.inbound.complete', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Complete
                                        </button>
                                    </form>
                                @endif

                                @if($item->is_completed && !$item->is_paid)
                                    <form action="{{ route('admin.transaction.inbound.paid', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Mark Paid
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: #6b7280;">
                        <div style="font-size: 6rem; margin-bottom: 1rem; opacity: 0.5;">📦</div>
                        <p style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">
                            No purchase transactions found
                        </p>
                        @if(request('search') || request('month') || request('status'))
                            <p style="margin-top: 1rem;">Try different filters or <a href="{{ route('admin.transaction.index', ['tab' => 'inbound']) }}" style="color: #6366f1;">clear them</a>.</p>
                        @endif
                    </div>
                @endforelse

            @else
                @forelse ($outbounds as $item)
                    <!-- Outbound card - copy your full outbound card content here -->
                    <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; transition: transform 0.2s, box-shadow 0.2s;"
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.12)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">

                        <!-- Header -->
                        <div style="padding: 1.25rem 1.25rem 0.75rem; background: #eff6ff; border-bottom: 1px solid #dbeafe;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e40af; margin: 0;">
                                    {{ $item->reference_number }}
                                </h3>
                                <span style="font-size: 0.875rem; font-weight: 600; color: {{ $item->status_color ?? '#6b7280' }};">
                                    {{ $item->display_status ?? 'Draft' }}
                                </span>
                            </div>
                            <div style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">
                                {{ $item->vendor->prefix ?? '' }} {{ $item->vendor->name ?? 'Unknown Vendor' }}
                            </div>
                        </div>

                        <!-- Body -->
                        <div style="padding: 1.25rem;">
                            <div style="font-size: 0.95rem; color: #4b5563; margin-bottom: 1rem;">
                                <div><strong>Total:</strong> Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}</div>
                                @if(($item->discount ?? 0) > 0)
                                    <div><strong>Discount:</strong> Rp {{ number_format($item->discount, 0, ',', '.') }}</div>
                                @endif
                                <div><strong>Net Profit:</strong> <span style="color: #10b981;">Rp {{ number_format($item->net_profit ?? 0, 0, ',', '.') }}</span></div>
                            </div>

                            <div style="font-size: 0.875rem; color: #6b7280;">
                                <div><strong>Created:</strong> {{ $item->created_date?->format('d M Y H:i') }}</div>
                                @if($item->deadline_payment_date)
                                    <div><strong>Payment due:</strong> {{ $item->deadline_payment_date->format('d M Y') }}</div>
                                @endif
                                @if($item->is_paid)
                                    <div style="color: #10b981;"><strong>Paid on:</strong> {{ $item->paid_date?->format('d M Y H:i') }}</div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                <a href="{{ route('admin.transaction.outbound.detail', $item->reference_number) }}"
                                   style="background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                                    Detail
                                </a>

                                @if(!$item->is_published)
                                    <form action="{{ route('admin.transaction.outbound.publish', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Publish
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.transaction.outbound.delete', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Delete
                                        </button>
                                    </form>
                                @endif

                                @if($item->is_published && !$item->is_completed)
                                    <form action="{{ route('admin.transaction.outbound.complete', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Complete
                                        </button>
                                    </form>
                                @endif

                                @if($item->is_completed && !$item->is_paid)
                                    <form action="{{ route('admin.transaction.outbound.paid', $item->reference_number) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                                            Mark Paid
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: #6b7280;">
                        <div style="font-size: 6rem; margin-bottom: 1rem; opacity: 0.5;">📦</div>
                        <p style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">
                            No sales transactions found
                        </p>
                        @if(request('search') || request('month') || request('status'))
                            <p style="margin-top: 1rem;">Try different filters or <a href="{{ route('admin.transaction.index', ['tab' => 'outbound']) }}" style="color: #6366f1;">clear them</a>.</p>
                        @endif
                    </div>
                @endforelse
            @endif

        </div>

        <!-- Pagination -->
        @if($items->hasPages())
            <div style="margin-top: 2.5rem; text-align: center;">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</x-guest-layout>