<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'th'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if ($request->user()?->merchant?->settings) {
            $locale = $request->user()->merchant->settings['locale'] ?? null;
        }

        if ($locale && in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
