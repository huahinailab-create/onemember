<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\CounterModeController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DataManagementController;
use App\Http\Controllers\MerchantProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Health check — no auth required, returns JSON for uptime monitors
Route::get('/up', HealthController::class)->name('health');

// ── Customer self-service portal (public, no auth) ────────────────────────
Route::get('/member/{publicUuid}',      [CustomerPortalController::class, 'show'])->name('portal.show');
Route::get('/member/{publicUuid}/card', [CustomerPortalController::class, 'card'])->name('portal.card');
Route::get('/member/{publicUuid}/qr.svg', [CustomerPortalController::class, 'qrSvg'])->name('portal.qr');

// ── Corporate website (public) ────────────────────────────────────────────
Route::get('/',            [CorporateController::class, 'home'])->name('corporate.home');
Route::get('/solutions',   [CorporateController::class, 'solutions'])->name('corporate.solutions');
Route::get('/industries',  [CorporateController::class, 'industries'])->name('corporate.industries');
Route::get('/features',    [CorporateController::class, 'features'])->name('corporate.features');
Route::get('/pricing',     [CorporateController::class, 'pricing'])->name('corporate.pricing');
Route::get('/about',       [CorporateController::class, 'about'])->name('corporate.about');
Route::get('/security',    [CorporateController::class, 'security'])->name('corporate.security');
Route::get('/contact',     [CorporateController::class, 'contact'])->name('corporate.contact');
Route::get('/faq',         [CorporateController::class, 'faq'])->name('corporate.faq');
Route::get('/resources',   [CorporateController::class, 'resources'])->name('corporate.resources');
Route::get('/blog',        [CorporateController::class, 'blog'])->name('corporate.blog');
Route::get('/careers',     [CorporateController::class, 'careers'])->name('corporate.careers');
Route::get('/partners',    [CorporateController::class, 'partners'])->name('corporate.partners');
Route::get('/demo',        [CorporateController::class, 'demo'])->name('corporate.demo');
Route::get('/privacy',     [CorporateController::class, 'privacy'])->name('corporate.privacy');
Route::get('/terms',       [CorporateController::class, 'terms'])->name('corporate.terms');

// LEGACY: old welcome route redirected to corporate home (kept for backwards-compat during transition)
Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/',                [OnboardingController::class, 'index'])->name('index');
    Route::get('/welcome',         [OnboardingController::class, 'welcome'])->name('welcome');
    Route::get('/skip',            [OnboardingController::class, 'skip'])->name('skip');
    Route::get('/business-info',   [OnboardingController::class, 'businessInfo'])->name('business-info');
    Route::post('/business-info',  [OnboardingController::class, 'storeBusinessInfo'])->name('business-info.store');
    Route::get('/business-settings',  [OnboardingController::class, 'businessSettings'])->name('business-settings');
    Route::post('/business-settings', [OnboardingController::class, 'storeBusinessSettings'])->name('business-settings.store');
    Route::get('/loyalty',   [OnboardingController::class, 'loyaltyPreference'])->name('loyalty');
    Route::post('/loyalty',  [OnboardingController::class, 'storeLoyaltyPreference'])->name('loyalty.store');
    Route::get('/quick-start',  [OnboardingController::class, 'quickStart'])->name('quick-start');
    Route::post('/quick-start', [OnboardingController::class, 'storeQuickStart'])->name('quick-start.store');
    Route::get('/finish',  [OnboardingController::class, 'finish'])->name('finish');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('password.confirm')
        ->name('profile.destroy');

    // Feedback
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    // Subscription Centre
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/portal', [SubscriptionController::class, 'portal'])->name('subscription.portal');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');
    Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/downgrade', [SubscriptionController::class, 'downgrade'])->name('subscription.downgrade');

    // Settings (replaces Merchant Profile)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');

    // Counter Mode toggle
    Route::put('/settings/counter-mode', [CounterModeController::class, 'toggle'])->name('counter-mode.toggle');

    // Data Management
    Route::get('/settings/data/import/members',         [DataManagementController::class, 'importForm'])->name('data.import.form');
    Route::post('/settings/data/import/members/upload', [DataManagementController::class, 'importUpload'])->name('data.import.upload');
    Route::post('/settings/data/import/members/preview',[DataManagementController::class, 'importPreview'])->name('data.import.preview');
    Route::post('/settings/data/import/members/execute',[DataManagementController::class, 'importExecute'])->name('data.import.execute');
    Route::get('/settings/data/export/{type}',          [DataManagementController::class, 'export'])->name('data.export');

    // Legacy redirect — keeps old links working
    Route::get('/merchant/profile', fn () => redirect()->route('settings'))->name('merchant.profile.edit');
    Route::put('/merchant/profile', [MerchantProfileController::class, 'update'])->name('merchant.profile.update');

    Route::get('/members', [MemberController::class, 'index'])->name('members');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'archive'])->name('members.archive');
    Route::post('/members/{member}/purchases', [PurchaseController::class, 'store'])->name('members.purchases.store');
    Route::post('/members/{member}/redemptions', [RedemptionController::class, 'store'])->name('members.redemptions.store');
    // Member portal controls
    Route::put('/members/{member}/portal/toggle',      [CustomerPortalController::class, 'togglePortal'])->name('members.portal.toggle');
    Route::post('/members/{member}/portal/regenerate', [CustomerPortalController::class, 'regenerateQr'])->name('members.portal.regenerate');

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

// Stripe webhook — no auth middleware, signature-verified instead
Route::post('/stripe/webhook', [SubscriptionController::class, 'webhook'])->name('stripe.webhook');

require __DIR__.'/auth.php';
