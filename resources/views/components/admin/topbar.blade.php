{{-- resources/views/components/admin/topbar.blade.php --}}
@php
    $admin = Auth::guard('admin')->user();
    $adminName = $admin?->name ?? 'Admin';
    $adminInitials = strtoupper(substr($adminName, 0, 2));
@endphp
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

        <button type="button" class="lb-topbar__avatar lb-topbar__avatar-btn" id="lbAdminTopbarAvatarBtn"
            data-photo-url="{{ $admin?->profile_photo_url ?? '' }}"
            data-photo-name="{{ $adminName }}"
            @if (empty($admin?->profile_photo_url)) aria-disabled="true" @endif>
            @if (!empty($admin?->profile_photo_url))
                <img class="lb-topbar__avatar-img" src="{{ $admin->profile_photo_url }}"
                    alt="{{ $adminName }} profile photo">
            @else
                <span aria-hidden="true">{{ $adminInitials }}</span>
            @endif
        </button>

        <button class="lb-topbar__user-btn" onclick="toggleAdminDropdown()">
            <span class="lb-topbar__user-name">{{ Auth::guard('admin')->user()->name }}</span>
            <svg class="lb-topbar__chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="lb-topbar__dropdown" id="adminDropdownMenu">
            <a href="{{ route('admin.profile.index') }}" class="lb-topbar__dropdown-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                My Profile
            </a>
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

{{-- Full image viewer (admin topbar) --}}
<div class="lb-photo-viewer" id="lbAdminTopbarPhotoViewer" aria-hidden="true">
    <div class="lb-photo-viewer__backdrop" onclick="lbCloseAdminTopbarPhotoViewer()"></div>
    <div class="lb-photo-viewer__dialog" role="dialog" aria-modal="true" aria-label="Profile photo viewer">
        <button type="button" class="lb-photo-viewer__close" onclick="lbCloseAdminTopbarPhotoViewer()"
            aria-label="Close photo viewer">✕</button>
        <img id="lbAdminTopbarPhotoViewerImg" class="lb-photo-viewer__img" alt="Profile photo">
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

    function lbOpenAdminTopbarPhotoViewer(src, name) {
        const viewer = document.getElementById('lbAdminTopbarPhotoViewer');
        const img = document.getElementById('lbAdminTopbarPhotoViewerImg');
        if (!viewer || !img || !src) return;
        img.src = src;
        img.alt = (name || 'Admin') + ' profile photo';
        viewer.classList.add('open');
        viewer.setAttribute('aria-hidden', 'false');
    }

    function lbCloseAdminTopbarPhotoViewer() {
        const viewer = document.getElementById('lbAdminTopbarPhotoViewer');
        const img = document.getElementById('lbAdminTopbarPhotoViewerImg');
        if (!viewer) return;
        viewer.classList.remove('open');
        viewer.setAttribute('aria-hidden', 'true');
        if (img) img.removeAttribute('src');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            lbCloseAdminTopbarPhotoViewer();
        }
    });

    (function() {
        const btn = document.getElementById('lbAdminTopbarAvatarBtn');
        if (!btn) return;
        btn.addEventListener('click', function() {
            const src = btn.getAttribute('data-photo-url');
            const name = btn.getAttribute('data-photo-name');
            if (!src) return;
            lbOpenAdminTopbarPhotoViewer(src, name);
        });
    })();
</script>
