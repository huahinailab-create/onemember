<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\TermsAcceptance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function onboardingUser(): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $merchant = Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => ['onboarding_step' => 2],
        ]);

        return [$user, $merchant];
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'currency'    => 'MYR',
            'timezone'    => 'Asia/Kuala_Lumpur',
            'date_format' => 'DD/MM/YYYY',
            'locale'      => 'en',
            'country'     => 'MY',
            'terms'       => 1,
        ], $overrides);
    }

    // ── Country / global settings ────────────────────────────────────────

    public function test_onboarding_saves_country_currency_timezone_language(): void
    {
        [$user, $merchant] = $this->onboardingUser();

        $this->actingAs($user)
            ->post(route('onboarding.business-settings.store'), $this->payload())
            ->assertRedirect(route('onboarding.loyalty', absolute: false));

        $merchant->refresh();
        $this->assertSame('MY', $merchant->country);
        $this->assertSame('MYR', $merchant->currency);
        $this->assertSame('Asia/Kuala_Lumpur', $merchant->timezone);
        $this->assertSame('en', $merchant->settings['locale']);
    }

    public function test_onboarding_rejects_unknown_country(): void
    {
        [$user] = $this->onboardingUser();

        $this->actingAs($user)
            ->post(route('onboarding.business-settings.store'), $this->payload(['country' => 'XX']))
            ->assertSessionHasErrors(['country']);
    }

    public function test_onboarding_settings_page_shows_country_and_terms(): void
    {
        [$user] = $this->onboardingUser();

        $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('onboarding.business-settings', absolute: false))
            ->assertOk()
            ->assertSee('name="country"', false)
            ->assertSee('name="terms"', false)
            ->assertSee(__('onboarding.terms_draft_note', [], 'en'));
    }

    // ── Terms acceptance ─────────────────────────────────────────────────

    public function test_terms_acceptance_is_required(): void
    {
        [$user] = $this->onboardingUser();

        $this->actingAs($user)
            ->post(route('onboarding.business-settings.store'), $this->payload(['terms' => null]))
            ->assertSessionHasErrors(['terms']);

        $this->assertSame(0, TermsAcceptance::count());
    }

    public function test_terms_acceptance_is_recorded_versioned(): void
    {
        [$user, $merchant] = $this->onboardingUser();

        $this->actingAs($user)->post(route('onboarding.business-settings.store'), $this->payload());

        $this->assertDatabaseHas('terms_acceptances', [
            'user_id'     => $user->id,
            'merchant_id' => $merchant->id,
            'document'    => 'merchant-terms-bundle',
            'version'     => config('countries.terms_version'),
        ]);
    }

    public function test_terms_acceptances_are_append_only(): void
    {
        [$user, $merchant] = $this->onboardingUser();
        $this->actingAs($user)->post(route('onboarding.business-settings.store'), $this->payload());

        $this->expectException(\LogicException::class);
        TermsAcceptance::first()->update(['version' => 'tampered']);
    }

    // ── Merchant settings ────────────────────────────────────────────────

    public function test_merchant_can_change_country_in_settings(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'country'                 => 'TH',
        ]);

        $this->actingAs($user)->put(route('settings.preferences.update'), [
            'currency'                    => 'SGD',
            'timezone'                    => 'Asia/Singapore',
            'date_format'                 => 'DD/MM/YYYY',
            'default_expiration_type'     => 'never',
            'default_birthday_enabled'    => 1,
            'locale'                      => 'en',
            'email_product_updates'       => 1,
            'email_tips'                  => 1,
            'email_feature_announcements' => 1,
            'country'                     => 'SG',
        ])->assertRedirect();

        $this->assertSame('SG', $merchant->fresh()->country);
    }

    // ── Free plan: 100 members (DECISION-081) ────────────────────────────

    public function test_free_plan_member_limit_is_100(): void
    {
        $this->assertSame(100, config('subscriptions.plans.free.limits.members'));
    }
}
