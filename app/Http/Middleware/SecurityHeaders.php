<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Control referrer information sent in requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict access to sensitive browser APIs
        $response->headers->set('Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()'
        );

        // Content-Security-Policy
        // - 'unsafe-inline' required: application has inline <script> blocks and onclick handlers
        // - 'unsafe-inline' for styles required: 117 style="" attributes throughout templates
        // - fonts.bunny.net: Figtree font loaded from CDN in all layouts
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            "img-src 'self' data:",
            "connect-src 'self'",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS — only apply when the connection is HTTPS
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
