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
            <div style="margin-bottom: 1.5rem;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" style="color: black;">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                            Vendor Management
                        </li>
                    </ol>
                </nav>
                <h1 style="
                    font-size: 2.25rem;
                    font-weight: 700;
                    color: #111827;
                    margin: 0;
                ">
                    Vendor Management
                </h1>
                <p style="
                    margin-top: 0.5rem;
                    color: #6b7280;
                    font-size: 1rem;
                ">
                    Manage all registered vendors and their information
                </p>
            </div>

            <!-- Add New Vendor Button -->
            <a href="{{ route('admin.vendor.create') }}"
               style="
                   background: #6366f1;
                   color: white;
                   padding: 0.75rem 1.5rem;
                   border-radius: 0.5rem;
                   text-decoration: none;
                   font-weight: 500;
                   box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
                   transition: all 0.2s;
               "
               onmouseover="this.style.background='#4f46e5'; this.style.transform='translateY(-1px)';"
               onmouseout="this.style.background='#6366f1'; this.style.transform='translateY(0)';">
                + Add New Vendor
            </a>
        </div>

        <!-- Table Container -->
        <div style="
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            border: 1px solid #e5e7eb;
            max-width: 100%;
        ">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="
                                padding: 1rem 1.5rem;
                                text-align: left;
                                font-size: 0.75rem;
                                font-weight: 600;
                                color: #6b7280;
                                text-transform: uppercase;
                                letter-spacing: 0.05em;
                            ">Vendor</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Phone</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Address</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendors as $vendor)
                            <tr style="transition: background-color 0.15s;"
                                onmouseover="this.style.backgroundColor='#f8fafc'"
                                onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 1rem 1.5rem; white-space: nowrap;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        @if($vendor->vendor_image)
                                            <img src="{{ asset('storage/' . $vendor->vendor_image) }}"
                                                 alt="{{ $vendor->name }}"
                                                 style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                                        @else
                                            <div style="
                                                width: 48px;
                                                height: 48px;
                                                border-radius: 50%;
                                                background: #e5e7eb;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-weight: bold;
                                                color: #6b7280;
                                            ">
                                                {{ strtoupper(substr($vendor->name ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight: 600; color: #111827;">
                                                {{ $vendor->prefix ? $vendor->prefix . ' ' : '' }}{{ $vendor->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #4b5563; white-space: nowrap;">
                                    {{ $vendor->phone_number ?? '—' }}
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #4b5563; max-width: 300px;">
                                    {{ Str::limit($vendor->address ?? '—', 60) }}
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('admin.vendor.edit', $vendor->id) }}"
                                       style="
                                           color: #3b82f6;
                                           text-decoration: none;
                                           font-weight: 500;
                                           margin-right: 1.25rem;
                                           transition: color 0.2s;
                                       "
                                       onmouseover="this.style.color='#2563eb'"
                                       onmouseout="this.style.color='#3b82f6'">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.vendor.delete', $vendor->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete {{ addslashes($vendor->prefix . ' ' . $vendor->name) }}? This action cannot be undone.')"
                                                style="
                                                    color: #ef4444;
                                                    background: none;
                                                    border: none;
                                                    font-weight: 500;
                                                    cursor: pointer;
                                                    transition: color 0.2s;
                                                "
                                                onmouseover="this.style.color='#dc2626'"
                                                onmouseout="this.style.color='#ef4444'">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 5rem 1.5rem; text-align: center; color: #6b7280;">
                                    <div style="font-size: 5rem; margin-bottom: 1.25rem; opacity: 0.6;">🏪</div>
                                    <p style="font-size: 1.4rem; font-weight: 600; margin-bottom: 0.75rem;">
                                        No vendors found
                                    </p>
                                    <p style="font-size: 1rem;">
                                        Get started by adding your first vendor
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vendors->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e5e7eb; text-align: center;">
                    {{ $vendors->links() }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>