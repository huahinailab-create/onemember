<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class MerchantAcquisitionTest extends TestCase
{
    use RefreshDatabase;

    // ── AC-1: Landing page ───────────────────────────────
    // The corporate home is at onemember.co (not app.onemember.co).

    private function corporateHome(): string
    {
        return 'http://' . config('domains.corporate') . '/';
    }

    public function test_landing_page_renders_for_guests(): void
    {
        $response = $this->get($this->corporateHome());
        $response->assertOk();
    }

    public function test_landing_page_shows_brand_logo_text(): void
    {
        $response = $this->get($this->corporateHome());
        $response->assertSee('one', false);
        $response->assertSee('member', false);
    }

    public function test_landing_page_shows_register_cta_for_guests(): void
    {
        $response = $this->get($this->corporateHome());
        // CTAs link to app.onemember.co/register (absolute, via $appUrl View Composer)
        $response->assertSee('https://' . config('domains.app') . '/register', false);
    }

    public function test_landing_page_shows_dashboard_link_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get($this->corporateHome());
        // Nav "Go to Dashboard" links to app.onemember.co/dashboard
        $response->assertSee('https://' . config('domains.app') . '/dashboard', false);
    }

    public function test_landing_page_shows_trial_badge(): void
    {
        App::setLocale('en');
        $response = $this->get($this->corporateHome());
        $response->assertSee('30-Day Free Trial', false);
    }

    public function test_landing_page_shows_no_credit_card_text(): void
    {
        App::setLocale('en');
        $response = $this->get($this->corporateHome());
        $response->assertSee('No credit card required', false);
    }

    // ── AC-2: Guest layout logo ──────────────────────────

    public function test_login_page_shows_brand_text_logo(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();
        $response->assertSee('#FF1585', false);
    }

    public function test_register_page_shows_brand_text_logo(): void
    {
        $response = $this->get(route('register'));
        $response->assertOk();
        $response->assertSee('#FF1585', false);
    }

    // ── AC-3: Register page trial strip ─────────────────

    public function test_register_page_shows_trial_strip_in_english(): void
    {
        App::setLocale('en');
        $response = $this->get(route('register'));
        $response->assertSee(__('auth.trial_heading'));
        $response->assertSee(__('auth.trial_tick_2'));
    }

    public function test_register_page_shows_trial_strip_in_thai(): void
    {
        App::setLocale('th');
        $response = $this->get(route('register'));
        $response->assertSee(__('auth.trial_heading'));
    }

    // ── AC-4: Onboarding finish trial panel ─────────────

    public function test_onboarding_finish_shows_trial_started_panel(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'trial_ends_at'           => now()->addDays(30),
        ]);

        App::setLocale('en');
        $response = $this->actingAs($user)->get(route('onboarding.finish'));
        $response->assertOk();
        $response->assertSee(__('onboarding.trial_started_heading'));
    }

    public function test_onboarding_finish_shows_trial_end_date(): void
    {
        $user = User::factory()->create();
        $trialEnd = now()->addDays(30);
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'trial_ends_at'           => $trialEnd,
        ]);

        $response = $this->actingAs($user)->get(route('onboarding.finish'));
        $response->assertOk();
        $response->assertSee($trialEnd->translatedFormat('j F Y'), false);
    }

    // ── AC-5 / AC-6: Lang keys exist in both locales ────

    public function test_welcome_lang_keys_exist_in_english(): void
    {
        App::setLocale('en');
        $this->assertNotEmpty(__('welcome.hero_headline'));
        $this->assertNotEmpty(__('welcome.cta_primary'));
        $this->assertNotEmpty(__('welcome.no_credit_card'));
        $this->assertNotEmpty(__('welcome.trial_badge'));
    }

    public function test_welcome_lang_keys_exist_in_thai(): void
    {
        App::setLocale('th');
        $this->assertNotEmpty(__('welcome.hero_headline'));
        $this->assertStringContainsString('ลูกค้า', __('welcome.hero_sub'));
    }

    public function test_auth_trial_keys_exist_in_both_locales(): void
    {
        App::setLocale('en');
        $this->assertNotEmpty(__('auth.trial_badge'));
        $this->assertNotEmpty(__('auth.trial_heading'));
        $this->assertNotEmpty(__('auth.trial_tick_1'));
        $this->assertNotEmpty(__('auth.trial_tick_2'));
        $this->assertNotEmpty(__('auth.trial_tick_3'));

        App::setLocale('th');
        $this->assertNotEmpty(__('auth.trial_badge'));
        $this->assertNotEmpty(__('auth.trial_heading'));
    }

    public function test_onboarding_trial_keys_exist_in_both_locales(): void
    {
        App::setLocale('en');
        $this->assertNotEmpty(__('onboarding.trial_confidence_badge'));
        $this->assertNotEmpty(__('onboarding.trial_started_heading'));
        $this->assertNotEmpty(__('onboarding.trial_ends_on'));
        $this->assertNotEmpty(__('onboarding.trial_after_note'));

        App::setLocale('th');
        $this->assertNotEmpty(__('onboarding.trial_confidence_badge'));
        $this->assertNotEmpty(__('onboarding.trial_started_heading'));
    }

    public function test_onboarding_welcome_shows_trial_confidence_badge(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => null,
        ]);

        App::setLocale('en');
        $response = $this->actingAs($user)->get(route('onboarding.welcome'));
        $response->assertOk();
        $response->assertSee(__('onboarding.trial_confidence_badge'));
    }
}
