<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\MerchantProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/merchant/profile', [MerchantProfileController::class, 'edit'])->name('merchant.profile.edit');
    Route::put('/merchant/profile', [MerchantProfileController::class, 'update'])->name('merchant.profile.update');

    Route::get('/members', [MemberController::class, 'index'])->name('members');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');

    Route::get('/loyalty-programs', fn () => view('coming-soon', [
        'pageTitle' => 'Loyalty Programs',
        'icon'      => 'bi-star',
    ]))->name('loyalty-programs');

    Route::get('/rewards', fn () => view('coming-soon', [
        'pageTitle' => 'Rewards',
        'icon'      => 'bi-gift',
    ]))->name('rewards');

    Route::get('/transactions', fn () => view('coming-soon', [
        'pageTitle' => 'Transactions',
        'icon'      => 'bi-arrow-left-right',
    ]))->name('transactions');

    Route::get('/reports', fn () => view('coming-soon', [
        'pageTitle' => 'Reports',
        'icon'      => 'bi-bar-chart-line',
    ]))->name('reports');
});

require __DIR__.'/auth.php';
