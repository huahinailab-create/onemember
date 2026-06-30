<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileExperienceTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────

    private function actingAsMerchant(array $settings = []): array
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                => $user->id,
            'onboarding_completed_at' => now(),
            'settings'               => array_merge([
                'locale'                   => 'en',
                'timezone'                 => 'Asia/Bangkok',
                'currency'                 => 'THB',
                'default_expiration_type'  => 'none',
                'default_expiration_value' => null,
                'email_notifications'      => [],
                'counter_mode'             => false,
            ], $settings),
        ]);
        $this->actingAs($user);
        return compact('user', 'merchant');
    }

    // ── Counter Mode toggle ───────────────────────────────────

    public function test_counter_mode_can_be_enabled(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant(['counter_mode' => false]);

        $this->put(route('counter-mode.toggle'))
            ->assertRedirect();

        $this->assertTrue((bool) $merchant->fresh()->settings['counter_mode']);
    }

    public function test_counter_mode_can_be_disabled(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant(['counter_mode' => true]);

        $this->put(route('counter-mode.toggle'))
            ->assertRedirect();

        $this->assertFalse((bool) $merchant->fresh()->settings['counter_mode']);
    }

    public function test_counter_mode_toggle_requires_auth(): void
    {
        $this->put(route('counter-mode.toggle'))
            ->assertRedirect(route('login'));
    }

    public function test_counter_mode_toggle_requires_merchant(): void
    {
        $user = User::factory()->create(); // no merchant
        $this->actingAs($user);

        $this->put(route('counter-mode.toggle'))
            ->assertForbidden();
    }

    public function test_counter_mode_persists_across_requests(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant(['counter_mode' => false]);

        $this->put(route('counter-mode.toggle'));
        $this->assertTrue((bool) $merchant->fresh()->settings['counter_mode']);

        $this->put(route('counter-mode.toggle'));
        $this->assertFalse((bool) $merchant->fresh()->settings['counter_mode']);
    }

    public function test_counter_mode_toggle_does_not_erase_other_settings(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant([
            'counter_mode' => false,
            'locale'       => 'th',
        ]);

        $this->put(route('counter-mode.toggle'));

        $fresh = $merchant->fresh();
        $this->assertTrue((bool) $fresh->settings['counter_mode']);
        $this->assertSame('th', $fresh->settings['locale']);
    }

    public function test_counter_mode_analytics_tracked(): void
    {
        $analytics = $this->mock(AnalyticsService::class);
        $analytics->shouldReceive('track')
            ->once()
            ->withArgs(fn ($event, $props) => $event === 'feature_used' && $props['feature'] === 'counter_mode');

        $this->actingAsMerchant(['counter_mode' => false]);
        $this->put(route('counter-mode.toggle'));
    }

    // ── Layout — counter mode bar ─────────────────────────────

    public function test_counter_mode_bar_shown_when_enabled(): void
    {
        $this->actingAsMerchant(['counter_mode' => true]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('counter-mode-bar', false);
    }

    public function test_counter_mode_bar_not_shown_when_disabled(): void
    {
        $this->actingAsMerchant(['counter_mode' => false]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('counter-mode-bar', false);
    }

    public function test_counter_mode_bar_contains_find_member_link(): void
    {
        $this->actingAsMerchant(['counter_mode' => true]);

        $this->get(route('dashboard'))
            ->assertSee(route('members'), false);
    }

    public function test_counter_mode_bar_contains_add_member_link(): void
    {
        $this->actingAsMerchant(['counter_mode' => true]);

        $this->get(route('dashboard'))
            ->assertSee(route('members.create'), false);
    }

    // ── Layout — PWA meta tags ────────────────────────────────

    public function test_layout_includes_theme_color_meta(): void
    {
        $this->actingAsMerchant();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('name="theme-color"', false);
    }

    public function test_layout_includes_manifest_link(): void
    {
        $this->actingAsMerchant();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('manifest.webmanifest', false);
    }

    public function test_layout_includes_apple_mobile_web_app_meta(): void
    {
        $this->actingAsMerchant();

        $response = $this->get(route('dashboard'));
        $response->assertOk()
            ->assertSee('apple-mobile-web-app-capable', false);
    }

    // ── Layout — sidebar backdrop ─────────────────────────────

    public function test_layout_includes_mobile_backdrop(): void
    {
        $this->actingAsMerchant();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('sidebar-backdrop', false);
    }

    // ── FAB component ─────────────────────────────────────────

    public function test_fab_shown_for_authenticated_merchant(): void
    {
        $this->actingAsMerchant();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('fab', false);
    }

    public function test_fab_links_to_add_member(): void
    {
        $this->actingAsMerchant();

        $this->get(route('dashboard'))
            ->assertSee(route('members.create'), false);
    }

    // ── Form improvements ─────────────────────────────────────

    public function test_member_create_phone_input_is_tel_type(): void
    {
        $this->actingAsMerchant();

        $this->get(route('members.create'))
            ->assertOk()
            ->assertSee('type="tel"', false)
            ->assertSee('inputmode="numeric"', false);
    }

    public function test_member_create_email_input_has_autocomplete(): void
    {
        $this->actingAsMerchant();

        $this->get(route('members.create'))
            ->assertOk()
            ->assertSee('autocomplete="email"', false);
    }

    // ── Topbar counter mode button ────────────────────────────

    public function test_topbar_shows_counter_mode_button(): void
    {
        $this->actingAsMerchant();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('topbar-counter-btn', false);
    }

    public function test_topbar_counter_button_active_when_enabled(): void
    {
        $this->actingAsMerchant(['counter_mode' => true]);

        $this->get(route('dashboard'))
            ->assertSee('topbar-counter-btn active', false);
    }

    public function test_topbar_counter_button_not_active_when_disabled(): void
    {
        $this->actingAsMerchant(['counter_mode' => false]);

        $this->get(route('dashboard'))
            ->assertDontSee('topbar-counter-btn active', false);
    }
}
