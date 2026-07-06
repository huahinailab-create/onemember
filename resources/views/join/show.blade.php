<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('launch.landing_welcome', ['merchant' => $merchant->name]) }} – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="launch-landing-body">
    <main class="launch-landing">
        <div class="launch-landing-card">
            <div class="launch-landing-brand">{{ $merchant->name }}</div>
            @if ($merchant->phone || $merchant->city)
                <div class="launch-landing-contact">
                    @if ($merchant->phone)<span><i class="bi bi-telephone me-1"></i>{{ $merchant->phone }}</span>@endif
                    @if ($merchant->city)<span><i class="bi bi-geo-alt me-1"></i>{{ $merchant->city }}</span>@endif
                </div>
            @endif

            <h1 class="launch-landing-headline">{{ __('launch.campaign_headline') }}</h1>
            <p class="launch-landing-offer">{{ __('launch.offer_' . $offer) }}</p>

            <p class="launch-landing-body-text">{{ __('launch.landing_body') }}</p>

            <div class="launch-landing-how">
                <h2 class="launch-landing-how-title">{{ __('launch.landing_how_title') }}</h2>
                <ol class="launch-landing-how-list">
                    <li>{{ __('launch.landing_how_1') }}</li>
                    <li>{{ __('launch.landing_how_2') }}</li>
                    <li>{{ __('launch.landing_how_3') }}</li>
                </ol>
            </div>

            <p class="launch-landing-privacy">{{ __('launch.landing_privacy', ['merchant' => $merchant->name]) }}</p>

            <footer class="launch-landing-footer">{{ __('launch.poster_powered') }}</footer>
        </div>
    </main>
</body>
</html>
