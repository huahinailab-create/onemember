<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#1A2E5A">
    <title>@yield('title', __('customer.page_title')) – OneMember</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="customer-body">

<main class="customer-shell @yield('shell-class')">
    <header class="customer-header">
        <a href="{{ auth('customer')->check() ? route('customer.wallet') : route('customer.login') }}"
           class="text-decoration-none" aria-label="OneMember">
            <span class="customer-wordmark"><span class="text-pink">one</span><span class="customer-wordmark-member">member</span></span>
        </a>
        @auth('customer')
            <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-light ms-auto me-2" aria-label="{{ __('customer.profile_title') }}">
                <i class="bi bi-person" aria-hidden="true"></i>
            </a>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">{{ __('customer.sign_out') }}</button>
            </form>
        @endauth
    </header>

    @hasSection('wallet-nav')
        @include('customer.wallet._nav')
    @endif

    <div class="customer-card">
        @if (session('status'))
            <div class="alert alert-success py-2" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </div>

    <footer class="customer-footer">
        <span>{{ __('customer.footer_tagline') }}</span>
        <span class="customer-footer-brand"><span class="text-pink">one</span>member</span>
    </footer>
</main>

</body>
</html>
