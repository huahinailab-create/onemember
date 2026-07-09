<?php

use App\Http\Controllers\Api\V1\MemberApiController;
use Illuminate\Support\Facades\Route;

/*
 * PLATFORM-002 Part 5 — Public API foundation.
 *
 * Conventions (docs/dev/public-api.md):
 * - URL versioning: /api/v1/… ; breaking changes go to /api/v2
 * - Auth: Bearer merchant API key (api.key middleware, ability per route)
 * - Rate limit: 'api' limiter (per key)
 * - Pagination: Laravel resource collections (data/links/meta)
 * - Errors: { "error": { "code", "message" } }
 *
 * Deliberately minimal surface — members read-only is the reference
 * implementation; more resources ship per roadmap, not all at once.
 */
Route::prefix('v1')->name('api.v1.')->middleware('throttle:api')->group(function () {
    // Unauthenticated liveness/version probe
    Route::get('/ping', fn () => response()->json([
        'ok'      => true,
        'version' => 'v1',
        'time'    => now()->toIso8601String(),
    ]))->name('ping');

    Route::middleware('api.key:members:read')->group(function () {
        Route::get('/members',      [MemberApiController::class, 'index'])->name('members.index');
        Route::get('/members/{id}', [MemberApiController::class, 'show'])->name('members.show');
    });
});
