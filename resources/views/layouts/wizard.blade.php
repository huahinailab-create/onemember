<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Setup – ' . config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="min-vh-100 d-flex flex-column">

    {{-- Wizard Header --}}
    <header class="bg-white border-bottom py-3">
        <div class="container" style="max-width:700px;">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                    <i class="bi bi-hexagon-fill text-primary fs-4"></i>
                    <span class="fw-bold fs-5 text-dark">{{ config('app.name') }}</span>
                </a>
                @yield('header-action')
            </div>
        </div>
    </header>

    {{-- Wizard Content --}}
    <main class="flex-grow-1 d-flex align-items-start justify-content-center py-5">
        <div class="w-100 px-3" style="max-width:640px;">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-3 text-center text-muted small border-top bg-white">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>

</div>

</body>
</html>
