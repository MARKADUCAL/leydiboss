{{-- topbar.blade.php --}}
@php
    $customer = auth('customer')->user();
    $userName = $userName ?? ($customer?->name ?? 'Customer');
    $userInitials =
        $userInitials ??
        ($customer
            ? (count($parts = array_filter(explode(' ', $customer->name))) >= 2
                ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1))
                : strtoupper(mb_substr($customer->name, 0, 2)))
            : 'CU');
@endphp
<header class="lb-topbar">

    {{-- Sidebar Toggle Button --}}
    <button class="lb-topbar__sidebar-toggle" id="sidebarToggleBtn" aria-label="Toggle sidebar" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Left: Page Title --}}
    <div class="lb-topbar__title">
        {{ $pageTitle ?? 'Customer Dashboard' }}
    </div>

    {{-- Right: User Menu --}}
    <div class="lb-topbar__user" id="lbUserMenu">

        {{-- Avatar circle with initials --}}
        <div class="lb-topbar__avatar" aria-hidden="true">
            {{ $userInitials }}
        </div>

        {{-- Full name (login username) --}}
        <span class="lb-topbar__username">
            {{ $userName }}
        </span>

        {{-- Chevron toggle --}}
        <button class="lb-topbar__chevron-btn" aria-label="Open user menu" aria-expanded="false"
            aria-controls="lbUserDropdown" onclick="lbToggleDropdown(this)">
            <svg class="lb-topbar__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                width="18" height="18">
                <path fill-rule="evenodd"
                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Dropdown Menu --}}
        <div class="lb-topbar__dropdown" id="lbUserDropdown" role="menu" aria-hidden="true">
            <a href="{{ route('customer.profile.index') }}" class="lb-topbar__dropdown-item" role="menuitem">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16"
                    height="16">
                    <path fill-rule="evenodd"
                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                        clip-rule="evenodd" />
                </svg>
                My Profile
            </a>

            <div class="lb-topbar__dropdown-divider"></div>

            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="lb-topbar__dropdown-item lb-topbar__dropdown-item--danger"
                    role="menuitem">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16"
                        height="16">
                        <path fill-rule="evenodd"
                            d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm10.72 4.72a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H9a.75.75 0 010-1.5h10.94l-1.72-1.72a.75.75 0 010-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </div>

</header>

{{-- Close dropdown when clicking outside --}}
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

    function lbToggleDropdown(btn) {
        const dropdown = document.getElementById('lbUserDropdown');
        const isOpen = btn.getAttribute('aria-expanded') === 'true';

        btn.setAttribute('aria-expanded', String(!isOpen));
        dropdown.setAttribute('aria-hidden', String(isOpen));
        dropdown.classList.toggle('lb-topbar__dropdown--open', !isOpen);
        btn.querySelector('.lb-topbar__chevron').style.transform = isOpen ? '' : 'rotate(180deg)';
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('lbUserMenu');
        if (menu && !menu.contains(e.target)) {
            const dropdown = document.getElementById('lbUserDropdown');
            const btn = menu.querySelector('.lb-topbar__chevron-btn');
            if (dropdown) {
                dropdown.classList.remove('lb-topbar__dropdown--open');
                dropdown.setAttribute('aria-hidden', 'true');
            }
            if (btn) {
                btn.setAttribute('aria-expanded', 'false');
                btn.querySelector('.lb-topbar__chevron').style.transform = '';
            }
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
