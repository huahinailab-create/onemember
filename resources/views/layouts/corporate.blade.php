<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1A2E5A">
    <link rel="icon" href="/favicon.ico">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <title>@yield('title', __('corporate.home_meta_title'))</title>
    <meta name="description" content="@yield('description', __('corporate.home_meta_desc'))">

    {{-- Open Graph — PNG image: LINE/Facebook/Twitter do not render SVG og:images --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', __('corporate.home_meta_title'))">
    <meta property="og:description" content="@yield('og_description', __('corporate.home_meta_desc'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="OneMember">
    <meta property="og:locale" content="{{ app()->getLocale() === 'th' ? 'th_TH' : 'en_US' }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', __('corporate.home_meta_title'))">
    <meta name="twitter:description" content="@yield('og_description', __('corporate.home_meta_desc'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Organization structured data — sitewide, truthful only (no ratings/reviews).
         Key built via concat: the literal string "@context" in Blade source
         is mis-parsed as the framework's @context directive otherwise. --}}
    <script type="application/ld+json">
    {!! json_encode([
        ('@' . 'context') => 'https://schema.org',
        ('@' . 'type') => 'Organization',
        'name' => 'OneMember',
        'url' => config('domains.corporate') ? 'https://' . config('domains.corporate') : url('/'),
        'logo' => asset('images/og-default.png'),
        'description' => __('corporate.home_meta_desc'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- WEBSITE-002A polish: corporate.js is Bootstrap-only (no Alpine, no
         Cropper chunk) — the marketing site needs none of the merchant app JS --}}
    @vite(['resources/css/app.css', 'resources/js/corporate.js'])
</head>
<body class="corp-body">

{{-- Skip link — first focusable element for keyboard/screen-reader users --}}
<a href="#corp-main" class="visually-hidden-focusable corp-skip-link">{{ __('corporate.skip_to_content') }}</a>

{{-- Corporate Navigation --}}
<nav class="navbar navbar-expand-lg corp-nav sticky-top" id="corpNav">
    <div class="container">
        <a class="navbar-brand corp-logo" href="{{ route('corporate.home') }}">
            <span style="color:#FF1585;font-weight:700;">one</span><span style="color:#1A2E5A;font-weight:700;">member</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#corpNavMenu" aria-controls="corpNavMenu" aria-expanded="false" aria-label="{{ __('corporate.nav_toggle_menu') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="corpNavMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('solutions') || request()->is('industries') || request()->is('features') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        {{ __('corporate.nav_product') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('corporate.solutions') }}">{{ __('corporate.nav_solutions') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.industries') }}">{{ __('corporate.nav_industries') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.features') }}">{{ __('corporate.nav_features') }}</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pricing') ? 'active' : '' }}" href="{{ route('corporate.pricing') }}">{{ __('corporate.nav_pricing') }}</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('about') || request()->is('blog') || request()->is('resources') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        {{ __('corporate.nav_company') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('corporate.about') }}">{{ __('corporate.nav_about') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.resources') }}">{{ __('corporate.nav_resources') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.blog') }}">{{ __('corporate.nav_blog') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.careers') }}">{{ __('corporate.nav_careers') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.partners') }}">{{ __('corporate.nav_partners') }}</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="{{ route('corporate.contact') }}">{{ __('corporate.nav_contact') }}</a>
                </li>
            </ul>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <x-language-switcher />
                @if (config('services.line.oa_url'))
                    <a href="{{ config('services.line.oa_url') }}" target="_blank" rel="noopener"
                       class="btn btn-sm btn-outline-navy">
                        <i class="bi bi-chat-dots-fill ms-0 me-1" aria-hidden="true"></i>{{ __('corporate.nav_line') }}
                    </a>
                @endif
                @auth
                    <a href="{{ $appUrl }}/dashboard" class="btn btn-sm btn-pink">{{ __('corporate.nav_go_dashboard') }} <i class="bi bi-arrow-right ms-1"></i></a>
                @else
                    <a href="{{ $appUrl }}/login" class="btn btn-sm btn-outline-navy">{{ __('corporate.nav_sign_in') }}</a>
                    <a href="{{ $appUrl }}/register" class="btn btn-sm btn-pink">{{ __('corporate.cta_start_trial') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Page Content --}}
<main id="corp-main">
    @yield('content')
</main>

{{-- Corporate Footer --}}
<footer class="corp-footer mt-0">
    <div class="container">
        <div class="row g-4 py-5">
            <div class="col-lg-4 col-md-6">
                <div class="corp-logo mb-3">
                    <span style="color:#FF1585;font-weight:700;font-size:1.5rem;">one</span><span style="color:#ffffff;font-weight:700;font-size:1.5rem;">member</span>
                </div>
                <p class="text-muted-light small mb-3">{{ __('corporate.footer_tagline') }}</p>
                <p class="small text-muted-light mb-1">
                    <i class="bi bi-envelope me-1"></i> <a href="mailto:hello@onemember.co" class="footer-link">hello@onemember.co</a>
                </p>
                @if (config('services.line.oa_url'))
                    <p class="small text-muted-light mb-0">
                        <i class="bi bi-chat-dots-fill me-1" aria-hidden="true"></i>
                        <a href="{{ config('services.line.oa_url') }}" target="_blank" rel="noopener" class="footer-link">{{ __('corporate.contact_line_cta') }}</a>
                    </p>
                @endif
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">{{ __('corporate.footer_product') }}</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.solutions') }}">{{ __('corporate.nav_solutions') }}</a></li>
                    <li><a href="{{ route('corporate.industries') }}">{{ __('corporate.nav_industries') }}</a></li>
                    <li><a href="{{ route('corporate.features') }}">{{ __('corporate.nav_features') }}</a></li>
                    <li><a href="{{ route('corporate.pricing') }}">{{ __('corporate.nav_pricing') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">{{ __('corporate.footer_company') }}</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.about') }}">{{ __('corporate.nav_about') }}</a></li>
                    <li><a href="{{ route('corporate.blog') }}">{{ __('corporate.nav_blog') }}</a></li>
                    <li><a href="{{ route('corporate.careers') }}">{{ __('corporate.nav_careers') }}</a></li>
                    <li><a href="{{ route('corporate.partners') }}">{{ __('corporate.nav_partners') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">{{ __('corporate.footer_support') }}</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.faq') }}">{{ __('corporate.footer_faq') }}</a></li>
                    <li><a href="{{ route('corporate.resources') }}">{{ __('corporate.footer_knowledge_center') }}</a></li>
                    <li><a href="{{ route('corporate.contact') }}">{{ __('corporate.nav_contact') }}</a></li>
                    <li><a href="{{ route('corporate.demo') }}">{{ __('corporate.nav_book_demo') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">{{ __('corporate.footer_legal') }}</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.privacy') }}">{{ __('corporate.footer_privacy') }}</a></li>
                    <li><a href="{{ route('corporate.terms') }}">{{ __('corporate.footer_terms') }}</a></li>
                    <li><a href="{{ route('corporate.pdpa') }}">{{ __('corporate.footer_pdpa_notice') }}</a></li>
                    <li><a href="{{ route('corporate.security') }}">{{ __('corporate.footer_security') }}</a></li>
                </ul>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="row align-items-center py-3">
            <div class="col-md-6">
                <p class="small text-muted-light mb-0">© {{ date('Y') }} OneMember Co., Ltd. {{ __('corporate.footer_rights') }} 🇹🇭</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ $appUrl }}/login" class="btn btn-sm btn-outline-light-corp">{{ __('corporate.nav_sign_in') }}</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
