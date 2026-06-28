<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RewardController;
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
    Route::delete('/members/{member}', [MemberController::class, 'archive'])->name('members.archive');
    Route::post('/members/{member}/purchases', [PurchaseController::class, 'store'])->name('members.purchases.store');

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::put('/campaigns/{campaign}/configure', [CampaignController::class, 'configure'])->name('campaigns.configure');
    Route::patch('/campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'archive'])->name('campaigns.archive');

    Route::get('/campaigns/{campaign}/rewards/create', [RewardController::class, 'create'])->name('campaigns.rewards.create');
    Route::post('/campaigns/{campaign}/rewards', [RewardController::class, 'store'])->name('campaigns.rewards.store');
    Route::get('/campaigns/{campaign}/rewards/{reward}', [RewardController::class, 'show'])->name('campaigns.rewards.show');
    Route::put('/campaigns/{campaign}/rewards/{reward}', [RewardController::class, 'update'])->name('campaigns.rewards.update');
    Route::delete('/campaigns/{campaign}/rewards/{reward}', [RewardController::class, 'archive'])->name('campaigns.rewards.archive');

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
