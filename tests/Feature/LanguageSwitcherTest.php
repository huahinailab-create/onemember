<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function merchantUser(string $locale = 'th'): array
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'settings'                => ['locale' => $locale, 'onboarding_step' => 5],
        ]);
        return [$user, $merchant];
    }

    // ── Guest can switch language ─────────────────────────────────────────────

    public function test_guest_can_switch_to_english(): void
    {
        $response = $this->post(route('locale.switch'), ['locale' => 'en']);
        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }

    public function test_guest_can_switch_to_thai(): void
    {
        $response = $this->post(route('locale.switch'), ['locale' => 'th']);
        $response->assertRedirect();
        $this->assertEquals('th', session('locale'));
    }

    public function test_guest_invalid_locale_is_ignored(): void
    {
        $response = $this->post(route('locale.switch'), ['locale' => 'fr']);
        $response->assertRedirect();
        $this->assertNull(session('locale'));
    }

    // ── Locale persists via session ───────────────────────────────────────────

    public function test_locale_persists_in_session_for_guest(): void
    {
        $this->post(route('locale.switch'), ['locale' => 'en']);
        // Subsequent request should use the session locale
        $this->withSession(['locale' => 'en']);
        $response = $this->get(route('login'));
        $response->assertOk();
        // The session value is the canonical persistence — middleware applies it
        $this->assertEquals('en', session('locale'));
    }

    // ── Authenticated merchant can switch language ────────────────────────────

    public function test_authenticated_merchant_can_switch_to_english(): void
    {
        [$user, $merchant] = $this->merchantUser('th');

        $response = $this->actingAs($user)->post(route('locale.switch'), ['locale' => 'en']);
        $response->assertRedirect();

        $this->assertEquals('en', session('locale'));
        $this->assertEquals('en', $merchant->fresh()->settings['locale']);
    }

    public function test_authenticated_merchant_can_switch_to_thai(): void
    {
        [$user, $merchant] = $this->merchantUser('en');

        $response = $this->actingAs($user)->post(route('locale.switch'), ['locale' => 'th']);
        $response->assertRedirect();

        $this->assertEquals('th', session('locale'));
        $this->assertEquals('th', $merchant->fresh()->settings['locale']);
    }

    // ── Locale preference persists after re-login ─────────────────────────────

    public function test_merchant_locale_is_applied_from_settings(): void
    {
        [$user, $merchant] = $this->merchantUser('en');

        // Merchant settings carry 'en' — middleware reads it and sets locale
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        $this->assertEquals('en', App::getLocale());
    }

    public function test_merchant_thai_locale_is_applied_from_settings(): void
    {
        [$user, $merchant] = $this->merchantUser('th');

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        $this->assertEquals('th', App::getLocale());
    }

    // ── Dashboard renders in both locales ─────────────────────────────────────

    public function test_dashboard_renders_in_english(): void
    {
        [$user] = $this->merchantUser('en');

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        // English nav label from navigation.php
        $response->assertSee(__('navigation.dashboard', [], 'en'), false);
    }

    public function test_dashboard_renders_in_thai(): void
    {
        [$user] = $this->merchantUser('th');

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee(__('navigation.dashboard', [], 'th'), false);
    }

    // ── Language switcher visible in layouts ──────────────────────────────────

    public function test_language_switcher_visible_on_login_page(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();
        // Switcher uses relative /locale action to avoid cross-domain POST issues
        $response->assertSee('action="/locale"', false);
    }

    public function test_language_switcher_visible_on_register_page(): void
    {
        $response = $this->get(route('register'));
        $response->assertOk();
        $response->assertSee('action="/locale"', false);
    }

    public function test_language_switcher_visible_on_corporate_home(): void
    {
        $response = $this->get(route('corporate.home'));
        $response->assertOk();
        $response->assertSee('action="/locale"', false);
    }

    // ── Onboarding business-settings: locale field ───────────────────────────

    public function test_onboarding_business_settings_locale_field_present(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => ['onboarding_step' => 2],
        ]);

        $response = $this->actingAs($user)->get(route('onboarding.business-settings'));
        $response->assertOk();
        $response->assertSee('name="locale"', false);
    }

    public function test_onboarding_business_settings_succeeds_without_explicit_locale(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => ['onboarding_step' => 2],
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            // locale intentionally omitted — request must fill a default
        ]);

        $response->assertRedirect(route('onboarding.loyalty'));
    }

    public function test_onboarding_business_settings_accepts_en_locale(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => ['onboarding_step' => 2],
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            'locale'      => 'en',
        ]);

        $response->assertRedirect(route('onboarding.loyalty'));
    }

    public function test_onboarding_business_settings_accepts_th_locale(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => ['onboarding_step' => 2],
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            'locale'      => 'th',
        ]);

        $response->assertRedirect(route('onboarding.loyalty'));
    }

    public function test_raw_word_locale_does_not_appear_in_validation_errors(): void
    {
        App::setLocale('th');

        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => ['onboarding_step' => 2],
        ]);

        // Submit an invalid locale value to trigger a validation error
        $response = $this->actingAs($user)->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            'locale'      => 'invalid',
        ]);

        $response->assertSessionHasErrors();
        $errors = session('errors')?->all() ?? [];
        foreach ($errors as $error) {
            $this->assertStringNotContainsStringIgnoringCase(
                'กรุณากรอก locale',
                $error,
                'Raw field name "locale" must not appear in validation errors.'
            );
            $this->assertDoesNotMatchRegularExpression(
                '/\blocale\b/',
                $error,
                'Raw field name "locale" must not appear in validation errors.'
            );
        }
    }

    // ── Mobile dropdown positioning regression (iPhone Safari) ────────────────
    //
    // Bootstrap's Popper-based dropdown positioning could open the language
    // menu off the left edge of the viewport on iOS Safari. The fix disables
    // Popper for this dropdown (data-bs-display="static") and lets plain CSS
    // (.lang-switcher-menu, scoped in resources/css/app.css) keep the menu
    // right-aligned with the toggle button inside the viewport below 768px.
    // These assertions guard the markup contract the CSS fix depends on —
    // removing either attribute silently reintroduces the bug.

    public function test_language_switcher_disables_popper_for_static_css_positioning(): void
    {
        $response = $this->get(route('corporate.home'));

        $response->assertOk();
        $response->assertSee('data-bs-display="static"', false);
    }

    public function test_language_switcher_menu_has_mobile_positioning_class(): void
    {
        $response = $this->get(route('corporate.home'));

        $response->assertOk();
        $response->assertSee('lang-switcher-wrap', false);
        $response->assertSee('lang-switcher-menu', false);
    }
}
