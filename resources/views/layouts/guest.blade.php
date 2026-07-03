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
            <a href="{{ url('/') }}" class="text-decoration-none">
                <span class="fw-bold fs-4" style="font-family:Arial,sans-serif;">
                    <span style="color:#FF1585;">one</span><span style="color:#1A2E5A;">member</span>
                </span>
            </a>
        </div>

        <div class="card card-brand-top">
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
