<?php

namespace App\Providers;

use App\Contracts\InsightProviderInterface;
use App\Services\Intelligence\RuleBasedInsightProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InsightProviderInterface::class, RuleBasedInsightProvider::class);
    }

    public function boot(): void
    {
        // ADR-007: "Campaign" is the product-layer name for LoyaltyProgram.
        // The alias lets new code use the product vocabulary without a
        // schema migration. Both names resolve to the same class.
        if (! class_exists(\App\Models\Campaign::class, false)) {
            class_alias(\App\Models\LoyaltyProgram::class, \App\Models\Campaign::class);
        }

        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        // Share absolute app-domain URL with all corporate views so CTAs
        // link to app.onemember.co without relying on route() which would
        // generate a domain-constrained URL pointing to the wrong domain.
        View::composer(['corporate.*', 'layouts.corporate'], function ($view) {
            $view->with('appUrl', 'https://' . config('domains.app'));
        });

        // TD-003: branding is composed here instead of instantiating the
        // service inside Blade views (keeps views container-free).
        View::composer(['layouts.app', 'settings.index'], function ($view) {
            $view->with('merchantBranding', new \App\Services\MerchantBrandingService(
                \Illuminate\Support\Facades\Auth::user()?->merchant
            ));
        });
    }
}
