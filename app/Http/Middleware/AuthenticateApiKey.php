<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PLATFORM-002 Part 5 — Bearer API-key authentication for /api/v1.
 *
 * Resolves the merchant tenant from the key and binds both to the request
 * (attributes 'api_key' / 'api_merchant'). Errors use the standard API
 * error envelope (docs/dev/public-api.md).
 */
class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $token = $request->bearerToken();

        if (! $token || ! ($key = ApiKey::findByPlaintext($token))) {
            return response()->json([
                'error' => ['code' => 'unauthenticated', 'message' => 'Invalid or missing API key.'],
            ], 401);
        }

        if ($ability && ! $key->can($ability)) {
            return response()->json([
                'error' => ['code' => 'forbidden', 'message' => "API key lacks the '{$ability}' ability."],
            ], 403);
        }

        $key->forceFill(['last_used_at' => now()])->save();

        $request->attributes->set('api_key', $key);
        $request->attributes->set('api_merchant', $key->merchant);

        return $next($request);
    }
}
