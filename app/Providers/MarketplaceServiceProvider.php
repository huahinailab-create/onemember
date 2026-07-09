<?php

namespace App\Providers;

use App\Marketplace\AppRegistry;
use App\Marketplace\Manifest;
use Illuminate\Support\ServiceProvider;

/**
 * PLATFORM-002 Part 1/2 — boots the Marketplace.
 *
 * - Registers the AppRegistry singleton.
 * - For every manifest that declares migrations, loads them into the normal
 *   `php artisan migrate` run (no separate migration channel to operate).
 * - Boots each app's SDK AppProvider (routes/translations/policies) when
 *   one is declared. Apps stay first-party modules inside the monolith
 *   (ADR-012); a future third-party app plugs in through this same path.
 */
class MarketplaceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AppRegistry::class, fn () => new AppRegistry());
    }

    public function boot(): void
    {
        $registry = $this->app->make(AppRegistry::class);

        $registry->all()->each(function (Manifest $manifest) {
            if ($manifest->migrationsPath && is_dir(base_path($manifest->migrationsPath))) {
                $this->loadMigrationsFrom(base_path($manifest->migrationsPath));
            }

            if ($manifest->provider && class_exists($manifest->provider)) {
                $this->app->register($manifest->provider);
            }
        });
    }
}
