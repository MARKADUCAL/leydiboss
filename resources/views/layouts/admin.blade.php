{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Portal')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Layout styles --}}
    @vite(['resources/css/admin.css'])

    {{-- Page-specific styles injected here --}}
    @stack('styles')
</head>

<body class="lb-layout">

    {{-- ── Backdrop Overlay (mobile) ── --}}
    <div class="lb-layout__backdrop" id="sidebarBackdrop"></div>

    {{-- ── Sidebar (left column) ── --}}
    <aside class="lb-layout__sidebar">
        @include('components.admin.sidebar')
    </aside>

    {{-- ── Right column: topbar + page content ── --}}
    <div class="lb-layout__body">

        {{-- Topbar --}}
        <div class="lb-layout__topbar">
            @include('components.admin.topbar')
        </div>

        {{-- Page Content --}}
        <main class="lb-layout__main">
            @yield('content')
        </main>

    </div>

    {{-- Scripts --}}
    @stack('scripts')
    @yield('scripts')

</body>

</html>
