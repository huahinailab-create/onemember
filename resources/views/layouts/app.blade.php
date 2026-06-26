<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name')) – {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>

<div class="d-flex">

    {{-- Sidebar --}}
    <nav class="sidebar d-flex flex-column p-3 gap-1" style="position: sticky; top: 0; height: 100vh;">

        {{-- Brand --}}
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 mb-3 text-decoration-none text-white px-2">
            <i class="bi bi-hexagon-fill fs-4 text-primary"></i>
            <span class="fw-semibold fs-5">{{ config('app.name') }}</span>
        </a>

        {{-- Navigation --}}
        <ul class="nav flex-column gap-1">
            @yield('sidebar-nav')
        </ul>

        <div class="mt-auto">
            @yield('sidebar-footer')
        </div>
    </nav>

    {{-- Main area --}}
    <div class="main-content d-flex flex-column">

        {{-- Topbar --}}
        <header class="topbar d-flex align-items-center justify-content-between px-4" style="position: sticky; top: 0; z-index: 100;">
            <div class="d-flex align-items-center gap-2">
                @yield('page-breadcrumb')
            </div>
            <div class="d-flex align-items-center gap-3">
                @yield('topbar-actions')
            </div>
        </header>

        {{-- Page content --}}
        <main class="p-4 flex-grow-1">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="px-4 py-3 border-top text-muted small">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </footer>

    </div>
</div>

@stack('scripts')
</body>
</html>
