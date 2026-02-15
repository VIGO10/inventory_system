<x-guest-layout>
    <div style="min-height: 100vh; background: #f9fafb;">

        <!-- Header -->
        <header style="background: white; border-bottom: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 10; backdrop-filter: blur(8px); background: rgba(255,255,255,0.95);">
            <div style="max-width: 1400px; margin: 0 auto; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <h1 style="font-size: 1.75rem; font-weight: 700; color: #111827; margin: 0;">Dashboard</h1>
                <div style="font-size: 0.875rem; color: #4b5563; font-weight: 500;">
                    {{ now()->setTimezone('Asia/Makassar')->format('d F Y • H:i') }} WITA
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main style="max-width: 1400px; margin: 0 auto; padding: 2rem 1.5rem;">

            <!-- Filter -->
            <div style="margin-bottom: 2rem;">
                <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <select name="month" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 140px; background: white; outline: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <option value="">All months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ (request('month') ?: now()->month) == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="year" style="padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; color: #111827; min-width: 120px; background: white; outline: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <option value="">All years</option>
                        @for ($y = now()->year - 5; $y <= now()->year; $y++)
                            <option value="{{ $y }}" {{ (request('year') ?: now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <button type="submit"
                            style="background: #6366f1; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 1px 3px rgba(99,102,241,0.2);"
                            onmouseover="this.style.background='#4f46e5'"
                            onmouseout="this.style.background='#6366f1'">
                        Filter
                    </button>

                    @if(request('month') || request('year'))
                        <a href="{{ route('admin.dashboard') }}"
                           style="color: #ef4444; font-weight: 500; text-decoration: none; white-space: nowrap; padding: 0.5rem 0; align-self: center; transition: color 0.2s;"
                           onmouseover="this.style.color='#dc2626'"
                           onmouseout="this.style.color='#ef4444'">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Stats Cards - Normal size + safe for very large numbers -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <!-- Total Users -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Total Users</p>
                        <p style="font-size: 2.5rem; font-weight: 700; color: #111827; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            {{ number_format($totalUsers ?? 0) }}
                        </p>
                    </div>
                    <div style="background: #dbeafe; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Net Profit (Paid) -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Net Profit (Paid)</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #15803d; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($netProfitPaid ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #dcfce7; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #16a34a;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>

                <!-- Net Profit (Unpaid) -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Net Profit (Unpaid)</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #f59e0b; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($netProfitUnpaid ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #fef3c7; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #d97706;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Purchase Price (Paid) -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Purchase Price (Paid)</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #b91c1c; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($purchasePricePaid ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #fee2e2; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #dc2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>

                <!-- Purchase Price (Unpaid) -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Purchase Price (Unpaid)</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #f59e0b; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($purchasePriceUnpaid ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #fef3c7; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #d97706;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Omset -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Omset</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #4338ca; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($omset ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #e0e7ff; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #6366f1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08 .402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Other Cost In -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Other Cost In</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #10b981; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($otherCostIn ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #d1fae5; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #065f46;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Other Cost Out -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.08); padding: 1.5rem; min-height: 130px; display: flex; align-items: center; overflow: hidden;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Other Cost Out</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #ef4444; margin: 0; line-height: 1.1; word-break: break-all; overflow-wrap: anywhere;">
                            Rp {{ number_format($otherCostOut ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #fee2e2; padding: 1.25rem; border-radius: 9999px; flex-shrink: 0; margin-left: 1rem;">
                        <svg style="width: 2.5rem; height: 2.5rem; color: #b91c1c;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inbound Transactions (Purchases) -->
            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Inbound Transactions (Purchases)</h2>
                <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 600; color: #374151; font-size: 1rem;">Reference</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 600; color: #374151; font-size: 1rem;">Supplier</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: right; font-weight: 600; color: #374151; font-size: 1rem;">Total</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 600; color: #374151; font-size: 1rem;">Status</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 600; color: #374151; font-size: 1rem;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inbounds as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;"
                                    onmouseover="this.style.background='#f8fafc'"
                                    onmouseout="this.style.background='white'">
                                    <td style="padding: 1.25rem 1.5rem; color: #111827; font-weight: 500;">{{ $item->reference_number }}</td>
                                    <td style="padding: 1.25rem 1.5rem; color: #111827; font-size: 1rem;">
                                        {{ $item->supplier->prefix ?? '' }} {{ $item->supplier->name ?? 'Unknown Supplier' }}
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: right; color: #111827; font-weight: 600;">
                                        Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                        <span style="padding: 0.45rem 1rem; border-radius: 999px; font-size: 0.9rem; font-weight: 600; 
                                                     background: {{ $item->status_color ?? '#e5e7eb' }};
                                                     color: {{ $item->status_color ? '#ffffff' : '#4b5563' }};">
                                            {{ $item->display_status ?? 'Draft' }}
                                        </span>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center; color: #4b5563; font-size: 0.95rem;">
                                        {{ $item->created_date ? $item->created_date->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 4rem 1rem; text-align: center; color: #6b7280; font-size: 1.25rem;">
                                        No inbound transactions found for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($inbounds->hasPages())
                        <div style="padding: 1.5rem; text-align: center; background: #f8fafc; border-top: 1px solid #e5e7eb;">
                            {{ $inbounds->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Outbound Transactions (Sales) -->
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Outbound Transactions (Sales)</h2>
                <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 600; color: #374151; font-size: 1rem;">Reference</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 600; color: #374151; font-size: 1rem;">Vendor</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: right; font-weight: 600; color: #374151; font-size: 1rem;">Total</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: right; font-weight: 600; color: #374151; font-size: 1rem;">Net Profit</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 600; color: #374151; font-size: 1rem;">Status</th>
                                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 600; color: #374151; font-size: 1rem;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($outbounds as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;"
                                    onmouseover="this.style.background='#f8fafc'"
                                    onmouseout="this.style.background='white'">
                                    <td style="padding: 1.25rem 1.5rem; color: #111827; font-weight: 500;">{{ $item->reference_number }}</td>
                                    <td style="padding: 1.25rem 1.5rem; color: #111827; font-size: 1rem;">
                                        {{ $item->vendor->prefix ?? '' }} {{ $item->vendor->name ?? 'Unknown Vendor' }}
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: right; color: #111827; font-weight: 600;">
                                        Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: right; color: #16a34a; font-weight: 600;">
                                        Rp {{ number_format($item->net_profit ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                        <span style="padding: 0.45rem 1rem; border-radius: 999px; font-size: 0.9rem; font-weight: 600; 
                                                     background: {{ $item->status_color ?? '#e5e7eb' }};
                                                     color: {{ $item->status_color ? '#ffffff' : '#4b5563' }};">
                                            {{ $item->display_status ?? 'Draft' }}
                                        </span>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center; color: #4b5563; font-size: 0.95rem;">
                                        {{ $item->created_date ? $item->created_date->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 4rem 1rem; text-align: center; color: #6b7280; font-size: 1.25rem;">
                                        No outbound transactions found for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($outbounds->hasPages())
                        <div style="padding: 1.5rem; text-align: center; background: #f8fafc; border-top: 1px solid #e5e7eb;">
                            {{ $outbounds->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </main>
    </div>
</x-guest-layout>