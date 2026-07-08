<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ControlRoomController as AdminControlRoomController;
use App\Http\Controllers\Admin\GoLiveController as AdminGoLiveController;
use App\Http\Controllers\Admin\MerchantController as AdminMerchantController;
use App\Http\Controllers\Admin\TrialExtensionController as AdminTrialExtensionController;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\Commerce\CommerceSettingsController;
use App\Http\Controllers\Commerce\ProductController as CommerceProductController;
use App\Http\Controllers\Commerce\OrderController as CommerceOrderController;
use App\Http\Controllers\Commerce\PublicOrderController;
use App\Http\Controllers\Commerce\StorefrontController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Identity\IdentityCardController;
use App\Http\Controllers\Identity\MemberIdentityController;
use App\Http\Controllers\JoinLandingController;
use App\Http\Controllers\LaunchKitController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// ── Domain-agnostic routes (respond on ALL domains) ───────────────────────
// These must be registered before domain groups so they match on any host.

Route::get('/up', HealthController::class)->name('health');
Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

// ── www.onemember.co → canonical 301 redirect ─────────────────────────────
Route::domain(config('domains.www'))->group(function () {
    Route::get('/{any?}', function (string $any = '') {
        $path = $any ? '/' . ltrim($any, '/') : '/';
        return redirect('https://' . config('domains.corporate') . $path, 301);
    })->where('any', '.*');
});

// ── onemember.co — Corporate website ─────────────────────────────────────
Route::domain(config('domains.corporate'))->group(function () {
    // Cross-domain redirects: send app-side paths to app.onemember.co
    Route::get('/login',     fn () => redirect()->away('https://' . config('domains.app') . '/login',    301));
    Route::get('/register',  fn () => redirect()->away('https://' . config('domains.app') . '/register', 301));
    Route::get('/dashboard', fn () => redirect()->away('https://' . config('domains.app') . '/dashboard', 302));
    Route::get('/onboarding/{any?}', fn () => redirect()->away('https://' . config('domains.app') . '/onboarding', 302))
        ->where('any', '.*');

    // Corporate pages
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
    Route::get('/pdpa',        [CorporateController::class, 'pdpa'])->name('corporate.pdpa');

    Route::get('/welcome', fn () => view('welcome'));
});

