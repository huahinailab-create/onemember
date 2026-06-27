<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

    <div class="w-100" style="max-width: 440px; padding: 1rem;">

        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="text-decoration-none text-dark">
                <i class="bi bi-hexagon-fill text-primary" style="font-size: 2.5rem;"></i>
                <div class="fw-bold fs-4 mt-1">{{ config('app.name') }}</div>
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>

        <p class="text-center text-muted small mt-3">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>

</body>
</html>
