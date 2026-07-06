<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? 'Dashboard') }} — OneMember Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .admin-sidebar {
            width: 240px;
            min-height: 100vh;
            background: #0d1b35;
            flex-shrink: 0;
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: rgba(255, 21, 133, 0.15);
            color: #fff;
        }
        .admin-sidebar .nav-link.active {
            border-left: 3px solid #FF1585;
            padding-left: calc(0.75rem - 3px);
        }
        .admin-sidebar .nav-link i { width: 18px; }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            height: 56px;
        }
        .admin-badge {
            background: #FF1585;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 2px 7px;
            border-radius: 4px;
            vertical-align: middle;
        }
        .admin-main {
            flex: 1;
            min-width: 0;
            background: #F0F0F4;
            min-height: 100vh;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1A2E5A;
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .stat-card .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        @media (max-width: 991px) {
            .admin-sidebar { width: 200px; }
        }
        @media (max-width: 767px) {
            .admin-sidebar { display: none !important; }
        }
    </style>
</head>
<body class="d-flex" style="font-family:'Figtree',sans-serif;">

    {{-- ── Sidebar ────────────────────────────────────────────────── --}}
    <aside class="admin-sidebar d-flex flex-column p-3 gap-1">

        {{-- Brand --}}
        <div class="mb-3 px-1 pt-1">
            <span style="color:#fff;font-weight:700;font-size:1.1rem;letter-spacing:-0.02em;">
                OneMember
            </span>
            <span class="admin-badge ms-2">ADMIN</span>
        </div>

        {{-- Nav --}}
        <nav class="d-flex flex-column gap-1">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="{{ route('admin.merchants.index') }}"
               class="nav-link {{ request()->routeIs('admin.merchants.*') ? 'active' : '' }}">
                <i class="bi bi-shop me-2"></i>Merchants
            </a>
            <a href="{{ route('admin.go-live') }}"
               class="nav-link {{ request()->routeIs('admin.go-live') ? 'active' : '' }}">
                <i class="bi bi-rocket-takeoff me-2"></i>Go-Live
            </a>
        </nav>

        {{-- Spacer --}}
        <div class="mt-auto"></div>

        {{-- User / exit --}}
        <div class="border-top border-secondary pt-3 mt-2">
            <p class="text-white-50 mb-1" style="font-size:0.75rem;font-weight:500;">
                {{ auth()->user()->name }}
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100 text-start px-2 py-1"
                        style="color:rgba(255,255,255,0.6);font-size:0.8rem;background:none;border:none;">
                    <i class="bi bi-box-arrow-left me-2"></i>Sign out
                </button>
            </form>
            <a href="{{ route('dashboard') }}"
               class="d-block mt-1 px-2 py-1"
               style="color:rgba(255,255,255,0.5);font-size:0.75rem;text-decoration:none;">
                <i class="bi bi-arrow-left-circle me-1"></i>Back to app
            </a>
        </div>
    </aside>

    {{-- ── Main area ──────────────────────────────────────────────── --}}
    <div class="admin-main d-flex flex-column">

        {{-- Topbar --}}
        <header class="admin-topbar d-flex align-items-center px-4">
            <h6 class="mb-0 fw-600" style="color:#1A2E5A;font-size:0.95rem;font-weight:600;">
                {{ $title ?? 'Dashboard' }}
            </h6>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted" style="font-size:0.8rem;">
                    {{ now()->format('d M Y') }}
                </span>
            </div>
        </header>

        {{-- Content --}}
        <main class="p-4">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
