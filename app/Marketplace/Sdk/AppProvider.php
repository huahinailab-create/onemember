<?php

namespace App\Marketplace\Sdk;

use Illuminate\Support\ServiceProvider;

/**
 * PLATFORM-002 Part 2 — Plugin SDK base provider.
 *
 * Every OneMember App ships one AppProvider subclass, declared in its
 * manifest (`provider` key in config/apps.php). MarketplaceServiceProvider
 * registers it at boot. The base class wires the common surfaces so an app
 * only overrides what it uses:
 *
 *   key()               required — the manifest key
 *   routesFile()        web routes, auto-gated by app.installed:{key}
 *   translationsPath()  lang files, namespaced "{key}::"
 *   navigation()        sidebar items [{route, icon, label(lang key)}]
 *   menus()             alias surface for future non-sidebar menus
 *   permissions()       ability strings the app introduces
 *   policies()          [ModelClass => PolicyClass]
 *   events()            [EventClass => [ListenerClass, ...]]
 *   widgets()           dashboard widget view names
 *   dashboardCards()    [{view, sort}] merchant-dashboard cards
 *   settingsSchema()    [{key, type, label, default}] per-merchant config
 *
 * Migrations are declared in the manifest (`migrations` path) and loaded by
 * MarketplaceServiceProvider so `php artisan migrate` stays the single
 * migration entry point. Seeders are declared via the manifest `seeder`.
 *
 * Full guide: docs/dev/plugin-sdk.md
 */
abstract class AppProvider extends ServiceProvider
{
    /** The manifest key this provider belongs to (e.g. 'queue'). */
    abstract public function key(): string;

    public function boot(): void
    {
        if ($file = $this->routesFile()) {
            $this->loadRoutesFrom($file);
        }

        if ($path = $this->translationsPath()) {
            $this->loadTranslationsFrom($path, $this->key());
        }

        foreach ($this->policies() as $model => $policy) {
            \Illuminate\Support\Facades\Gate::policy($model, $policy);
        }

        foreach ($this->events() as $event => $listeners) {
            foreach ((array) $listeners as $listener) {
                \Illuminate\Support\Facades\Event::listen($event, $listener);
            }
        }
    }

    /** Absolute path to the app's web routes file, or null. */
    public function routesFile(): ?string
    {
        return null;
    }

    /** Absolute path to the app's lang directory, or null. */
    public function translationsPath(): ?string
    {
        return null;
    }

    /** @return list<array{route:string,icon:string,label:string}> */
    public function navigation(): array
    {
        return [];
    }

    /** @return list<array{route:string,icon:string,label:string}> */
    public function menus(): array
    {
        return $this->navigation();
    }

    /** @return list<string> ability strings, e.g. 'queue.tickets.manage' */
    public function permissions(): array
    {
        return [];
    }

    /** @return array<class-string, class-string> model => policy */
    public function policies(): array
    {
        return [];
    }

    /** @return array<class-string, class-string|list<class-string>> event => listeners */
    public function events(): array
    {
        return [];
    }

    /** @return list<string> Blade view names rendered as dashboard widgets */
    public function widgets(): array
    {
        return [];
    }

    /** @return list<array{view:string,sort:int}> merchant dashboard cards */
    public function dashboardCards(): array
    {
        return [];
    }

    /** @return list<array{key:string,type:string,label:string,default:mixed}> */
    public function settingsSchema(): array
    {
        return [];
    }
}
