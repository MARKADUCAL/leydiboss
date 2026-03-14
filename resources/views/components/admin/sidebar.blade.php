{{-- resources/views/components/admin/sidebar.blade.php --}}
<div class="lb-sidebar">

    {{-- ── Brand ──────────────────────────────────────────── --}}
    <div class="lb-sidebar__brand">
        <div class="lb-sidebar__logo-wrap">
            <img src="{{ asset('logo.png') }}" alt="Leydi Boss" class="lb-sidebar__logo">
        </div>
        <span class="lb-sidebar__brand-name">Leydi Boss</span>
    </div>

    {{-- ── Navigation ─────────────────────────────────────── --}}
    <nav class="lb-sidebar__nav">

        <a href="{{ route('admin.dashboard') }}"
            class="lb-sidebar__nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="lb-sidebar__nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2
                             m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0
                             011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </span>
            <span class="lb-sidebar__nav-label">Dashboard</span>
        </a>

        @can('accessAdminCustomers')
        <a href="{{ route('admin.customers.index') }}"
            class="lb-sidebar__nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <span class="lb-sidebar__nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126
                             -1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656
                             .126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0
                             11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            <span class="lb-sidebar__nav-label">Customers</span>
        </a>
        @endcan

        @can('accessAdminAdmins')
        <a href="{{ route('admin.admins.index') }}"
            class="lb-sidebar__nav-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
            <span class="lb-sidebar__nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a4 4 0 01-4-4m0 0a4 4 0 118 0m0 0a4 4 0 01-8 0m6 0a4 4 0 01-4-4m0 0a4 4 0 118 0m0 0a4 4 0 01-8 0" />
                </svg>
            </span>
            <span class="lb-sidebar__nav-label">Admins</span>
        </a>
        @endcan

        <a href="{{ route('admin.services.index') }}"
            class="lb-sidebar__nav-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <span class="lb-sidebar__nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.75 3a.75.75 0 00-.75.75V6a.75.75 0 01-.75.75H6a.75.75 0 00-.75.75v2.25c0 .414.336.75.75.75h2.25c.414 0 .75.336.75.75V15a.75.75 0 01-.75.75H6a.75.75 0 00-.75.75v2.25c0 .414.336.75.75.75h2.25c.414 0 .75.336.75.75v2.25c0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75V21a.75.75 0 01.75-.75H18a.75.75 0 00.75-.75V17.25a.75.75 0 00-.75-.75h-2.25a.75.75 0 01-.75-.75v-2.25c0-.414.336-.75.75-.75H18a.75.75 0 00.75-.75V7.5a.75.75 0 00-.75-.75h-2.25A.75.75 0 0115 6V3.75A.75.75 0 0014.25 3h-4.5z" />
                </svg>
            </span>
            <span class="lb-sidebar__nav-label">Services</span>
        </a>

    </nav>

</div>
