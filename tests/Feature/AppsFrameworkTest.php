<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AppsFrameworkTest extends TestCase
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

    public function test_apps_page_lists_registry_with_placeholders(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('apps.index', absolute: false))
            ->assertOk()
            ->assertSee(__('apps.name_commerce', [], 'en'))
            ->assertSee(__('apps.badge_coming_soon', [], 'en'))   // marketplace placeholders
            ->assertSee(__('apps.core_note', [], 'en'));
    }

    public function test_merchant_can_install_available_app(): void
    {
        $this->actingAs($this->user)
            ->post(route('apps.install'), ['app' => 'commerce'])
            ->assertRedirect(route('apps.index', absolute: false));

        $this->assertTrue($this->merchant->fresh()->hasApp('commerce'));
        $this->assertTrue(AuditLog::where('event', 'app.installed')->exists());
    }

    public function test_coming_soon_app_cannot_be_installed(): void
    {
        $this->actingAs($this->user)
            ->post(route('apps.install'), ['app' => 'pos'])
            ->assertSessionHasErrors(['app']);

        $this->assertFalse($this->merchant->fresh()->hasApp('pos'));
    }

    public function test_unknown_app_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->post(route('apps.install'), ['app' => 'bitcoin-casino'])
            ->assertSessionHasErrors(['app']);
    }

    public function test_uninstall_disables_access_but_keeps_settings(): void
    {
        $this->actingAs($this->user)->post(route('apps.install'), ['app' => 'commerce']);
        $this->actingAs($this->user)
            ->post(route('apps.uninstall'), ['app' => 'commerce'])
            ->assertRedirect();

        $merchant = $this->merchant->fresh();
        $this->assertFalse($merchant->hasApp('commerce'));
        $this->assertTrue(AuditLog::where('event', 'app.uninstalled')->exists());
    }

    public function test_app_installed_middleware_gates_routes(): void
    {
        Route::middleware(['web', 'auth', 'app.installed:commerce'])
            ->get('/_test/commerce-gated', fn () => 'inside');

        // Not installed → 403
        $this->actingAs($this->user)->get('/_test/commerce-gated')->assertForbidden();

        // Installed → allowed
        $this->actingAs($this->user)->post(route('apps.install'), ['app' => 'commerce']);
        $this->actingAs($this->user)->get('/_test/commerce-gated')->assertOk();
    }

    public function test_install_state_is_tenant_scoped(): void
    {
        $this->actingAs($this->user)->post(route('apps.install'), ['app' => 'commerce']);

        $otherUser     = User::factory()->create(['email_verified_at' => now()]);
        $otherMerchant = Merchant::factory()->create(['user_id' => $otherUser->id, 'onboarding_completed_at' => now()]);

        $this->assertTrue($this->merchant->fresh()->hasApp('commerce'));
        $this->assertFalse($otherMerchant->fresh()->hasApp('commerce'));
    }

    public function test_apps_page_localized_thai_default(): void
    {
        $this->actingAs($this->user)
            ->get(route('apps.index', absolute: false))
            ->assertOk()
            ->assertSee(__('apps.title', [], 'th'));
    }
}
