{{-- resources/views/components/admin/topbar.blade.php --}}
<div class="lb-topbar">

    {{-- ── Sidebar Toggle Button ───────────────────────────── --}}
    <button class="lb-topbar__sidebar-toggle" id="sidebarToggleBtn" aria-label="Toggle sidebar" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- ── Page Title ──────────────────────────────────────── --}}
    <div class="lb-topbar__title">
        @yield('page-title', 'Admin Dashboard')
    </div>

    {{-- ── User Dropdown ───────────────────────────────────── --}}
    <div class="lb-topbar__user" id="adminUserDropdown">

        <div class="lb-topbar__avatar">
            {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 2)) }}
        </div>

        <button class="lb-topbar__user-btn" onclick="toggleAdminDropdown()">
            <span class="lb-topbar__user-name">{{ Auth::guard('admin')->user()->name }}</span>
            <svg class="lb-topbar__chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="lb-topbar__dropdown" id="adminDropdownMenu">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="lb-topbar__dropdown-item lb-topbar__dropdown-item--danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3
                                 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>

    </div>

</div>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.lb-layout__sidebar');
        const backdrop = document.querySelector('.lb-layout__backdrop');
        const isMobile = window.innerWidth <= 768;

        if (sidebar) {
            if (isMobile) {
                // On mobile: toggle open class (slide in/out)
                sidebar.classList.toggle('open');
                if (backdrop) {
                    backdrop.classList.toggle('open');
                }
            } else {
                // On desktop: toggle collapsed class (shrink/expand)
                sidebar.classList.toggle('collapsed');
            }
        }
    }

    function toggleAdminDropdown() {
        document.getElementById('adminDropdownMenu').classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('adminUserDropdown');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('adminDropdownMenu').classList.remove('open');
        }
    });

    // Close sidebar when clicking on backdrop (mobile)
    document.addEventListener('click', function(e) {
        const backdrop = document.querySelector('.lb-layout__backdrop');
        if (backdrop && e.target === backdrop) {
            backdrop.classList.remove('open');
            document.querySelector('.lb-layout__sidebar').classList.remove('open');
        }
    });
</script>
</script>
