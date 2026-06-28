<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="d-flex" x-data="{ sidebarOpen: true }">

    {{-- ── Sidebar ───────────────────────────────────────── --}}
    <nav class="sidebar p-3" :class="{ 'collapsed': !sidebarOpen }" aria-label="Main navigation">

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}"
           class="d-flex align-items-center gap-2 text-decoration-none text-white px-1 mb-4">
            <i class="bi bi-hexagon-fill text-primary fs-4 flex-shrink-0"></i>
            <span class="fw-bold fs-5 text-white">{{ config('app.name') }}</span>
        </a>

        {{-- Main Menu --}}
        <div class="sidebar-section-label">Main Menu</div>
        <ul class="nav flex-column gap-1 mb-2">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('members') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('members') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Members</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('campaigns.index') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                    <i class="bi bi-star"></i>
                    <span>Campaigns</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('rewards') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('rewards') ? 'active' : '' }}">
                    <i class="bi bi-gift"></i>
                    <span>Rewards</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('transactions') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('transactions') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Transactions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('reports') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>
        </ul>

        {{-- Account --}}
        <div class="sidebar-section-label">Account</div>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="{{ route('settings') }}"
                   class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

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
                    <span>Log Out</span>
                </button>
            </form>
        </div>

    </nav>
    {{-- ── /Sidebar ──────────────────────────────────────── --}}

    {{-- ── Main area ─────────────────────────────────────── --}}
    <div class="main-content">

        {{-- Topbar --}}
        <header class="topbar">
            <button class="topbar-toggle" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title">{{ $pageTitle ?? '' }}</div>
            <div class="topbar-user ms-auto">
                <i class="bi bi-person-circle text-secondary"></i>
                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success') || session('error'))
            <div class="px-4 pt-4 pb-0">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        @endif

        {{-- Page content --}}
        <main class="content-area">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="app-footer d-flex align-items-center justify-content-between">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            <span>v0.1.0</span>
        </footer>

    </div>
    {{-- ── /Main area ────────────────────────────────────── --}}

</div>

</body>
</html>
