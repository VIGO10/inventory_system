<x-guest-layout>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('fail'))
        <div class="alert alert-danger">{{ session('fail') }}</div>
    @endif

    <div style="padding: 2rem 1rem; max-width: 1400px; margin: 0 auto;">

        <!-- Header -->
        <div style="display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 3rem;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="color: #111827;">
                        <a href="{{ route('admin.dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item" style="color: #111827;">
                        <a href="{{ route('admin.transaction.index', ['tab' => 'inbound']) }}">Transactions</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                        Purchase Detail #{{ $transaction->reference_number }}
                    </li>
                </ol>
            </nav>
            <h1 style="font-size: 2.5rem; font-weight: 700; color: #111827; margin: 0 0 0.75rem 0; text-align: center; width: 100%;">
                Purchase Detail
            </h1>
            <p style="color: #1f2937; font-size: 1.2rem; margin: 0 auto; text-align: center; max-width: 700px;">
                Reference: <strong style="color: #111827;">{{ $transaction->reference_number }}</strong>
            </p>
        </div>

        <!-- Main Card -->
        <div style="background: white; border-radius: 1rem; box-shadow: 0 10px 30px -8px rgba(0,0,0,0.12); border: 1px solid #e5e7eb; padding: 3rem 2.5rem; max-width: 1100px; margin: 0 auto;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">

                <!-- Left Column - Transaction Info -->
                <div>
                    <h3 style="font-size: 1.6rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">Transaction Information</h3>

                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div>
                            <label style="font-weight: 600; color: #111827; display: block; margin-bottom: 0.4rem;">Supplier</label>
                            <div style="font-size: 1.1rem; font-weight: 500; color: #111827;">
                                {{ $transaction->supplier->prefix ?? '—' }} {{ $transaction->supplier->name ?? '—' }}
                            </div>
                        </div>

                        <div>
                            <label style="font-weight: 600; color: #111827; display: block; margin-bottom: 0.4rem;">Payment Deadline</label>
                            <div style="font-size: 1.1rem; color: #111827;">
                                {{ $transaction->deadline_payment_date ? $transaction->deadline_payment_date->format('d M Y') : '—' }}
                            </div>
                        </div>

                        <div>
                            <label style="font-weight: 600; color: #111827; display: block; margin-bottom: 0.4rem;">Created At</label>
                            <div style="font-size: 1.1rem; color: #111827;">
                                {{ $transaction->created_date ? $transaction->created_date->format('d M Y H:i') : '—' }}
                            </div>
                        </div>

                        <div>
                            <label style="font-weight: 600; color: #111827; display: block; margin-bottom: 0.4rem;">Status</label>
                            <span style="font-size: 0.875rem; font-weight: 600; color: {{ $transaction->status_color }};">
                                {{ $transaction->display_status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Invoice Photo & Totals -->
                <div>
                    <h3 style="font-size: 1.6rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">Invoice / Delivery Note</h3>

                    @if ($transaction->transaction_image)
                        <div style="border: 2px solid #e5e7eb; border-radius: 1rem; overflow: hidden; background: #f9fafb; margin-bottom: 1.5rem;">
                            <img src="{{ Storage::url($transaction->transaction_image) }}" alt="Invoice / Delivery Note"
                                 style="width: 100%; height: auto; display: block; object-fit: contain;">
                        </div>
                    @else
                        <div style="text-align: center; color: #1f2937; font-weight: 500; padding: 4rem 1rem; border: 2px dashed #d1d5db; border-radius: 1rem;">
                            No invoice / delivery note uploaded
                        </div>
                    @endif

                    <!-- Totals Summary -->
                    <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #e5e7eb;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem; color: #111827;">
                            <span style="font-weight: 600;">Subtotal</span>
                            <span style="font-weight: 700;">Rp {{ number_format($transaction->total_price + $transaction->discount, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem; color: #111827;">
                            <span style="font-weight: 600;">Discount</span>
                            <span style="font-weight: 700; color: #dc2626;">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 800; border-top: 1px solid #d1d5db; padding-top: 1rem; color: #111827;">
                            <span>Grand Total</span>
                            <span style="color: #065f46; font-weight: 800;">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div style="margin-top: 3rem;">
                <h3 style="font-size: 1.9rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem; text-align: center;">
                    Purchased Items
                </h3>

                @if ($transaction->items->isNotEmpty())
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 1rem;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 1rem; text-align: left; font-weight: 700; color: #111827;">Product (Supplier Catalog)</th>
                                    <th style="padding: 1rem; text-align: center; font-weight: 700; color: #111827;">Catalog / Stock</th>
                                    <th style="padding: 1rem; text-align: center; font-weight: 700; color: #111827;">
                                        Title 1 Qty
                                    </th>
                                    <th style="padding: 1rem; text-align: center; font-weight: 700; color: #111827;">
                                        Title 2 Qty
                                    </th>
                                    <th style="padding: 1rem; text-align: center; font-weight: 700; color: #111827;">Discount (Rp)</th>
                                    <th style="padding: 1rem; text-align: right; font-weight: 700; color: #111827;">Price (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->items as $item)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 1rem; color: #111827; font-weight: 500;">
                                            {{ $item->catalogSupplier->name ?? $item->catalogSupplier->title ?? '—' }}
                                        </td>
                                        <td style="padding: 1rem; text-align: center; color: #111827;">
                                            {{ $item->catalog->name ?? '—' }}
                                        </td>
                                        <td style="padding: 1rem; text-align: center; color: #111827;">
                                            {{ number_format($item->title_1_qty, 0, ',', '.') }} {{ $item->catalogSupplier->title_1 }}
                                        </td>
                                        <td style="padding: 1rem; text-align: center; color: #111827;">
                                            {{ number_format($item->title_2_qty, 0, ',', '.') }} {{ $item->catalogSupplier->title_2 }}
                                        </td>
                                        <td style="padding: 1rem; text-align: center; color: #111827;">
                                            {{ number_format($item->discount, 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 1rem; text-align: right; font-weight: 700; color: #065f46;">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; color: #1f2937; font-weight: 500; padding: 4rem 1.5rem; border: 2px dashed #d1d5db; border-radius: 1rem;">
                        No items found in this transaction
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 4rem; display: flex; gap: 1.5rem; justify-content: center;">
                <a href="{{ route('admin.transaction.index') }}"
                   style="padding: 1rem 2.5rem; background: #4b5563; color: white; border-radius: 0.75rem; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: background 0.2s;">
                    Back to List
                </a>

                @if (!$transaction->is_published)
                    <a href="{{ route('admin.transaction.inbound.edit', $transaction->reference_number) }}"
                       style="padding: 1rem 2.5rem; background: #6366f1; color: white; border-radius: 0.75rem; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: background 0.2s;">
                        Edit Purchase
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>