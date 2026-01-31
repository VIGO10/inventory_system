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
            <div style="margin-bottom: 1.5rem; width: 100%;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" style="color: black;">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">
                            Catalog Management
                        </li>
                    </ol>
                </nav>
                <h1 style="
                    font-size: 2.25rem;
                    font-weight: 700;
                    color: #111827;
                    margin: 0;
                ">
                    Catalog
                </h1>
                <p style="
                    margin-top: 0.5rem;
                    color: #6b7280;
                    font-size: 1rem;
                ">
                    Manage your catalog products here.
                </p>
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; width: 100%;">
                <!-- Add New Product Button -->
                <a href="{{ route('admin.catalog.create') }}"
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
                    + Add New Product
                </a>

                <!-- Search Form -->
                <form action="{{ route('admin.catalog.index') }}" method="GET" style="flex: 1; min-width: 300px;">
                    <input type="hidden" name="tab" value="{{ request('tab', 'available') }}">

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               placeholder="Search by product name or description..."
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
                            <a href="{{ route('admin.catalog.index') }}?tab={{ request('tab', 'available') }}"
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

        <!-- Tabs -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; border-bottom: 2px solid #e5e7eb;">
                <a href="{{ route('admin.catalog.index', ['tab' => 'available']) }}"
                   style="
                       padding: 0.75rem 1.5rem;
                       font-weight: {{ request('tab', 'available') === 'available' ? '600' : '500'}};
                       color: {{ request('tab', 'available') === 'available' ? '#6366f1' : '#4b5563'}};
                       text-decoration: none;
                       border-bottom: {{ request('tab', 'available') === 'available' ? '3px solid #6366f1' : 'none'}};
                       transition: all 0.2s;
                   ">
                    Available ({{ $counts['available'] ?? 0 }})
                </a>
                <a href="{{ route('admin.catalog.index', ['tab' => 'not-available']) }}"
                   style="
                       padding: 0.75rem 1.5rem;
                       font-weight: {{ request('tab', 'available') === 'not-available' ? '600' : '500'}};
                       color: {{ request('tab', 'available') === 'not-available' ? '#ef4444' : '#4b5563'}};
                       text-decoration: none;
                       border-bottom: {{ request('tab', 'available') === 'not-available' ? '3px solid #ef4444' : 'none'}};
                       transition: all 0.2s;
                   ">
                    Not Available ({{ $counts['not_available'] ?? 0 }})
                </a>
            </div>
        </div>

        <!-- Cards Container -->
        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        ">
            @forelse ($catalogs as $item)
                <div style="
                    background: white;
                    border-radius: 1rem;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                    border: 1px solid #e5e7eb;
                    transition: transform 0.2s, box-shadow 0.2s;
                "
                onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.12)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">

                    <!-- Image -->
                    <div style="height: 180px; background: #f3f4f6; position: relative;">
                        @if($item->product_image)
                            <img src="{{ asset('storage/' . $item->product_image) }}"
                                 alt="{{ $item->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="
                                width: 100%;
                                height: 100%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #9ca3af;
                                font-size: 4rem;
                                font-weight: bold;
                            ">
                                {{ strtoupper(substr($item->name ?? 'P', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div style="padding: 1.25rem;">
                        <h3 style="
                            font-size: 1.125rem;
                            font-weight: 600;
                            color: #111827;
                            margin: 0 0 0.5rem 0;
                            line-height: 1.4;
                        ">
                            {{ $item->name }}
                        </h3>

                        <p style="
                            color: #6b7280;
                            font-size: 0.875rem;
                            margin: 0 0 1rem 0;
                            line-height: 1.5;
                        ">
                            {{ $item->description ? Str::limit($item->description, 80) : 'No description' }}
                        </p>

                        <div style="margin: 1rem 0; font-size: 0.95rem; color: #4b5563;">

                            <div>
                                <strong>Min. Order:</strong> 
                                {{ $item->minimum_order_qty }} 
                                {{ $item->minimum_order_title === 'title_2' && $item->title_2 ? $item->title_2 : $item->title_1 }}
                            </div>

                            @if($item->title_2 && $item->value_per_title_2)
                                <div>
                                    <strong>Conversion:</strong> 
                                    1 {{ $item->title_2 }} = {{ $item->value_per_title_2 }} {{ $item->title_1 }}
                                </div>
                            @endif

                            <!-- Stock Display -->
                            <div style="margin-top: 0.75rem;">
                                <strong>Stock:</strong>
                                {!! $item->display_stock !!}

                            </div>

                            <!-- Price per Minimum Order -->
                            <div style="margin-top: 0.75rem;">
                                <strong>Price per Min. Order:</strong> 
                                @php
                                    $minPrice = 0;
                                    if ($item->minimum_order_title === 'title_2' && $item->title_2_price > 0) {
                                        $minPrice = $item->minimum_order_qty * $item->title_2_price;
                                    } elseif ($item->title_1_price > 0) {
                                        $minPrice = $item->minimum_order_qty * $item->title_1_price;
                                    }
                                @endphp

                                @if($minPrice > 0)
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                @else
                                    — Not set
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">
                            <a href="{{ route('admin.catalog.edit', $item->id) }}"
                               style="
                                   background: #3b82f6;
                                   color: white;
                                   padding: 0.5rem 1rem;
                                   border-radius: 0.5rem;
                                   text-decoration: none;
                                   font-size: 0.875rem;
                                   font-weight: 500;
                               ">
                                Edit
                            </a>

                            <form action="{{ route('admin.catalog.toggle', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit"
                                        style="
                                            background: {{ $item->is_available ? '#f59e0b' : '#10b981' }};
                                            color: white;
                                            padding: 0.5rem 1rem;
                                            border: none;
                                            border-radius: 0.5rem;
                                            font-size: 0.875rem;
                                            font-weight: 500;
                                            cursor: pointer;
                                        ">
                                    {{ $item->is_available ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.catalog.delete', $item->id) }}" method="POST" style="display: inline;">
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
                    </div>
                </div>
            @empty
                <div style="
                    grid-column: 1 / -1;
                    text-align: center;
                    padding: 4rem 1rem;
                    color: #6b7280;
                ">
                    <div style="font-size: 6rem; margin-bottom: 1rem; opacity: 0.5;">📦</div>
                    <p style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">
                        No products found
                    </p>
                    <p style="font-size: 1.1rem;">
                        @if($search)
                            Try a different search term or
                        @endif
                        click the button above to add your first product
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($catalogs->hasPages())
            <div style="margin-top: 2rem; text-align: center;">
                {{ $catalogs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-guest-layout>