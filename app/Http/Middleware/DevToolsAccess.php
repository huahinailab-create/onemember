<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevToolsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $env   = app()->environment();
        $debug = config('app.debug', false);

        $allowed = $env === 'local'
            || $env === 'development'
            || ($debug && auth()->check());

        if (! $allowed) {
            abort(404);
        }

        return $next($request);
    }
}
