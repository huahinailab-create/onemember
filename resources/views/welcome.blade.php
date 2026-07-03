<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — {{ __('welcome.meta_title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --om-navy:#1A2E5A; --om-navy-d:#0F1C3A; --om-pink:#FF1585; }
        .om-hero     { background:var(--om-navy); }
        .btn-om-pink { background:var(--om-pink);border-color:var(--om-pink);color:#fff; }
        .btn-om-pink:hover { background:#d90f70;border-color:#d90f70;color:#fff; }
        .benefit-icon { width:52px;height:52px;border-radius:12px; }
    </style>
</head>
<body>

{{-- ── Hero ────────────────────────────────────────────── --}}
<header class="om-hero text-white">
    <div class="container py-3">
        <nav class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="text-decoration-none">
                <span class="fw-bold fs-3" style="font-family:Arial,sans-serif;">
                    <span style="color:var(--om-pink);">one</span><span class="text-white">member</span>
                </span>
            </a>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        {{ __('welcome.go_to_dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        {{ __('welcome.sign_in') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-om-pink btn-sm fw-semibold">
                        {{ __('welcome.start_free_trial') }}
                    </a>
                @endauth
            </div>
        </nav>
    </div>

    <div class="container py-5 text-center">
        <span class="badge rounded-pill px-3 py-2 mb-4 fw-normal"
              style="background:rgba(255,21,133,.18);color:var(--om-pink);font-size:.875rem;">
            {{ __('welcome.trial_badge') }}
        </span>
        <h1 class="display-5 fw-bold mb-3" style="max-width:640px;margin:0 auto;">
            {{ __('welcome.hero_headline') }}
        </h1>
        <p class="lead mb-5 text-white-50" style="max-width:520px;margin:0 auto;">
            {{ __('welcome.hero_sub') }}
        </p>
        @guest
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('register') }}" class="btn btn-om-pink btn-lg fw-semibold px-5">
                    {{ __('welcome.cta_primary') }}
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                    {{ __('welcome.cta_sign_in') }}
                </a>
            </div>
            <p class="mt-3 text-white-50 small">
                <i class="bi bi-shield-check me-1"></i>{{ __('welcome.no_credit_card') }}
            </p>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-om-pink btn-lg fw-semibold px-5">
                {{ __('welcome.go_to_dashboard') }}
            </a>
        @endguest
    </div>
</header>

{{-- ── Benefits ────────────────────────────────────────── --}}
<section class="py-5 bg-white">
    <div class="container" style="max-width:900px;">
        <div class="text-center mb-5">
            <h2 class="fw-bold fs-2 mb-2" style="color:var(--om-navy);">
                {{ __('welcome.benefits_heading') }}
            </h2>
            <p class="text-muted">{{ __('welcome.benefits_sub') }}</p>
        </div>
        <div class="row g-4">
            @foreach ([
                ['bi-people-fill',    'benefit_members_title', 'benefit_members_body', '#e8f0fe', '#1A2E5A'],
                ['bi-graph-up-arrow', 'benefit_revenue_title', 'benefit_revenue_body', '#fff0f7', '#FF1585'],
                ['bi-lightning-fill', 'benefit_auto_title',    'benefit_auto_body',    '#f0fdf4', '#16A34A'],
            ] as [$icon, $title, $body, $bg, $color])
            <div class="col-12 col-md-4 text-center">
                <div class="benefit-icon d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="background:{{ $bg }};">
                    <i class="bi {{ $icon }} fs-4" style="color:{{ $color }};"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:var(--om-navy);">{{ __('welcome.'.$title) }}</h5>
                <p class="text-muted small mb-0">{{ __('welcome.'.$body) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── How it works ────────────────────────────────────── --}}
<section class="py-5" style="background:#F0F0F4;">
    <div class="container" style="max-width:780px;">
        <div class="text-center mb-5">
            <h2 class="fw-bold fs-2 mb-2" style="color:var(--om-navy);">{{ __('welcome.how_heading') }}</h2>
        </div>
        <div class="row g-4">
            @foreach ([
                ['01', 'how_step1_title', 'how_step1_body'],
                ['02', 'how_step2_title', 'how_step2_body'],
                ['03', 'how_step3_title', 'how_step3_body'],
            ] as [$num, $title, $body])
            <div class="col-12 col-md-4 text-center">
                <div class="fw-bold mb-2 fs-4" style="color:var(--om-pink);">{{ $num }}</div>
                <h6 class="fw-bold mb-1" style="color:var(--om-navy);">{{ __('welcome.'.$title) }}</h6>
                <p class="text-muted small mb-0">{{ __('welcome.'.$body) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Final CTA ───────────────────────────────────────── --}}
@guest
<section class="py-5 om-hero text-white text-center">
    <div class="container">
        <h2 class="fw-bold fs-2 mb-3">{{ __('welcome.final_cta_heading') }}</h2>
        <p class="text-white-50 mb-4">{{ __('welcome.final_cta_sub') }}</p>
        <a href="{{ route('register') }}" class="btn btn-om-pink btn-lg fw-semibold px-5">
            {{ __('welcome.cta_primary') }}
        </a>
        <p class="mt-3 text-white-50 small">
            <i class="bi bi-shield-check me-1"></i>{{ __('welcome.no_credit_card') }}
        </p>
    </div>
</section>
@endguest

{{-- ── Footer ──────────────────────────────────────────── --}}
<footer class="py-4 text-center text-muted small border-top bg-white">
    &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('welcome.footer_rights') }}
</footer>

</body>
</html>
