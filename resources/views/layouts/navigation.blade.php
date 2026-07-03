{{--
    LEGACY FILE — not used by the OneMember application.
    The active navigation lives in resources/views/layouts/app.blade.php (sidebar layout).
    Converted from Tailwind to Bootstrap 5 for consistency. RELEASE-1A.
--}}
<nav class="navbar navbar-expand-sm bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand text-decoration-none" href="{{ route('dashboard') }}">
            <span style="font-family:Arial,sans-serif;font-weight:700;">
                <span style="color:#FF1585;">one</span><span style="color:#1A2E5A;">member</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#legacyNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="legacyNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}"
                       href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.edit') }}">{{ Auth::user()->name }}</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link p-0">{{ __('Log Out') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
