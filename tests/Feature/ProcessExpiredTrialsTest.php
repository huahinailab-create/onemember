<?php

namespace Tests\Feature;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessExpiredTrialsTest extends TestCase
{
    use RefreshDatabase;

    private function makeMerchant(array $attributes = []): Merchant
    {
        $user = User::factory()->create();

        return Merchant::factory()->create(array_merge([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
        ], $attributes));
    }

    public function test_expired_trial_is_downgraded_to_free(): void
    {
        $merchant = $this->makeMerchant([
            'subscription_status' => SubscriptionStatus::Trial,
            'subscription_plan'   => SubscriptionPlan::Professional,
            'trial_ends_at'       => now()->subDay(),
        ]);

        $this->artisan('subscriptions:process-expired-trials')->assertSuccessful();

        $merchant->refresh();
        $this->assertSame(SubscriptionStatus::Expired, $merchant->subscription_status);
        $this->assertSame(SubscriptionPlan::Free, $merchant->subscription_plan);
    }

    public function test_active_trial_is_untouched(): void
    {
        $merchant = $this->makeMerchant([
            'subscription_status' => SubscriptionStatus::Trial,
            'subscription_plan'   => SubscriptionPlan::Professional,
            'trial_ends_at'       => now()->addDays(5),
        ]);

        $this->artisan('subscriptions:process-expired-trials')->assertSuccessful();

        $merchant->refresh();
        $this->assertSame(SubscriptionStatus::Trial, $merchant->subscription_status);
        $this->assertSame(SubscriptionPlan::Professional, $merchant->subscription_plan);
    }

    public function test_paid_subscription_is_untouched(): void
    {
        $merchant = $this->makeMerchant([
            'subscription_status' => SubscriptionStatus::Active,
            'subscription_plan'   => SubscriptionPlan::Starter,
            'trial_ends_at'       => now()->subMonth(),
        ]);

        $this->artisan('subscriptions:process-expired-trials')->assertSuccessful();

        $this->assertSame(SubscriptionStatus::Active, $merchant->fresh()->subscription_status);
    }

    public function test_command_is_idempotent(): void
    {
        $merchant = $this->makeMerchant([
            'subscription_status' => SubscriptionStatus::Trial,
            'subscription_plan'   => SubscriptionPlan::Professional,
            'trial_ends_at'       => now()->subDay(),
        ]);

        $this->artisan('subscriptions:process-expired-trials')->assertSuccessful();
        $this->artisan('subscriptions:process-expired-trials')
            ->expectsOutputToContain('No expired trials to process.')
            ->assertSuccessful();

        $this->assertSame(SubscriptionStatus::Expired, $merchant->fresh()->subscription_status);
    }
}
