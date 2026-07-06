<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('identity.card_title') }} – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="identity-card-body">
    <main class="identity-card-wrap">
        <div class="identity-card">
            <header class="identity-card-header">
                <span class="identity-card-logo"><span class="identity-logo-one">one</span><span class="identity-logo-member">member</span></span>
                <span class="identity-card-label">{{ __('identity.card_label') }}</span>
            </header>

            <div class="identity-card-avatar" aria-hidden="true">{{ mb_substr($customer->name, 0, 1) }}</div>
            <div class="identity-card-name">{{ $customer->name }}</div>
            <div class="identity-card-id">{{ $customer->onemember_id }}</div>

            <div class="identity-card-qr">{!! $qrSvg !!}</div>
            <p class="identity-card-scan-hint">{{ __('identity.card_scan_hint') }}</p>

            <footer class="identity-card-footer">
                <span>{{ __('identity.card_privacy') }}</span>
            </footer>
        </div>
    </main>
</body>
</html>
