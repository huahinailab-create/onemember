<?php

use App\Apps\Procurement\Http\ProcurementController;
use Illuminate\Support\Facades\Route;

// PLATFORM-002 Part 9 — Procurement App routes (loaded by the SDK provider).
Route::domain(config('domains.app'))
    ->middleware(['web', 'auth', 'verified', 'app.installed:procurement'])
    ->prefix('procurement')
    ->name('procurement.')
    ->group(function () {
        Route::get('/',                             [ProcurementController::class, 'index'])->name('index');
        Route::get('/suppliers',                    fn () => redirect()->route('procurement.index'))->name('suppliers.index');
        Route::post('/suppliers',                   [ProcurementController::class, 'storeSupplier'])->name('suppliers.store');
        Route::post('/suppliers/{supplier}/rate',   [ProcurementController::class, 'rateSupplier'])->name('suppliers.rate');
        Route::post('/requests',                    [ProcurementController::class, 'storeRequest'])->name('requests.store');
        Route::put('/requests/{purchaseRequest}/submit',  [ProcurementController::class, 'submitRequest'])->name('requests.submit');
        Route::put('/requests/{purchaseRequest}/approve', [ProcurementController::class, 'approveRequest'])->name('requests.approve');
        Route::put('/requests/{purchaseRequest}/reject',  [ProcurementController::class, 'rejectRequest'])->name('requests.reject');
        Route::put('/orders/{purchaseOrder}/receive',     [ProcurementController::class, 'receiveOrder'])->name('orders.receive');
    });
