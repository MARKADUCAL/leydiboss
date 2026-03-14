{{-- resources/views/layouts/admin-auth.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin — Leydi Boss Car Wash & Detailing' }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Page-specific styles injected here --}}
    @stack('styles')
</head>

<body>


    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Page-specific scripts injected here --}}
    @yield('scripts')

</body>

</html>