// ── app.onemember.co — Merchant application ───────────────────────────────
Route::domain(config('domains.app'))->group(function () {
    // ── Platform Admin (/admin) — OneMember internal only ────────────────────
    Route::middleware(['auth', 'verified', 'admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::redirect('/', '/admin/dashboard');
            Route::get('/dashboard',              [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/go-live',                [AdminGoLiveController::class, 'index'])->name('go-live');
            Route::get('/control-room',           [AdminControlRoomController::class, 'index'])->name('control-room');
            Route::get('/merchants',              [AdminMerchantController::class,  'index'])->name('merchants.index');
            Route::get('/merchants/{merchant}',   [AdminMerchantController::class,  'show'])->name('merchants.show');
            Route::post('/merchants/{merchant}/extend-trial', [AdminTrialExtensionController::class, 'store'])->name('merchants.extend-trial');
        });

    // Root → merchant login
    Route::get('/', fn () => redirect()->to('/login'));

    // Public per-merchant join landing (RELEASE-5A) — information only,
    // no data collection. QR posters printed from the Launch Kit point here.
    Route::get('/join/{slug}', [JoinLandingController::class, 'show'])->name('join.show');

    // OneMember Card (PH2-001A) — public by unguessable uuid; QR is token-only
    Route::get('/omid/{publicUuid}', [IdentityCardController::class, 'show'])->name('identity.card');

    // Public Merchant Storefront (APP-002) — exists only while Commerce App installed
    Route::get('/store/{slug}', [StorefrontController::class, 'show'])->name('storefront.show');
    Route::post('/store/{slug}/order', [PublicOrderController::class, 'store'])
        ->middleware('throttle:20,10')->name('storefront.order.store');
    Route::get('/store/{slug}/order/{orderUuid}', [PublicOrderController::class, 'show'])->name('storefront.order.show');

    // Customer self-service portal (public, no auth)
    Route::get('/member/{publicUuid}',        [CustomerPortalController::class, 'show'])->name('portal.show');
    Route::get('/member/{publicUuid}/card',   [CustomerPortalController::class, 'card'])->name('portal.card');
    Route::get('/member/{publicUuid}/qr.svg', [CustomerPortalController::class, 'qrSvg'])->name('portal.qr');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    // Onboarding
    Route::middleware(['auth', 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/',                   [OnboardingController::class, 'index'])->name('index');
        Route::get('/welcome',            [OnboardingController::class, 'welcome'])->name('welcome');
        Route::get('/skip',               [OnboardingController::class, 'skip'])->name('skip');
        Route::get('/business-info',      [OnboardingController::class, 'businessInfo'])->name('business-info');
        Route::post('/business-info',     [OnboardingController::class, 'storeBusinessInfo'])->name('business-info.store');
        Route::get('/business-settings',  [OnboardingController::class, 'businessSettings'])->name('business-settings');
        Route::post('/business-settings', [OnboardingController::class, 'storeBusinessSettings'])->name('business-settings.store');
        Route::get('/loyalty',            [OnboardingController::class, 'loyaltyPreference'])->name('loyalty');
        Route::post('/loyalty',           [OnboardingController::class, 'storeLoyaltyPreference'])->name('loyalty.store');
        Route::get('/quick-start',        [OnboardingController::class, 'quickStart'])->name('quick-start');
        Route::post('/quick-start',       [OnboardingController::class, 'storeQuickStart'])->name('quick-start.store');
        Route::get('/finish',             [OnboardingController::class, 'finish'])->name('finish');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->middleware('password.confirm')
            ->name('profile.destroy');

        Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

        Route::get('/subscription',           [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/success',   [SubscriptionController::class, 'success'])->name('subscription.success');
        Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::post('/subscription/portal',   [SubscriptionController::class, 'portal'])->name('subscription.portal');
        Route::post('/subscription/cancel',   [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::post('/subscription/resume',   [SubscriptionController::class, 'resume'])->name('subscription.resume');
        Route::post('/subscription/upgrade',  [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
        Route::post('/subscription/downgrade',[SubscriptionController::class, 'downgrade'])->name('subscription.downgrade');

        Route::get('/settings',              [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile',      [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/preferences',  [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
        Route::put('/settings/localization', [SettingsController::class, 'updateLocalization'])->name('settings.localization.update');
        // OMEGA-001E — live Store URL availability check (Settings > Business Profile)
        Route::get('/settings/store-url/availability', [SettingsController::class, 'checkStoreUrlAvailability'])
            ->name('settings.store-url.availability');

        Route::put('/settings/counter-mode', [CounterModeController::class, 'toggle'])->name('counter-mode.toggle');
        Route::get('/counter',               [CounterModeController::class, 'index'])->name('counter');

        // Commerce App (APP-001) — routes exist only for merchants who installed it
        Route::middleware('app.installed:commerce')->prefix('commerce')->name('commerce.')->group(function () {
            Route::get('/products',                    [CommerceProductController::class, 'index'])->name('products.index');
            Route::get('/products/create',             [CommerceProductController::class, 'create'])->name('products.create');
            Route::post('/products',                   [CommerceProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit',     [CommerceProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}',          [CommerceProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}',       [CommerceProductController::class, 'archive'])->name('products.archive');
            Route::get('/orders',                      [CommerceOrderController::class, 'index'])->name('orders.index');
            Route::put('/orders/{order}/status',       [CommerceOrderController::class, 'updateStatus'])->name('orders.status');
            Route::put('/orders/{order}/paid',         [CommerceOrderController::class, 'markPaid'])->name('orders.paid');
            Route::get('/settings',                    [CommerceSettingsController::class, 'edit'])->name('settings');
            Route::put('/settings',                    [CommerceSettingsController::class, 'update'])->name('settings.update');
        });

        Route::get('/apps',            [AppsController::class, 'index'])->name('apps.index');
        Route::post('/apps/install',   [AppsController::class, 'install'])->name('apps.install');
        Route::post('/apps/uninstall', [AppsController::class, 'uninstall'])->name('apps.uninstall');

        Route::get('/launch-kit',              [LaunchKitController::class, 'index'])->name('launch-kit');
        Route::get('/launch-kit/poster',       [LaunchKitController::class, 'poster'])->name('launch-kit.poster');
        Route::get('/launch-kit/counter-card', [LaunchKitController::class, 'counterCard'])->name('launch-kit.counter-card');
        Route::get('/launch-kit/staff-guide',  [LaunchKitController::class, 'staffGuide'])->name('launch-kit.staff-guide');

        Route::get('/settings/data/import/members',          [DataManagementController::class, 'importForm'])->name('data.import.form');
        Route::post('/settings/data/import/members/upload',  [DataManagementController::class, 'importUpload'])->name('data.import.upload');
        Route::post('/settings/data/import/members/preview', [DataManagementController::class, 'importPreview'])->name('data.import.preview');
        Route::post('/settings/data/import/members/execute', [DataManagementController::class, 'importExecute'])->name('data.import.execute');
        Route::get('/settings/data/export/{type}',           [DataManagementController::class, 'export'])->name('data.export');

        // Legacy URL kept as a redirect for old bookmarks (TD-004: dead
        // controller/view/PUT endpoint removed 2026-07-05).
        Route::get('/merchant/profile', fn () => redirect()->route('settings'))->name('merchant.profile.edit');

        Route::get('/members',              [MemberController::class, 'index'])->name('members');
        Route::get('/members/create',       [MemberController::class, 'create'])->name('members.create');
        Route::post('/members',             [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/onemember/add',      [MemberIdentityController::class, 'addForm'])->name('members.identity.add');
        Route::post('/members/onemember/resolve', [MemberIdentityController::class, 'resolve'])->name('members.identity.resolve');
        Route::post('/members/onemember/join',    [MemberIdentityController::class, 'join'])->name('members.identity.join');
        Route::get('/members/{member}',     [MemberController::class, 'show'])->name('members.show');
        Route::put('/members/{member}',     [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}',  [MemberController::class, 'archive'])->name('members.archive');
        Route::post('/members/{member}/purchases',   [PurchaseController::class, 'store'])->name('members.purchases.store');
        Route::post('/members/{member}/redemptions', [RedemptionController::class, 'store'])->name('members.redemptions.store');
        Route::put('/members/{member}/portal/toggle',      [CustomerPortalController::class, 'togglePortal'])->name('members.portal.toggle');
        Route::post('/members/{member}/portal/regenerate', [CustomerPortalController::class, 'regenerateQr'])->name('members.portal.regenerate');

        Route::get('/campaigns',              [CampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/campaigns/create',       [CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns',             [CampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}',   [CampaignController::class, 'show'])->name('campaigns.show');
        Route::get('/campaigns/{campaign}/analytics', [CampaignController::class, 'analytics'])->name('campaigns.analytics');
        Route::put('/campaigns/{campaign}',   [CampaignController::class, 'update'])->name('campaigns.update');
        Route::put('/campaigns/{campaign}/configure',  [CampaignController::class, 'configure'])->name('campaigns.configure');
        Route::patch('/campaigns/{campaign}/pause',    [CampaignController::class, 'pause'])->name('campaigns.pause');
        Route::delete('/campaigns/{campaign}',         [CampaignController::class, 'archive'])->name('campaigns.archive');

        Route::get('/campaigns/{campaign}/rewards/create',          [RewardController::class, 'create'])->name('campaigns.rewards.create');
        Route::post('/campaigns/{campaign}/rewards',                [RewardController::class, 'store'])->name('campaigns.rewards.store');
        Route::get('/campaigns/{campaign}/rewards/{reward}',        [RewardController::class, 'show'])->name('campaigns.rewards.show');
        Route::put('/campaigns/{campaign}/rewards/{reward}',        [RewardController::class, 'update'])->name('campaigns.rewards.update');
        Route::delete('/campaigns/{campaign}/rewards/{reward}',     [RewardController::class, 'archive'])->name('campaigns.rewards.archive');

        Route::get('/rewards', fn () => view('coming-soon', [
            'pageTitle' => 'Rewards', 'icon' => 'bi-gift',
        ]))->name('rewards');

        Route::get('/transactions', fn () => view('coming-soon', [
            'pageTitle' => 'Transactions', 'icon' => 'bi-arrow-left-right',
        ]))->name('transactions');

        Route::get('/reports', fn () => view('coming-soon', [
            'pageTitle' => 'Reports', 'icon' => 'bi-bar-chart-line',
        ]))->name('reports');
    });

    // Stripe webhook — no auth, signature-verified
    Route::post('/stripe/webhook', [SubscriptionController::class, 'webhook'])->name('stripe.webhook');

    // Auth routes (Breeze) — constrained to app domain
    require __DIR__.'/auth.php';
});
