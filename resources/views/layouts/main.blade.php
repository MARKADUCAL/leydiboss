{{-- resources/views/layouts/main.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Global Styles --}}
    @vite(['resources/css/landing/main.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>

<body>

    {{-- Top Navigation Bar --}}
    @include('components.landing.topbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.landing.footer')

    {{-- Force full-page navigation for customer login/register (avoids click interception) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('a[href*="/customer/login"], a[href*="/customer/register"]').forEach(function (a) {
                if (!a.href) return;
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.location.href = a.href;
                });
            });
        });
    </script>

</body>

</html>
