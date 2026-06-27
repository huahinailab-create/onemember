<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

    <div class="text-center" style="max-width: 480px; padding: 2rem;">
        <i class="bi bi-hexagon-fill text-primary mb-3 d-block" style="font-size: 3rem;"></i>
        <h1 class="fw-bold h2 mb-2">{{ config('app.name') }}</h1>
        <p class="text-muted mb-4">Loyalty management for merchants and members.</p>
        <div class="d-flex gap-2 justify-content-center">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a>
            @endauth
        </div>
    </div>

</body>
</html>
