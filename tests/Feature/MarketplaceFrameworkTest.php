<?php

namespace Tests\Feature;

use App\Marketplace\AppManager;
use App\Marketplace\AppRegistry;
use App\Marketplace\Events\AppDisabled;
use App\Marketplace\Events\AppInstalled;
use App\Marketplace\Manifest;
use App\Models\Merchant;
use App\Models\MerchantApp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/** PLATFORM-002 Part 1 — Marketplace framework (registry, manifest, lifecycle). */
class MarketplaceFrameworkTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_registry_builds_manifests_from_legacy_config_entries(): void
    {
        $registry = app(AppRegistry::class);

        $this->assertTrue($registry->has('commerce'));
        $manifest = $registry->get('commerce');
        $this->assertTrue($manifest->isAvailable());
        $this->assertNotEmpty($manifest->version);

        // Legacy two-field entries still produce complete manifests.
        $legacy = $registry->get('crm');
        $this->assertSame('coming_soon', $legacy->status);
        $this->assertSame([], $legacy->dependencies);
    }

    public function test_registry_health_reports_every_app(): void
    {
        $health = app(AppRegistry::class)->health();

        $this->assertNotEmpty($health);
        foreach ($health as $row) {
            $this->assertArrayHasKey('healthy', $row);
            $this->assertTrue($row['healthy'], $row['key'] . ': ' . implode('; ', $row['problems']));
        }
    }

    public function test_install_creates_state_row_and_dispatches_event(): void
    {
        Event::fake([AppInstalled::class]);

        app(AppManager::class)->install($this->merchant, 'commerce');

        $this->assertTrue($this->merchant->fresh()->hasApp('commerce'));
        $this->assertDatabaseHas('merchant_apps', [
            'merchant_id' => $this->merchant->id,
            'app_key'     => 'commerce',
            'enabled'     => true,
        ]);
        Event::assertDispatched(AppInstalled::class, fn ($e) => $e->appKey === 'commerce');
    }

    public function test_dependency_must_be_installed_first(): void
    {
        $registry = app(AppRegistry::class);
        $registry->register(new Manifest(
            key: 'needs_commerce', icon: 'bi-x', status: 'available', version: '1.0.0',
            category: 'test', dependencies: ['commerce'], permissions: [], featureFlags: [],
            defaultConfig: [], navigation: [], provider: null, migrationsPath: null, seeder: null, docs: null,
        ));

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(AppManager::class)->install($this->merchant, 'needs_commerce');
    }

    public function test_uninstall_blocked_while_dependents_installed(): void
    {
        $registry = app(AppRegistry::class);
        $registry->register(new Manifest(
            key: 'needs_commerce', icon: 'bi-x', status: 'available', version: '1.0.0',
            category: 'test', dependencies: ['commerce'], permissions: [], featureFlags: [],
            defaultConfig: [], navigation: [], provider: null, migrationsPath: null, seeder: null, docs: null,
        ));

        $manager = app(AppManager::class);
        $manager->install($this->merchant, 'commerce');
        $manager->install($this->merchant->fresh(), 'needs_commerce');

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $manager->uninstall($this->merchant->fresh(), 'commerce');
    }

    public function test_disable_gates_access_but_keeps_install_and_data(): void
    {
        Event::fake([AppDisabled::class]);
        $manager = app(AppManager::class);
        $manager->install($this->merchant, 'commerce');

        $manager->disable($this->merchant->fresh(), 'commerce');

        $fresh = $this->merchant->fresh();
        $this->assertContains('commerce', $fresh->installedApps()); // still installed
        $this->assertFalse($fresh->hasApp('commerce'));             // access gated
        Event::assertDispatched(AppDisabled::class);

        // Route gate follows hasApp
        $this->actingAs($this->user->fresh())
            ->get(route('commerce.products.index', absolute: false))
            ->assertForbidden();

        $manager->enable($fresh, 'commerce');
        $this->assertTrue($this->merchant->fresh()->hasApp('commerce'));
    }

    public function test_pre_marketplace_merchants_without_state_rows_stay_enabled(): void
    {
        // Legacy path: settings JSON only, no merchant_apps row.
        $this->merchant->update(['settings' => ['installed_apps' => ['commerce']]]);

        $this->assertSame(0, MerchantApp::count());
        $this->assertTrue($this->merchant->fresh()->hasApp('commerce'));
    }

    public function test_configuration_merges_defaults_and_overrides(): void
    {
        $manager = app(AppManager::class);
        $manager->install($this->merchant, 'commerce');

        $manager->configure($this->merchant, 'commerce', ['receipt_note' => 'Thanks!']);

        $config = $manager->configFor($this->merchant->fresh(), 'commerce');
        $this->assertSame('Thanks!', $config['receipt_note']);
    }

    public function test_toggle_endpoint_disables_and_enables(): void
    {
        app(AppManager::class)->install($this->merchant, 'commerce');

        $this->actingAs($this->user)->post(route('apps.toggle'), ['app' => 'commerce'])
            ->assertRedirect(route('apps.index', absolute: false));
        $this->assertFalse($this->merchant->fresh()->hasApp('commerce'));

        $this->actingAs($this->user->fresh())->post(route('apps.toggle'), ['app' => 'commerce']);
        $this->assertTrue($this->merchant->fresh()->hasApp('commerce'));
    }

    public function test_merchant_app_states_are_tenant_scoped(): void
    {
        $otherOwner = User::factory()->create(['email_verified_at' => now()]);
        $other = Merchant::factory()->create(['user_id' => $otherOwner->id]);

        app(AppManager::class)->install($this->merchant, 'commerce');

        $this->assertFalse($other->fresh()->hasApp('commerce'));
        $this->assertSame(0, MerchantApp::where('merchant_id', $other->id)->count());
    }
}
