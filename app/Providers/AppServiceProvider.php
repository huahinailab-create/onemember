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
    }
}
