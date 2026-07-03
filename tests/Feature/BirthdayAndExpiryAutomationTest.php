<?php

namespace Tests\Feature;

use App\Console\Commands\ProcessBirthdayRewards;
use App\Console\Commands\ProcessPointExpiry;
use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\TransactionType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirthdayAndExpiryAutomationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────

    private function makeMerchant(): Merchant
    {
        $user = User::factory()->create();
        return Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
        ]);
    }

    private function makePointsCampaign(Merchant $merchant, array $settings = []): LoyaltyProgram
    {
        return LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
            'settings'    => array_merge([
                'spend_amount'               => 100,
                'points_awarded'             => 1,
                'expiration_type'            => 'never',
                'expiration_duration'        => null,
                'birthday_enabled'           => false,
                'birthday_points'            => null,
                'birthday_valid_days_before' => 0,
                'birthday_valid_days_after'  => 0,
            ], $settings),
        ]);
    }

    private function makeStampsCampaign(Merchant $merchant): LoyaltyProgram
    {
        return LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'type'        => LoyaltyProgramType::Stamps,
            'status'      => CampaignStatus::Active,
            'settings'    => [
                'stamps_required'            => 10,
                'birthday_enabled'           => true,
                'birthday_points'            => 50,
                'birthday_valid_days_before' => 0,
                'birthday_valid_days_after'  => 7,
                'expiration_type'            => 'months',
                'expiration_duration'        => 12,
            ],
        ]);
    }

    private function makeMemberWithBirthday(Merchant $merchant, string $birthday, int $points = 100): Member
    {
        return Member::factory()->create([
            'merchant_id'     => $merchant->id,
            'birthday'        => $birthday,
            'total_points'    => $points,
            'last_activity_at'=> now()->subMonths(1),
        ]);
    }

    // ── Birthday command tests ────────────────────────────────

    public function test_birthday_bonus_awarded_on_birthday_date(): void
    {
        $merchant = $this->makeMerchant();
        $campaign = $this->makePointsCampaign($merchant, [
            'birthday_enabled'           => true,
            'birthday_points'            => 50,
            'birthday_valid_days_before' => 0,
            'birthday_valid_days_after'  => 0,
        ]);
        $member = $this->makeMemberWithBirthday($merchant, now()->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertDatabaseHas('transactions', [
            'member_id'          => $member->id,
            'loyalty_program_id' => $campaign->id,
            'type'               => TransactionType::Birthday->value,
            'points'             => 50,
        ]);
        $this->assertSame(150, $member->fresh()->total_points);
    }

    public function test_birthday_bonus_not_awarded_if_birthday_not_in_window(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'birthday_enabled'           => true,
            'birthday_points'            => 50,
            'birthday_valid_days_before' => 0,
            'birthday_valid_days_after'  => 0,
        ]);
        $this->makeMemberWithBirthday($merchant, now()->addDays(30)->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_birthday_bonus_not_awarded_twice_in_same_year(): void
    {
        $merchant = $this->makeMerchant();
        $campaign = $this->makePointsCampaign($merchant, [
            'birthday_enabled'           => true,
            'birthday_points'            => 50,
            'birthday_valid_days_before' => 0,
            'birthday_valid_days_after'  => 7,
        ]);
        $member = $this->makeMemberWithBirthday($merchant, now()->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();
        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertSame(1, Transaction::where('member_id', $member->id)
            ->where('type', TransactionType::Birthday->value)
            ->count());
    }

    public function test_birthday_bonus_not_awarded_when_birthday_disabled(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'birthday_enabled' => false,
            'birthday_points'  => 50,
        ]);
        $this->makeMemberWithBirthday($merchant, now()->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_birthday_bonus_not_awarded_for_stamp_campaign(): void
    {
        $merchant = $this->makeMerchant();
        $this->makeStampsCampaign($merchant);
        $this->makeMemberWithBirthday($merchant, now()->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_birthday_bonus_respects_valid_days_before(): void
    {
        $merchant = $this->makeMerchant();
        $campaign = $this->makePointsCampaign($merchant, [
            'birthday_enabled'           => true,
            'birthday_points'            => 50,
            'birthday_valid_days_before' => 3,
            'birthday_valid_days_after'  => 0,
        ]);
        // Birthday is tomorrow — within the 3-day before window
        $member = $this->makeMemberWithBirthday($merchant, now()->addDay()->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertDatabaseHas('transactions', [
            'member_id' => $member->id,
            'type'      => TransactionType::Birthday->value,
        ]);
    }

    public function test_birthday_bonus_respects_valid_days_after(): void
    {
        $merchant = $this->makeMerchant();
        $campaign = $this->makePointsCampaign($merchant, [
            'birthday_enabled'           => true,
            'birthday_points'            => 50,
            'birthday_valid_days_before' => 0,
            'birthday_valid_days_after'  => 3,
        ]);
        // Birthday was yesterday — within the 3-day after window
        $member = $this->makeMemberWithBirthday($merchant, now()->subDay()->format('Y-m-d'), 100);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        $this->assertDatabaseHas('transactions', [
            'member_id' => $member->id,
            'type'      => TransactionType::Birthday->value,
        ]);
    }

    // ── Point expiry command tests ────────────────────────────

    public function test_points_expired_after_inactivity_window(): void
    {
        $merchant = $this->makeMerchant();
        $campaign = $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'months',
            'expiration_duration' => 12,
        ]);
        $member = Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => now()->subMonths(13),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertDatabaseHas('transactions', [
            'member_id'          => $member->id,
            'loyalty_program_id' => $campaign->id,
            'type'               => TransactionType::Expire->value,
            'points'             => -500,
            'balance_after'      => 0,
        ]);
        $this->assertSame(0, $member->fresh()->total_points);
    }

    public function test_points_not_expired_within_activity_window(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'months',
            'expiration_duration' => 12,
        ]);
        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => now()->subMonths(11),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_points_not_expired_when_total_points_zero(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'months',
            'expiration_duration' => 12,
        ]);
        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 0,
            'last_activity_at' => now()->subMonths(13),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_points_not_expired_twice_on_same_day(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'months',
            'expiration_duration' => 12,
        ]);
        $member = Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => now()->subMonths(13),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();
        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertSame(1, Transaction::where('member_id', $member->id)
            ->where('type', TransactionType::Expire->value)
            ->count());
    }

    public function test_points_not_expired_for_never_expiry_type(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'never',
            'expiration_duration' => null,
        ]);
        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => now()->subYears(5),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_points_not_expired_for_stamp_campaign(): void
    {
        $merchant = $this->makeMerchant();
        $this->makeStampsCampaign($merchant);
        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => now()->subMonths(24),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_expiry_uses_years_when_type_is_years(): void
    {
        $merchant = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'years',
            'expiration_duration' => 1,
        ]);
        // Last active 11 months ago — within a 1-year window, should NOT expire
        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => now()->subMonths(11),
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_last_activity_at_not_updated_on_expiry(): void
    {
        $merchant     = $this->makeMerchant();
        $this->makePointsCampaign($merchant, [
            'expiration_type'     => 'months',
            'expiration_duration' => 12,
        ]);
        $lastActivity = now()->subMonths(13);
        $member       = Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'total_points'     => 500,
            'last_activity_at' => $lastActivity,
        ]);

        $this->artisan('loyalty:process-point-expiry')->assertSuccessful();

        $this->assertEquals(
            $lastActivity->toDateTimeString(),
            $member->fresh()->last_activity_at->toDateTimeString()
        );
    }
}
