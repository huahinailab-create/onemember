@props(['branding', 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="{{ $branding->primaryColor() }}">

    <title>{{ $title ?? __('portal.page_title') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --portal-primary:   {{ $branding->primaryColor() }};
            --portal-secondary: {{ $branding->secondaryColor() }};
        }
    </style>
</head>
<body class="portal-body">

{{ $slot }}

<footer class="portal-footer text-center py-4">
    <small class="text-muted">
        {{ __('portal.powered_by') }}
        <a href="{{ config('app.url') }}" class="text-muted text-decoration-none fw-semibold">{{ config('app.name') }}</a>
    </small>
</footer>

</body>
</html>
