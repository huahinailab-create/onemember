<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\TermsAcceptance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantAgreementTest extends TestCase
{
    use RefreshDatabase;

    public function test_agreement_lists_required_clauses_and_draft_marking(): void
    {
        app()->setLocale('en');
        $html = view('legal.merchant-agreement')->render();

        foreach ([
            'clause_trial', 'clause_free_plan', 'clause_upgrade', 'clause_responsibility',
            'clause_not_merchant_of_record', 'clause_no_funds',
        ] as $key) {
            $this->assertStringContainsString(__("legal.{$key}", [], 'en'), $html);
        }

        $this->assertStringContainsString(__('legal.draft_badge', [], 'en'), $html);
        $this->assertStringContainsString(config('countries.terms_version'), $html);
    }

    public function test_onboarding_shows_agreement_before_acceptance(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Merchant::factory()->create(['user_id' => $user->id, 'settings' => ['onboarding_step' => 2]]);

        $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('onboarding.business-settings', absolute: false))
            ->assertOk()
            ->assertSee(__('legal.agreement_title', [], 'en'))
            ->assertSee(__('legal.clause_no_funds', [], 'en'))
            ->assertSee('name="terms"', false);
    }

    public function test_public_terms_page_shows_agreement(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get('http://' . config('domains.corporate') . '/terms')
            ->assertOk()
            ->assertSee(__('legal.clause_not_merchant_of_record', [], 'en'));
    }

    public function test_accepted_version_matches_displayed_version(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $merchant = Merchant::factory()->create(['user_id' => $user->id, 'settings' => ['onboarding_step' => 2]]);

        $this->actingAs($user)->post(route('onboarding.business-settings.store'), [
            'currency' => 'THB', 'timezone' => 'Asia/Bangkok', 'date_format' => 'DD/MM/YYYY',
            'country' => 'TH', 'terms' => 1, 'locale' => 'th',
        ]);

        $this->assertSame(
            config('countries.terms_version'),
            TermsAcceptance::where('merchant_id', $merchant->id)->value('version')
        );
    }
}
