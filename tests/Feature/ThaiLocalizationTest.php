<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MerchantStatus;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ThaiLocalizationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────

    private function createMerchantWithLocale(string $locale): array
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'currency'                => 'THB',
            'timezone'                => 'Asia/Bangkok',
            'settings'                => [
                'locale'          => $locale,
                'onboarding_step' => 5,
            ],
        ]);
        return compact('user', 'merchant');
    }

    private function createMemberForMerchant(Merchant $merchant): Member
    {
        return Member::factory()->create([
            'merchant_id'    => $merchant->id,
            'portal_enabled' => true,
        ]);
    }

    // ── Task 1: Default locale 'th' for new merchants ────────

    public function test_new_merchant_created_during_onboarding_gets_locale_th(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('onboarding.business-info.store'), [
            'name'          => 'ร้านกาแฟ Happy',
            'business_type' => 'Restaurant & Café',
        ]);

        $merchant = $user->fresh()->merchant;
        $this->assertNotNull($merchant);
        $this->assertSame('th', $merchant->settings['locale'] ?? null);
    }

    // ── Task 2: Onboarding settings saves locale ─────────────

    public function test_onboarding_business_settings_saves_locale_en(): void
    {
        ['user' => $user, 'merchant' => $merchant] = $this->createMerchantWithLocale('th');
        $this->actingAs($user);

        $this->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            'country'     => 'TH',
            'terms'       => 1,
            'locale'      => 'en',
        ]);

        $merchant->refresh();
        $this->assertSame('en', $merchant->settings['locale']);
    }

    public function test_onboarding_business_settings_saves_locale_th(): void
    {
        ['user' => $user, 'merchant' => $merchant] = $this->createMerchantWithLocale('en');
        $this->actingAs($user);

        $this->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            'country'     => 'TH',
            'terms'       => 1,
            'locale'      => 'th',
        ]);

        $merchant->refresh();
        $this->assertSame('th', $merchant->settings['locale']);
    }

    public function test_onboarding_business_settings_rejects_invalid_locale(): void
    {
        ['user' => $user] = $this->createMerchantWithLocale('th');
        $this->actingAs($user);

        $response = $this->post(route('onboarding.business-settings.store'), [
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'date_format' => 'DD/MM/YYYY',
            'country'     => 'TH',
            'terms'       => 1,
            'locale'      => 'ja',
        ]);

        $response->assertSessionHasErrors('locale');
    }

    // ── Task 3: Portal renders in merchant's locale ──────────

    public function test_member_portal_renders_in_thai_when_merchant_locale_is_th(): void
    {
        ['merchant' => $merchant] = $this->createMerchantWithLocale('th');

        $campaign = $merchant->loyaltyPrograms()->create([
            'name'     => 'Test',
            'type'     => LoyaltyProgramType::Points,
            'status'   => CampaignStatus::Active,
            'settings' => ['spend_amount' => 100, 'points_awarded' => 1],
        ]);

        $member = $this->createMemberForMerchant($merchant);

        $response = $this->get(route('portal.show', $member->public_uuid));

        $response->assertOk();
        // Thai translation of portal.points_balance
        $response->assertSee('คะแนนสะสม');
    }

    public function test_member_portal_renders_in_english_when_merchant_locale_is_en(): void
    {
        ['merchant' => $merchant] = $this->createMerchantWithLocale('en');

        $campaign = $merchant->loyaltyPrograms()->create([
            'name'     => 'Test',
            'type'     => LoyaltyProgramType::Points,
            'status'   => CampaignStatus::Active,
            'settings' => ['spend_amount' => 100, 'points_awarded' => 1],
        ]);

        $member = $this->createMemberForMerchant($merchant);

        $response = $this->get(route('portal.show', $member->public_uuid));

        $response->assertOk();
        // English translation of portal.points_balance
        $response->assertSee('Points Balance');
    }

    // ── Task 4: Starter campaigns created in locale ──────────

    public function test_starter_stamp_campaign_created_in_thai_when_locale_is_th(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'currency'                => 'THB',
            'timezone'                => 'Asia/Bangkok',
            'settings'                => [
                'locale'                   => 'th',
                'onboarding_step'          => 4,
                'onboarding_loyalty_type'  => 'stamps',
            ],
        ]);
        $this->actingAs($user);

        App::setLocale('th');

        $this->post(route('onboarding.quick-start.store'), ['choice' => 'yes']);

        $campaign = $merchant->loyaltyPrograms()->first();
        $this->assertNotNull($campaign);
        $this->assertSame('บัตรสะสมแสตมป์', $campaign->name);
    }

    public function test_starter_points_campaign_created_in_thai_when_locale_is_th(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'currency'                => 'THB',
            'timezone'                => 'Asia/Bangkok',
            'settings'                => [
                'locale'                   => 'th',
                'onboarding_step'          => 4,
                'onboarding_loyalty_type'  => 'points',
            ],
        ]);
        $this->actingAs($user);

        App::setLocale('th');

        $this->post(route('onboarding.quick-start.store'), ['choice' => 'yes']);

        $campaign = $merchant->loyaltyPrograms()->first();
        $this->assertNotNull($campaign);
        $this->assertSame('โปรแกรมสะสมคะแนน', $campaign->name);
    }

    public function test_starter_stamp_campaign_created_in_english_when_locale_is_en(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'currency'                => 'THB',
            'timezone'                => 'Asia/Bangkok',
            'settings'                => [
                'locale'                   => 'en',
                'onboarding_step'          => 4,
                'onboarding_loyalty_type'  => 'stamps',
            ],
        ]);
        $this->actingAs($user);

        App::setLocale('en');

        $this->post(route('onboarding.quick-start.store'), ['choice' => 'yes']);

        $campaign = $merchant->loyaltyPrograms()->first();
        $this->assertNotNull($campaign);
        $this->assertSame('Stamp Card', $campaign->name);
    }

    // ── Task 5: Alpine.js i18n keys exist in both locales ────

    public function test_earn_rule_tpl_exists_in_english(): void
    {
        App::setLocale('en');
        $translated = __('campaigns.earn_rule_tpl');
        $this->assertStringContainsString(':pts', $translated);
        $this->assertStringContainsString(':unit', $translated);
        $this->assertStringContainsString(':spend', $translated);
        $this->assertStringContainsString(':currency', $translated);
    }

    public function test_earn_rule_tpl_exists_in_thai(): void
    {
        App::setLocale('th');
        $translated = __('campaigns.earn_rule_tpl');
        $this->assertStringContainsString(':pts', $translated);
        $this->assertStringContainsString(':unit', $translated);
        $this->assertStringContainsString(':spend', $translated);
        $this->assertStringContainsString(':currency', $translated);
        // Should be in Thai characters
        $this->assertStringContainsString('ลูกค้า', $translated);
    }

    public function test_points_never_expire_translates_to_thai(): void
    {
        App::setLocale('th');
        $this->assertSame('คะแนนไม่มีวันหมดอายุ', __('campaigns.points_never_expire'));
    }

    public function test_birthday_bonus_tpl_has_all_placeholders_in_thai(): void
    {
        App::setLocale('th');
        $tpl = __('campaigns.birthday_bonus_tpl');
        foreach ([':pts', ':unit', ':before', ':day_b', ':after', ':day_a'] as $placeholder) {
            $this->assertStringContainsString($placeholder, $tpl,
                "birthday_bonus_tpl missing placeholder: $placeholder");
        }
    }
}
