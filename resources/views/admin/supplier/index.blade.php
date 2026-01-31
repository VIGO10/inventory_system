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
                            Supplier Management
                        </li>
                    </ol>
                </nav>
                <h1 style="
                    font-size: 2.25rem;
                    font-weight: 700;
                    color: #111827;
                    margin: 0;
                ">
                    Supplier Management
                </h1>
                <p style="
                    margin-top: 0.5rem;
                    color: #6b7280;
                    font-size: 1rem;
                ">
                    Manage all registered suppliers and their information
                </p>
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; width: 100%;">
                <!-- Add New Supplier Button -->
                <a href="{{ route('admin.supplier.create') }}"
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
                    + Add New Supplier
                </a>

                <!-- Search Form -->
                <form action="{{ route('admin.supplier.index') }}" method="GET" style="flex: 1; min-width: 300px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               placeholder="Search by name or prefix..."
                               style="
                                   flex: 1;
                                   padding: 0.75rem 1rem;
                                   border: 1px solid #d1d5db;
                                   border-radius: 0.5rem;
                                   font-size: 1rem;
                                   color: #111827;
                               ">

                        <button type="submit"
                                style="
                                    background: #6366f1;
                                    color: white;
                                    padding: 0.75rem 1.25rem;
                                    border: none;
                                    border-radius: 0.5rem;
                                    font-weight: 500;
                                    cursor: pointer;
                                    transition: background 0.2s;
                                "
                                onmouseover="this.style.background='#4f46e5'"
                                onmouseout="this.style.background='#6366f1'">
                            Search
                        </button>

                        @if($search)
                            <a href="{{ route('admin.supplier.index') }}"
                               style="
                                   color: #ef4444;
                                   font-weight: 500;
                                   text-decoration: none;
                                   white-space: nowrap;
                               ">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
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
                            ">Supplier</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Phone</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Address</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr style="transition: background-color 0.15s;"
                                onmouseover="this.style.backgroundColor='#f8fafc'"
                                onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 1rem 1.5rem; white-space: nowrap;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        @if($supplier->supplier_image)
                                            <img src="{{ asset('storage/' . $supplier->supplier_image) }}"
                                                 alt="{{ $supplier->name }}"
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
                                                {{ strtoupper(substr($supplier->name ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a style="font-weight: 600; color: #111827;" href="{{ route('admin.supplier.catalog.index', $supplier->slug) }}">{{ $supplier->prefix ? $supplier->prefix . ' ' : '' }}{{ $supplier->name }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #4b5563; white-space: nowrap;">
                                    {{ $supplier->phone_number ?? '—' }}
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #4b5563; max-width: 300px;">
                                    {{ Str::limit($supplier->address ?? '—', 60) }}
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('admin.supplier.edit', $supplier->slug) }}"
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

                                    <form action="{{ route('admin.supplier.delete', $supplier->slug) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete {{ addslashes($supplier->prefix . ' ' . $supplier->name) }}? This action cannot be undone.')"
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
                                <td colspan="4" style="padding: 5rem 1.5rem; text-align: center; color: #6b7280;">
                                    <div style="font-size: 5rem; margin-bottom: 1.25rem; opacity: 0.6;">🏪</div>
                                    <p style="font-size: 1.4rem; font-weight: 600; margin-bottom: 0.75rem;">
                                        No suppliers found
                                    </p>
                                    <p style="font-size: 1rem;">
                                        @if($search)
                                            Try a different search term or
                                        @endif
                                        Get started by adding your first supplier
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suppliers->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e5e7eb; text-align: center;">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>