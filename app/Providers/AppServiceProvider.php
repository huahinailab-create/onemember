<?php

namespace App\Providers;

use App\Contracts\InsightProviderInterface;
use App\Services\Intelligence\RuleBasedInsightProvider;
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
    }
}
