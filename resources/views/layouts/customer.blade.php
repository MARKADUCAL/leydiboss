<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Styles --}}
    @vite(['resources/css/customer.css', 'resources/js/customer-layout.js'])
    @vite(['resources/css/auth/customer/sidebar.css'])
    @vite(['resources/css/auth/customer/topbar.css'])

    {{-- Sileo Vanilla CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/hamada147/sileo-vanilla/dist/styles.css" />

    @stack('styles')
</head>

<body class="lb-layout">

    {{-- ── Backdrop Overlay (mobile) ── --}}
    <div class="lb-layout__backdrop" id="sidebarBackdrop"></div>

    <aside class="lb-layout__sidebar">
        @include('components.customer.sidebar')
    </aside>

    <div class="lb-layout__body">
        <div class="lb-layout__topbar">
            @include('components.customer.topbar')
        </div>

        <main class="lb-layout__main">
            @yield('content')
        </main>
    </div>

    {{-- Sileo Vanilla JS --}}
    <script src="https://cdn.jsdelivr.net/gh/hamada147/sileo-vanilla/dist/sileo.iife.js"></script>

    {{-- Welcome Toast on Login --}}
    @if (session('login_success'))
        <script>
            sileo.init({
                position: 'top-center'
            });
            sileo.success({
                title: 'Welcome to LediBoss! 🎉',
                description: 'You are successfully logged in.',
                duration: 8000,
                style: {
                    minWidth: '420px',
                    padding: '24px 28px',
                    fontSize: '18px',
                }
            });
        </script>
    @endif


    @stack('scripts')
    @yield('scripts')

</body>

</html>
