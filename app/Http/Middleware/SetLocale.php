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
        // 2. Session (explicit user switch via /locale endpoint)
        // 3. Hard default: Thai (onemember.co is a Thai-first corporate site)
        //
        // Browser Accept-Language detection is intentionally omitted — a Thai SME
        // may use an English-language OS/browser and should still see Thai by default.

        $locale = null;

        // 1. Merchant preference (highest priority)
        if ($request->user()?->merchant?->settings) {
            $locale = $request->user()->merchant->settings['locale'] ?? null;
        }

        // 2. Session preference (explicit switch by user)
        if (! $locale) {
            $locale = session('locale');
        }

        // Always set an explicit locale — never fall through to config('app.locale')
        // which can differ between environments. Thai is the hard default.
        App::setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : self::DEFAULT);

        return $next($request);
    }

}
