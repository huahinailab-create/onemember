<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="launch-print-body">
    <div class="launch-print-toolbar d-print-none">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>{{ __('launch.open_print') }}
        </button>
    </div>
    {{ $slot }}
</body>
</html>
