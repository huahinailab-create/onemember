<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA / mobile meta --}}
    <meta name="theme-color" content="#1A2E5A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@php
    $__logo        = $merchantBranding->logo();
    $__counterMode = (bool) (Auth::user()?->merchant?->settings['counter_mode'] ?? false);
@endphp

<div class="d-flex">

    {{-- ── Sidebar ───────────────────────────────────────── --}}
    <nav class="sidebar p-3" id="om-sidebar"
         aria-label="{{ __('navigation.main_menu') }}"
         role="navigation">

        {{-- Mobile close button --}}
        <div class="d-flex align-items-center justify-content-end d-md-none mb-2">
            <button type="button"
                    class="sidebar-close-btn"
                    id="om-sidebar-close"
                    aria-label="{{ __('mobile.close_menu') }}">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}"
           class="d-flex align-items-center gap-2 text-decoration-none text-white px-1 mb-1">
            @if ($__logo)
                <img src="{{ $__logo }}" alt="{{ $merchantBranding->displayName() }}"
                     style="height:36px;width:auto;max-width:120px;object-fit:contain;border-radius:4px;">
            @else
                <span class="sidebar-brand-mark">
                    <span class="brand-one">one</span><span class="brand-member">member</span>
                </span>
            @endif
        </a>
        @if ($__logo)
            <div class="px-1 mb-4" style="font-size:0.65rem;opacity:0.5;line-height:1;">
                {{ __('navigation.powered_by') }}
                <span style="font-family:Arial,sans-serif;font-weight:700;">
                    <span style="color:#FF1585;">one</span><span style="color:#ffffff;">member</span>
                </span>
            </div>
        @else
            <div class="mb-3"></div>
        @endif

        {{-- Main Menu --}}
        <div class="sidebar-section-label">{{ __('navigation.main_menu') }}</div>
        <ul class="nav flex-column gap-1 mb-2">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   >
                    <i class="bi bi-speedometer2"></i>
                    <span>{{ __('navigation.dashboard') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('members') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('members', 'members.*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-people"></i>
                    <span>{{ __('navigation.members') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('campaigns.index') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('campaigns.*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-star"></i>
                    <span>{{ __('navigation.campaigns') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('rewards') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('rewards') ? 'active' : '' }}"
                   >
                    <i class="bi bi-gift"></i>
                    <span>{{ __('navigation.rewards') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('transactions') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('transactions') ? 'active' : '' }}"
                   >
                    <i class="bi bi-arrow-left-right"></i>
                    <span>{{ __('navigation.transactions') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('reports') ? 'active' : '' }}"
                   >
                    <i class="bi bi-bar-chart-line"></i>
                    <span>{{ __('navigation.reports') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('launch-kit') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('launch-kit*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-rocket-takeoff"></i>
                    <span>{{ __('navigation.launch_kit') }}</span>
                </a>
            </li>
            @if (Auth::user()?->merchant?->hasApp('commerce'))
            <li class="nav-item">
                <a href="{{ route('commerce.products.index') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('commerce.*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-shop"></i>
                    <span>{{ __('navigation.commerce') }}</span>
                </a>
            </li>
            @endif
            {{-- PLATFORM-002: manifest-declared App navigation (Plugin SDK) --}}
            @if (Auth::user()?->merchant)
                @foreach (app(\App\Marketplace\AppRegistry::class)->all() as $manifestApp)
                    @if ($manifestApp->navigation !== [] && Auth::user()->merchant->hasApp($manifestApp->key))
                        @foreach ($manifestApp->navigation as $navItem)
                            <li class="nav-item">
                                <a href="{{ route($navItem['route']) }}"
                                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs($manifestApp->key . '.*') ? 'active' : '' }}">
                                    <i class="bi {{ $navItem['icon'] }}"></i>
                                    <span>{{ __($navItem['label']) }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
            @endif
            <li class="nav-item">
                <a href="{{ route('apps.index') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('apps.*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span>{{ __('navigation.apps') }}</span>
                </a>
            </li>
        </ul>

        {{-- Account --}}
        <div class="sidebar-section-label">{{ __('navigation.account') }}</div>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="{{ route('subscription.index') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('subscription.*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-credit-card"></i>
                    <span>{{ __('navigation.subscription') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('settings') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('settings*') ? 'active' : '' }}"
                   >
                    <i class="bi bi-gear"></i>
                    <span>{{ __('navigation.settings') }}</span>
                </a>
            </li>
        </ul>

        {{-- Developer Tools (non-production only) --}}
        @if (!app()->environment('production'))
        <div class="sidebar-section-label text-warning">{{ __('Developer Tools') }}</div>
        <ul class="nav flex-column gap-1 mb-2">
            <li class="nav-item">
                <a href="{{ route('dev.dashboard') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('dev.*') ? 'active' : '' }}">
                    <i class="bi bi-tools text-warning"></i>
                    <span class="text-warning">{{ __('navigation.dev_tools') }}</span>
                </a>
            </li>
        </ul>
        @endif

        {{-- Send Feedback --}}
        <div class="mt-3 mb-1 px-1">
            <button type="button"
                    class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center gap-2 justify-content-center"
                    data-bs-toggle="modal"
                    data-bs-target="#feedbackModal"
                    title="{{ __('navigation.send_feedback') }}">
                <i class="bi bi-chat-dots"></i>
                <span>{{ __('navigation.send_feedback') }}</span>
            </button>
        </div>

        {{-- User & Logout --}}
        <div class="mt-auto">
            <div class="sidebar-divider"></div>
            <div class="sidebar-user px-1 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                         style="width:32px;height:32px;font-size:0.75rem;font-weight:700;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="user-name text-truncate">{{ Auth::user()->name }}</div>
                        <div class="user-email text-truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout-btn d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>{{ __('navigation.log_out') }}</span>
                </button>
            </form>
        </div>

    </nav>

    {{-- ── Mobile backdrop — rendered AFTER sidebar in DOM so iOS Safari
         hit-testing does not route taps to it ahead of the sidebar ──── --}}
    <div class="sidebar-backdrop d-md-none"
         id="om-sidebar-backdrop"
         aria-hidden="true"></div>
    {{-- ── /Sidebar ──────────────────────────────────────── --}}

    {{-- ── Main area ─────────────────────────────────────── --}}
    <div class="main-content">

        {{-- Topbar --}}
        <header class="topbar">
            <button class="topbar-toggle" id="om-topbar-toggle" aria-label="{{ __('mobile.toggle_sidebar') }}">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title">{{ $pageTitle ?? '' }}</div>
            <div class="ms-auto d-flex align-items-center gap-2">
                {{-- Counter Mode toggle --}}
                <form method="POST" action="{{ route('counter-mode.toggle') }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                            class="topbar-counter-btn {{ $__counterMode ? 'active' : '' }}"
                            title="{{ $__counterMode ? __('mobile.counter_mode_disable') : __('mobile.counter_mode_enable') }}">
                        <i class="bi bi-shop"></i>
                        <span class="d-none d-sm-inline">{{ __('mobile.counter_mode') }}</span>
                    </button>
                </form>
                {{-- PLATFORM-002 P11: global help entry (Knowledge Center) --}}
                <a href="{{ route('help.index') }}"
                   class="btn btn-sm btn-outline-secondary rounded-circle help-btn {{ request()->routeIs('help.*') ? 'active' : '' }}"
                   title="{{ __('help.help_button') }}"
                   aria-label="{{ __('help.help_button') }}">
                    <i class="bi bi-question-lg" aria-hidden="true"></i>
                </a>
                <x-language-switcher />
                <div class="topbar-user">
                    <i class="bi bi-person-circle text-secondary"></i>
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Counter Mode bar --}}
        @if ($__counterMode)
            <div class="counter-mode-bar">
                <span class="counter-mode-label">
                    <i class="bi bi-shop me-1"></i>{{ __('mobile.counter_mode_active') }}
                </span>
                <div class="counter-mode-actions ms-auto">
                    <a href="{{ route('counter') }}"
                       class="btn btn-sm btn-light d-flex align-items-center gap-1">
                        <i class="bi bi-search"></i>
                        <span>{{ __('mobile.counter_find_member') }}</span>
                    </a>
                    <a href="{{ route('members.create') }}"
                       class="btn btn-sm btn-warning d-flex align-items-center gap-1">
                        <i class="bi bi-person-plus"></i>
                        <span>{{ __('mobile.counter_add_member') }}</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Flash messages (single global renderer — Design System §2.19) --}}
        @if (session('success') || session('error'))
            <div class="px-4 pt-4 pb-0">
                <x-ui.flash />
            </div>
        @endif

        {{-- Page content --}}
        <main class="content-area">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="app-footer d-flex align-items-center justify-content-between">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            <span>v{{ config('app.version') }}</span>
        </footer>

    </div>
    {{-- ── /Main area ────────────────────────────────────── --}}

</div>

{{-- FAB — mobile only --}}
<x-fab />

{{-- Floating feedback button (desktop only) --}}
<button type="button"
        class="d-none d-md-flex align-items-center justify-content-center position-fixed rounded-circle shadow-sm border-0"
        style="bottom:1.5rem;right:1.5rem;width:44px;height:44px;z-index:1040;background:var(--bs-secondary-bg);color:var(--bs-secondary-color);opacity:0.75;"
        data-bs-toggle="modal"
        data-bs-target="#feedbackModal"
        title="{{ __('navigation.send_feedback') }}">
    <i class="bi bi-chat-dots fs-5"></i>
</button>

@include('feedback.modal')

</body>
</html>
