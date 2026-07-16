{{-- RELEASE-001 — branded error pages. Deliberately self-contained (inline
     styles, no Vite asset) so they render even when the build pipeline or
     app state is the thing that broke. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>@yield('code') – OneMember</title>
    <style>
        body { margin: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Noto Sans Thai', sans-serif;
               min-height: 100vh; display: flex; align-items: center; justify-content: center;
               background: linear-gradient(160deg, #1A2E5A, #16264a); color: #fff; text-align: center; padding: 1.5rem; }
        .box { max-width: 26rem; }
        .brand { font-weight: 700; font-size: 1.15rem; letter-spacing: -0.01em; margin-bottom: 2rem; }
        .brand .one { color: #FF1585; }
        .code { font-size: 3.5rem; font-weight: 700; line-height: 1; margin: 0 0 0.5rem; }
        h1 { font-size: 1.15rem; font-weight: 600; margin: 0 0 0.5rem; }
        p { color: rgba(255, 255, 255, 0.75); margin: 0 0 1.75rem; font-size: 0.95rem; }
        a.btn { display: inline-block; min-height: 44px; line-height: 44px; padding: 0 1.5rem; border-radius: 999px;
                background: #FF1585; color: #fff; text-decoration: none; font-weight: 600; }
        a.btn:focus-visible { outline: 3px solid #fff; outline-offset: 2px; }
    </style>
</head>
<body>
    <main class="box">
        <div class="brand"><span class="one">one</span>member</div>
        <p class="code" aria-hidden="true">@yield('code')</p>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        <a class="btn" href="{{ url('/') }}">{{ __('errors.go_home') }}</a>
    </main>
</body>
</html>
