<?php

use App\Apps\Queue\Http\QueueTicketController;
use Illuminate\Support\Facades\Route;

// PLATFORM-002 Part 8 — Queue App routes (loaded by QueueAppProvider,
// gated on install + enable via app.installed:queue).
Route::domain(config('domains.app'))
    ->middleware(['web', 'auth', 'verified', 'app.installed:queue'])
    ->prefix('queue')
    ->name('queue.')
    ->group(function () {
        Route::get('/',                 [QueueTicketController::class, 'index'])->name('tickets.index');
        Route::post('/tickets',         [QueueTicketController::class, 'store'])->name('tickets.store');
        Route::put('/tickets/{ticket}', [QueueTicketController::class, 'updateStatus'])->name('tickets.status');
        Route::get('/display',          [QueueTicketController::class, 'display'])->name('display');
        Route::post('/counters',        [QueueTicketController::class, 'storeCounter'])->name('counters.store');
    });
