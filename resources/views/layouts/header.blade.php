<style>
/* ===============================
   HEADER
=================================*/
.main-header {
    background: linear-gradient(to right, #1e1b4b, #312e81, #4338ca);
    color: white;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.7);
    position: relative;
    z-index: 50;
}

.header-container {
    max-width: 1400px;
    margin: auto;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ===============================
   BRAND
=================================*/
.brand-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    color: white;
}

.logo-box {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===============================
   DESKTOP MENU
=================================*/
.desktop-menu {
    display: flex;
    gap: 0.5rem;
}

.desktop-menu a {
    padding: 0.6rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    color: rgba(255,255,255,0.75);
    transition: 0.2s ease;
    font-size: 0.95rem;
}

.desktop-menu a:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

.desktop-menu a.active {
    background: rgba(255,255,255,0.15);
    color: white;
}

/* ===============================
   USER AREA
=================================*/
.header-right {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}

.user-box {
    background: rgba(255,255,255,0.1);
    padding: 0.5rem 0.9rem;
    border-radius: 10px;
    font-size: 0.85rem;
}

/* ===============================
   HAMBURGER
=================================*/
.hamburger {
    display: none;
    font-size: 1.8rem;
    cursor: pointer;
}

/* ===============================
   SIDEBAR
=================================*/
.mobile-sidebar {
    position: fixed;
    top: 0;
    left: -260px;
    width: 260px;
    height: 100%;
    background: #1e1b4b;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    transition: 0.3s ease;
    z-index: 1001;
}

.mobile-sidebar.active {
    left: 0;
}

.mobile-sidebar a {
    color: white;
    text-decoration: none;
    padding: 0.7rem;
    border-radius: 8px;
    transition: 0.2s;
}

.mobile-sidebar a:hover {
    background: rgba(255,255,255,0.15);
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

/* ===============================
   OVERLAY
=================================*/
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    opacity: 0;
    visibility: hidden;
    transition: 0.3s;
    z-index: 1000;
}

.overlay.active {
    opacity: 1;
    visibility: visible;
}

/* ===============================
   RESPONSIVE
=================================*/
@media (max-width: 992px) {
    .desktop-menu {
        display: none;
    }

    .hamburger {
        display: block;
    }

    .user-box {
        display: none;
    }
}
</style>

<header class="main-header">
    <div class="header-container">

        <!-- LOGO -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <div class="logo-box">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4" />
                </svg>
            </div>
            <div>
                <h2 style="margin:0;font-weight:700;">Inventory</h2>
                <small style="opacity:0.7;">Management System</small>
            </div>
        </a>

        <!-- DESKTOP NAV -->
        @php $route = Route::currentRouteName(); @endphp
        @if (Auth::check() && Auth::user()->role === 'admin')
            <nav class="desktop-menu">
                <a href="{{ route('admin.other-cost.index') }}" class="{{ str_contains($route,'other-cost') ? 'active' : '' }}">Other Cost</a>
                <a href="{{ route('admin.transaction.index') }}" class="{{ str_contains($route,'transaction') ? 'active' : '' }}">Transaction</a>
                <a href="{{ route('admin.catalog.index') }}" class="{{ str_contains($route,'catalog') ? 'active' : '' }}">Catalog</a>
                <a href="{{ route('admin.supplier.index') }}" class="{{ str_contains($route,'supplier') ? 'active' : '' }}">Supplier</a>
                <a href="{{ route('admin.vendor.index') }}" class="{{ str_contains($route,'vendor') ? 'active' : '' }}">Vendor</a>
                <a href="{{ route('admin.user.index') }}" class="{{ str_contains($route,'user') ? 'active' : '' }}">User</a>
            </nav>
        

            <!-- RIGHT -->
            <div class="header-right">
                <div style="text-align: right;">
                    <div style="font-weight: 50; font-size: 0.9rem;">{{ Auth::user()->fullname }}</div>
                    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.75);">
                        {{ ucfirst(Auth::user()->role) }}
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
                onmouseout="this.style.background='rgba(239,68,68,0.15)'; this.style.transform='translateY(0)'">
                    Logout
                </a>

                <form id="logout-form"
                    action="{{ route('logout') }}"
                    method="POST"
                    style="display: none;">
                    @csrf
                </form>

                <div class="hamburger" onclick="toggleSidebar()">☰</div>

            </div>
        @endif
    </div>
</header>

<!-- SIDEBAR -->
<div id="sidebar" class="mobile-sidebar">
    <div class="sidebar-header">
        <strong>Menu</strong>
        <button onclick="toggleSidebar()" style="background:none;border:none;color:white;font-size:1.2rem;">✕</button>
    </div>

    <a href="{{ route('admin.other-cost.index') }}" onclick="toggleSidebar()">Other Cost</a>
    <a href="{{ route('admin.transaction.index') }}" onclick="toggleSidebar()">Transaction</a>
    <a href="{{ route('admin.catalog.index') }}" onclick="toggleSidebar()">Catalog</a>
    <a href="{{ route('admin.supplier.index') }}" onclick="toggleSidebar()">Supplier</a>
    <a href="{{ route('admin.vendor.index') }}" onclick="toggleSidebar()">Vendor</a>
    <a href="{{ route('admin.user.index') }}" onclick="toggleSidebar()">User</a>
</div>

<!-- OVERLAY -->
<div id="overlay" class="overlay" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("overlay").classList.toggle("active");
}
</script>
