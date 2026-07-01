<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevToolsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $flagEnabled = config('devtools.enabled', false);
        $env         = app()->environment();

        $nonProduction = $env !== 'production';
        $allowed       = $nonProduction && $flagEnabled;

        if (! $allowed) {
            abort(404);
        }

        return $next($request);
    }
}
