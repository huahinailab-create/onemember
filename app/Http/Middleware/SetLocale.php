<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'th'];
    private const DEFAULT   = 'th';

    public function handle(Request $request, Closure $next): Response
    {
        // Priority chain:
        // 1. Merchant settings (authenticated, explicit user choice)
        // 2. Session (explicit user choice for guests or authenticated before merchant loads)
        // 3. Browser Accept-Language header (first visit only — not stored in session)
        // 4. APP_LOCALE / default (th)

        $locale = null;

        // 1. Merchant preference (highest priority)
        if ($request->user()?->merchant?->settings) {
            $locale = $request->user()->merchant->settings['locale'] ?? null;
        }

        // 2. Session preference (explicit switch by user)
        if (! $locale) {
            $locale = session('locale');
        }

        // 3. Browser language detection (first visit — session not yet set)
        if (! $locale && ! session()->has('locale')) {
            $locale = $this->detectFromBrowser($request);
        }

        if ($locale && in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    private function detectFromBrowser(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language', '');
        if (! $acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header (e.g. "th-TH,th;q=0.9,en;q=0.8")
        $parts = explode(',', $acceptLanguage);
        foreach ($parts as $part) {
            $lang = strtolower(trim(explode(';', $part)[0]));
            // Match on 2-char prefix
            $prefix = substr($lang, 0, 2);
            if (in_array($prefix, self::SUPPORTED, true)) {
                return $prefix;
            }
        }

        return null;
    }
}
