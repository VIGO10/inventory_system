<header style="
    position: relative;
    background: linear-gradient(to right, #1e1b4b, #312e81, #4338ca);
    color: white;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.7);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    z-index: 50; max-height: 100px;
">

    <div style="
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 15% 20%, rgba(139,92,246,0.12) 0%, transparent 50%);
        pointer-events: none;
        opacity: 0.7;
    "></div>

    <div style="
        max-width: 1400px;
        margin: 0 auto;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 10;
    ">

        <!-- Logo + Brand -->
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="
                width: 56px;
                height: 56px;
                background: linear-gradient(135deg, #6366f1, #a855f7);
                border-radius: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 25px -5px rgba(99,102,241,0.5);
                border: 1px solid rgba(255,255,255,0.15);
            ">
                <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>

            <div>
                <h1 style="
                    font-size: 1.75rem;
                    font-weight: 800;
                    letter-spacing: -0.025em;
                    margin: 0;
                    background: linear-gradient(to right, #e0e7ff, #c7d2fe);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                ">
                    Inventory
                </h1>
                <p style="
                    font-size: 0.875rem;
                    color: rgba(199,210,254,0.75);
                    margin-top: -0.25rem;
                    font-weight: 400;
                ">
                    Management System
                </p>
            </div>
        </div>

        <!-- Navigation + User Area -->
        @if (Auth::check() && Auth::user()->role === 'admin')
        <div style="display: flex; align-items: center; gap: 2.5rem;">

            <!-- Main Navigation Menu -->
            <nav style="display: flex; gap: 0.75rem;">
                @php
                    $currentRoute = Route::currentRouteName();
                @endphp

                <a href="{{ route('admin.catalog.index') }}"
                   style="
                       padding: 0.6rem 1.1rem;
                       border-radius: 0.75rem;
                       font-weight: 500;
                       font-size: 0.95rem;
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.color='white'"
                   onmouseout="this.style.background='{{ str_starts_with($currentRoute, 'admin.catalog.index') ? 'rgba(255,255,255,0.12)' : 'transparent' }}'; this.style.color='{{ str_starts_with($currentRoute, 'users') ? 'white' : 'rgba(255,255,255,0.75)' }}'"
                >Catalog</a>

                <a href="{{ route('admin.supplier.index') }}"
                   style="
                       padding: 0.6rem 1.1rem;
                       border-radius: 0.75rem;
                       font-weight: 500;
                       font-size: 0.95rem;
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.color='white'"
                   onmouseout="this.style.background='{{ str_starts_with($currentRoute, 'admin.supplier.index') ? 'rgba(255,255,255,0.12)' : 'transparent' }}'; this.style.color='{{ str_starts_with($currentRoute, 'users') ? 'white' : 'rgba(255,255,255,0.75)' }}'"
                >Supplier</a>
                
                <a href="{{ route('admin.vendor.index') }}"
                   style="
                       padding: 0.6rem 1.1rem;
                       border-radius: 0.75rem;
                       font-weight: 500;
                       font-size: 0.95rem;
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.color='white'"
                   onmouseout="this.style.background='{{ str_starts_with($currentRoute, 'admin.vendor.index') ? 'rgba(255,255,255,0.12)' : 'transparent' }}'; this.style.color='{{ str_starts_with($currentRoute, 'users') ? 'white' : 'rgba(255,255,255,0.75)' }}'"
                >Vendor</a>

                <a href="{{ route('admin.user.index') }}"
                   style="
                       padding: 0.6rem 1.1rem;
                       border-radius: 0.75rem;
                       font-weight: 500;
                       font-size: 0.95rem;
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.color='white'"
                   onmouseout="this.style.background='{{ str_starts_with($currentRoute, 'admin.user.index') ? 'rgba(255,255,255,0.12)' : 'transparent' }}'; this.style.color='{{ str_starts_with($currentRoute, 'users') ? 'white' : 'rgba(255,255,255,0.75)' }}'"
                >User</a>

                <a href="{{ route('admin.dashboard') }}"
                   style="
                       padding: 0.6rem 1.1rem;
                       border-radius: 0.75rem;
                       font-weight: 500;
                       font-size: 0.95rem;
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.color='white'"
                   onmouseout="this.style.background='{{ str_starts_with($currentRoute, 'admin.dashboard') ? 'rgba(255,255,255,0.12)' : 'transparent' }}'; this.style.color='{{ str_starts_with($currentRoute, 'dashboard') ? 'white' : 'rgba(255,255,255,0.75)' }}'"
                >Dashboard</a>

            </nav>

            <!-- User Info + Logout -->
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.5rem 1rem;
                    background: rgba(255,255,255,0.08);
                    border-radius: 0.75rem;
                    border: 1px solid rgba(255,255,255,0.1);
                ">
                    <div style="text-align: right;">
                        <div style="font-weight: 600; font-size: 0.95rem;">{{ Auth::user()->fullname }}</div>
                        <div style="font-size: 0.75rem; color: rgba(199,210,254,0.7);">
                            {{ ucfirst(Auth::user()->role) }}
                        </div>                
                    </div>
                </div>

                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   style="
                       padding: 0.5rem 1.25rem;
                       background: rgba(239,68,68,0.15);
                       color: #fca5a5;
                       border-radius: 0.75rem;
                       font-size: 0.875rem;
                       font-weight: 500;
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(239,68,68,0.25)'; this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.background='rgba(239,68,68,0.15)'; this.style.transform='translateY(0)'"
                >
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
        @endif

    </div>
</header>