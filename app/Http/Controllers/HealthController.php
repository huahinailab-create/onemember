<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status'      => 'ok',
            'app'         => config('app.name'),
            'environment' => config('app.env'),
            'timestamp'   => now()->toIso8601String(),
            'version'     => config('app.version'),
        ]);
    }
}
