<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Mail\MemberBirthdayEmail;
use App\Mail\MemberPointsEarnedEmail;
use App\Mail\MemberRewardRedeemedEmail;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MemberNotificationEmailTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;
    private LoyaltyProgram $campaign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
        ]);
        $this->campaign = LoyaltyProgram::factory()->create([
            'merchant_id' => $this->merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
            'settings'    => ['spend_amount' => 100, 'points_awarded' => 1],
        ]);
    }

    private function makeMember(array $attributes = []): Member
    {
        return Member::factory()->create(array_merge([
            'merchant_id'  => $this->merchant->id,
            'email'        => 'member@example.com',
            'total_points' => 500,
        ], $attributes));
    }

    private function recordPurchase(Member $member): void
    {
        $this->actingAs($this->user)->post(route('members.purchases.store', $member), [
            'purchase_amount' => 500,
        ]);
    }

    // ── Points earned ────────────────────────────────────────────────────

    public function test_points_earned_email_queued_after_purchase(): void
    {
        Mail::fake();
        $member = $this->makeMember();

        $this->recordPurchase($member);

        Mail::assertQueued(MemberPointsEarnedEmail::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email);
        });
    }

    public function test_no_points_email_when_member_has_no_email(): void
    {
        Mail::fake();
        $member = $this->makeMember(['email' => null]);

        $this->recordPurchase($member);

        Mail::assertNotQueued(MemberPointsEarnedEmail::class);
    }

    public function test_no_points_email_when_merchant_disabled_member_notifications(): void
    {
        Mail::fake();
        $this->merchant->update([
            'settings' => array_merge($this->merchant->settings ?? [], [
                'email_notifications' => ['member_notifications' => false],
            ]),
        ]);
        $member = $this->makeMember();

        $this->recordPurchase($member);

        Mail::assertNotQueued(MemberPointsEarnedEmail::class);
    }

    // ── Reward redeemed ──────────────────────────────────────────────────

    public function test_reward_redeemed_email_queued_after_redemption(): void
    {
        Mail::fake();
        $member = $this->makeMember();
        $reward = Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $this->campaign->id,
            'status'             => 'active',
            'points_required'    => 100,
        ]);

        $this->actingAs($this->user)->post(route('members.redemptions.store', $member), [
            'reward_id' => $reward->id,
        ]);

        Mail::assertQueued(MemberRewardRedeemedEmail::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email);
        });
    }

    // ── Birthday greeting ────────────────────────────────────────────────

    public function test_birthday_email_queued_when_bonus_awarded(): void
    {
        Mail::fake();
        $this->campaign->update([
            'settings' => array_merge($this->campaign->settings, [
                'birthday_enabled' => true,
                'birthday_points'  => 50,
            ]),
        ]);
        $member = $this->makeMember(['birthday' => now()->subYears(25)->format('Y-m-d')]);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        Mail::assertQueued(MemberBirthdayEmail::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email);
        });
    }

    public function test_no_birthday_email_for_member_without_email(): void
    {
        Mail::fake();
        $this->campaign->update([
            'settings' => array_merge($this->campaign->settings, [
                'birthday_enabled' => true,
                'birthday_points'  => 50,
            ]),
        ]);
        $this->makeMember([
            'email'    => null,
            'birthday' => now()->subYears(25)->format('Y-m-d'),
        ]);

        $this->artisan('loyalty:process-birthday-rewards')->assertSuccessful();

        Mail::assertNotQueued(MemberBirthdayEmail::class);
    }

    // ── Email content locale ─────────────────────────────────────────────

    public function test_points_email_uses_merchant_locale(): void
    {
        Mail::fake();
        $this->merchant->update([
            'settings' => array_merge($this->merchant->settings ?? [], ['locale' => 'en']),
        ]);
        $member = $this->makeMember();

        $this->recordPurchase($member);

        Mail::assertQueued(MemberPointsEarnedEmail::class, function ($mail) {
            return $mail->locale === 'en';
        });
    }
}
