<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * OVERNIGHT-001 P1 — deployment verification. Asserts the routes/config that
 * a deploy must expose are actually registered, so a missing route or a
 * route-cache regression is caught in CI *before* it ships (the "new admin
 * page not visible after deploy" class of bug).
 */
class DeploymentIntegrityTest extends TestCase
{
    /**
     * Every named route the app and its navigation depend on. If any of these
     * disappears (rename, accidental removal, domain-group mistake), CI fails.
     */
    public function test_critical_named_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes()->getRoutesByName())->keys();

        $critical = [
            // Admin control surfaces (the reported deploy risk)
            'admin.dashboard', 'admin.control-room', 'admin.go-live',
            'admin.merchants.index', 'admin.merchants.show', 'admin.merchants.extend-trial',
            // Merchant core
            'dashboard', 'members', 'members.create', 'members.store', 'members.show',
            'campaigns.index', 'rewards', 'transactions', 'reports', 'settings',
            'counter', 'launch-kit', 'apps.index', 'apps.install', 'apps.uninstall',
            // Identity (PH2-001A)
            'identity.card', 'members.identity.add', 'members.identity.resolve', 'members.identity.join',
            // Commerce (APP-001/002/003)
            'commerce.products.index', 'commerce.products.create', 'commerce.orders.index',
            'commerce.settings', 'storefront.show', 'storefront.order.store', 'storefront.order.show',
            // Public
            'join.show', 'portal.show', 'corporate.home', 'corporate.terms',
            'onboarding.finish', 'onboarding.business-settings.store',
        ];

        $missing = array_values(array_diff($critical, $routes->all()));

        $this->assertSame([], $missing, 'Missing critical routes: ' . implode(', ', $missing));
    }

    /**
     * `php artisan route:cache` must succeed cleanly — if it ever fails on
     * deploy (e.g. a serialization-breaking route), a stale cache can leave
     * new routes invisible. This runs the real command in an isolated cache.
     */
    public function test_route_cache_command_succeeds(): void
    {
        $exit = $this->artisan('route:cache')->run();
        $this->artisan('route:clear')->run();

        $this->assertSame(0, $exit, 'route:cache failed — deploys may serve a stale route cache.');
    }

    public function test_admin_routes_are_bound_to_app_domain(): void
    {
        $control = collect(Route::getRoutes()->getRoutesByName())->get('admin.control-room');

        $this->assertNotNull($control);
        $this->assertSame(config('domains.app'), $control->getDomain(),
            'Admin routes must be on the app domain group, else they 404 after deploy.');
    }
}
