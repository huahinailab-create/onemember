<?php

namespace Tests\Feature;

use App\Console\Commands\SendTrialEndingReminders;
use App\Enums\SubscriptionStatus;
use App\Events\TrialEnding;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SendTrialEndingRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function makeMerchant(array $overrides = []): Merchant
    {
        $user = User::factory()->create();
        return Merchant::factory()->create(array_merge([
            'user_id'             => $user->id,
            'subscription_status' => SubscriptionStatus::Trial,
            'trial_ends_at'       => now()->addDays(5),
            'settings'            => ['locale' => 'th'],
        ], $overrides));
    }

    public function test_reminder_sent_when_trial_ends_within_7_days(): void
    {
        Event::fake([TrialEnding::class]);
        $this->makeMerchant(['trial_ends_at' => now()->addDays(5)]);

        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();

        Event::assertDispatched(TrialEnding::class);
    }

    public function test_reminder_not_sent_when_trial_ends_after_7_days(): void
    {
        Event::fake([TrialEnding::class]);
        $this->makeMerchant(['trial_ends_at' => now()->addDays(10)]);

        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();

        Event::assertNotDispatched(TrialEnding::class);
    }

    public function test_reminder_not_sent_twice(): void
    {
        Event::fake([TrialEnding::class]);
        $this->makeMerchant(['trial_ends_at' => now()->addDays(5)]);

        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();
        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();

        Event::assertDispatchedTimes(TrialEnding::class, 1);
    }

    public function test_reminder_not_sent_when_subscription_is_not_trial(): void
    {
        Event::fake([TrialEnding::class]);
        $this->makeMerchant([
            'subscription_status' => SubscriptionStatus::Active,
            'trial_ends_at'       => now()->addDays(3),
        ]);

        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();

        Event::assertNotDispatched(TrialEnding::class);
    }

    public function test_reminder_marks_merchant_as_reminded(): void
    {
        Event::fake([TrialEnding::class]);
        $merchant = $this->makeMerchant(['trial_ends_at' => now()->addDays(5)]);

        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();

        $this->assertTrue((bool) $merchant->fresh()->settings['trial_reminder_sent']);
    }

    public function test_days_remaining_passed_to_event(): void
    {
        Event::fake([TrialEnding::class]);
        $this->makeMerchant(['trial_ends_at' => now()->addDays(3)]);

        $this->artisan('subscriptions:send-trial-ending-reminders')->assertSuccessful();

        Event::assertDispatched(TrialEnding::class, function (TrialEnding $event) {
            return $event->daysRemaining >= 2 && $event->daysRemaining <= 4;
        });
    }
}
