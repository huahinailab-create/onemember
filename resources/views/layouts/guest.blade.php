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
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

    <div class="w-100" style="max-width: 420px;">

        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="text-decoration-none text-dark">
                <i class="bi bi-hexagon-fill fs-1 text-primary"></i>
                <div class="fw-bold fs-4 mt-1">{{ config('app.name') }}</div>
            </a>
        </div>

        <div class="card p-4 shadow-sm">
            @yield('content')
        </div>

        <p class="text-center text-muted small mt-3">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>

@stack('scripts')
</body>
</html>
