<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CORE-002 — gates App routes on the merchant having installed the App.
 * Usage: ->middleware('app.installed:commerce')
 */
class EnsureAppInstalled
{
    public function handle(Request $request, Closure $next, string $appKey): Response
    {
        $merchant = $request->user()?->merchant;

        if (! $merchant || ! $merchant->hasApp($appKey)) {
            abort(403, __('apps.error_not_installed'));
        }

        return $next($request);
    }
}
