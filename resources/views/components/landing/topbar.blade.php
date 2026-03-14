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

<header class="topbar">
    <div class="topbar-container">

        {{-- Logo --}}
        <div class="logo">
            <img src="{{ asset('logo.png') }}" alt="Leydi Boss Logo">
            Leydi Boss
        </div>

        {{-- Navigation --}}
        <nav class="nav-menu">
            <a href="#home" class="nav-link active">Home</a>
            <a href="#services" class="nav-link">Services</a>
            <a href="#gallery" class="nav-link">Gallery</a>
            <a href="#contact" class="nav-link">Contact</a>
        </nav>

        {{-- Get Started Button --}}
        <a href="#" class="btn-started" onclick="showTopbarConfirm(event)">Get Started</a>

    </div>
</header>

{{-- Custom Confirm Modal --}}
<div id="topbarConfirmModal" class="topbar-modal-overlay">
    <div class="topbar-modal-box">
        <div class="topbar-modal-icon">🚗</div>
        <h2 class="topbar-modal-title">Ready to Get Started?</h2>
        <p class="topbar-modal-message">You're about to be redirected to the booking page. Would you like to continue?
        </p>
        <div class="topbar-modal-buttons">
            <button class="topbar-modal-btn topbar-modal-cancel" onclick="closeTopbarConfirm()">Cancel</button>
            <a href="{{ route('customer.login') }}" class="topbar-modal-btn topbar-modal-confirm">Yes, Let's Go!</a>
        </div>
    </div>
</div>

{{-- Close dropdown when clicking outside --}}
<script>
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

    function showTopbarConfirm(e) {
        e.preventDefault();
        document.getElementById('topbarConfirmModal').classList.add('active');
    }

    function closeTopbarConfirm() {
        document.getElementById('topbarConfirmModal').classList.remove('active');
    }

    document.getElementById('topbarConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeTopbarConfirm();
    });
</script>
