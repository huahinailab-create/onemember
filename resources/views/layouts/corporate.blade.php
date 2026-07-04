<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'OneMember — Loyalty Platform for Thai SMEs')</title>
    <meta name="description" content="@yield('description', 'OneMember helps Thai small businesses build customer loyalty with points, stamps, and rewards. Start your free trial today.')">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'OneMember — Loyalty Platform for Thai SMEs')">
    <meta property="og:description" content="@yield('og_description', 'Build lasting customer loyalty with OneMember. Points, stamps, rewards, and birthday automation — all in one platform.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="OneMember">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'OneMember')">
    <meta name="twitter:description" content="@yield('og_description', 'Build lasting customer loyalty with OneMember.')">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="corp-body">

{{-- Corporate Navigation --}}
<nav class="navbar navbar-expand-lg corp-nav sticky-top" id="corpNav">
    <div class="container">
        <a class="navbar-brand corp-logo" href="{{ route('corporate.home') }}">
            <span style="color:#FF1585;font-weight:700;">one</span><span style="color:#1A2E5A;font-weight:700;">member</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#corpNavMenu" aria-controls="corpNavMenu" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="corpNavMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('solutions') || request()->is('industries') || request()->is('features') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        Product
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('corporate.solutions') }}">Solutions</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.industries') }}">Industries</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.features') }}">Features</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pricing') ? 'active' : '' }}" href="{{ route('corporate.pricing') }}">Pricing</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('about') || request()->is('blog') || request()->is('resources') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        Company
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('corporate.about') }}">About Us</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.resources') }}">Resources</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.blog') }}">Blog</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.careers') }}">Careers</a></li>
                        <li><a class="dropdown-item" href="{{ route('corporate.partners') }}">Partners</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="{{ route('corporate.contact') }}">Contact</a>
                </li>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <x-language-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-pink">Go to Dashboard <i class="bi bi-arrow-right ms-1"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-navy">Sign In</a>
                    <a href="{{ route('corporate.demo') }}" class="btn btn-sm btn-pink">Book a Demo</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Page Content --}}
@yield('content')

{{-- Corporate Footer --}}
<footer class="corp-footer mt-0">
    <div class="container">
        <div class="row g-4 py-5">
            <div class="col-lg-4 col-md-6">
                <div class="corp-logo mb-3">
                    <span style="color:#FF1585;font-weight:700;font-size:1.5rem;">one</span><span style="color:#ffffff;font-weight:700;font-size:1.5rem;">member</span>
                </div>
                <p class="text-muted-light small mb-3">The loyalty platform built for Thai SMEs. Help your customers keep coming back.</p>
                <p class="small text-muted-light">
                    <i class="bi bi-envelope me-1"></i> <a href="mailto:hello@onemember.co" class="footer-link">hello@onemember.co</a>
                </p>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Product</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.solutions') }}">Solutions</a></li>
                    <li><a href="{{ route('corporate.industries') }}">Industries</a></li>
                    <li><a href="{{ route('corporate.features') }}">Features</a></li>
                    <li><a href="{{ route('corporate.pricing') }}">Pricing</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Company</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.about') }}">About Us</a></li>
                    <li><a href="{{ route('corporate.blog') }}">Blog</a></li>
                    <li><a href="{{ route('corporate.careers') }}">Careers</a></li>
                    <li><a href="{{ route('corporate.partners') }}">Partners</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Support</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('corporate.contact') }}">Contact</a></li>
                    <li><a href="{{ route('corporate.demo') }}">Book a Demo</a></li>
                    <li><a href="{{ route('corporate.resources') }}">Resources</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Legal</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('corporate.privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('corporate.terms') }}">Terms of Service</a></li>
                    <li><a href="{{ route('corporate.security') }}">Security & PDPA</a></li>
                </ul>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="row align-items-center py-3">
            <div class="col-md-6">
                <p class="small text-muted-light mb-0">© {{ date('Y') }} OneMember Co., Ltd. All rights reserved. Thailand 🇹🇭</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light-corp">Sign In to App</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
