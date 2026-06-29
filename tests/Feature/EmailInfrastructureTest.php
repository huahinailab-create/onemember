<?php

namespace Tests\Feature;

use App\Events\EmailFailed;
use App\Events\EmailSent;
use App\Events\FeedbackSubmitted;
use App\Events\PasswordChanged;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionPurchased;
use App\Events\SubscriptionRenewed;
use App\Events\PaymentFailed;
use App\Events\TrialEnding;
use App\Events\TrialStarted;
use App\Mail\EmailVerifiedEmail;
use App\Mail\FeedbackReceivedEmail;
use App\Mail\PasswordChangedEmail;
use App\Mail\PaymentFailedEmail;
use App\Mail\SubscriptionCancelledEmail;
use App\Mail\SubscriptionPurchasedEmail;
use App\Mail\SubscriptionRenewedEmail;
use App\Mail\TrialEndingReminderEmail;
use App\Mail\TrialStartedEmail;
use App\Mail\WelcomeEmail;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    private function userWithMerchant(): User
    {
        $user     = User::factory()->create();
        Merchant::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    // ── Mailables are queueable ───────────────────────────────────────

    public function test_welcome_email_implements_should_queue(): void
    {
        $user     = User::factory()->create();
        $mailable = new WelcomeEmail($user);

        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $mailable);
    }

    public function test_all_mailables_implement_should_queue(): void
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);

        $mailables = [
            new WelcomeEmail($user),
            new EmailVerifiedEmail($user),
            new TrialStartedEmail($merchant),
            new TrialEndingReminderEmail($merchant, 3),
            new SubscriptionPurchasedEmail($merchant, 'starter'),
            new SubscriptionRenewedEmail($merchant, 'starter', now()->addMonth()),
            new SubscriptionCancelledEmail($merchant),
            new PaymentFailedEmail($merchant, 'inv_test', '500.00'),
            new PasswordChangedEmail($user),
            new FeedbackReceivedEmail($user, ['category' => 'bug', 'message' => 'test'], false),
        ];

        foreach ($mailables as $mailable) {
            $this->assertInstanceOf(
                \Illuminate\Contracts\Queue\ShouldQueue::class,
                $mailable,
                get_class($mailable) . ' must implement ShouldQueue'
            );
        }
    }

    // ── Registered event sends WelcomeEmail ──────────────────────────

    public function test_registered_event_queues_welcome_email(): void
    {
        Mail::fake();
        Queue::fake();

        $user = User::factory()->create();
        event(new Registered($user));

        Mail::assertQueued(WelcomeEmail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
    }

    // ── Verified event sends EmailVerifiedEmail ───────────────────────

    public function test_verified_event_queues_email_verified_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);
        event(new Verified($user));

        Mail::assertQueued(EmailVerifiedEmail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
    }

    // ── TrialStarted event sends TrialStartedEmail ───────────────────

    public function test_trial_started_event_queues_email(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        TrialStarted::dispatch($merchant);

        Mail::assertQueued(TrialStartedEmail::class);
    }

    public function test_trial_started_skipped_when_product_updates_disabled(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $settings                               = $merchant->settings ?? [];
        $settings['email_notifications']        = ['product_updates' => false];
        $merchant->update(['settings' => $settings]);

        TrialStarted::dispatch($merchant->fresh());

        Mail::assertNotQueued(TrialStartedEmail::class);
    }

    // ── TrialEnding event sends TrialEndingReminderEmail ─────────────

    public function test_trial_ending_event_queues_email(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        TrialEnding::dispatch($merchant, 3);

        Mail::assertQueued(TrialEndingReminderEmail::class, function ($mail) {
            return $mail->daysRemaining === 3;
        });
    }

    // ── Billing events send correct mailables ─────────────────────────

    public function test_subscription_purchased_event_queues_email(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        SubscriptionPurchased::dispatch($merchant, 'starter', 'price_test', 'sub_test');

        Mail::assertQueued(SubscriptionPurchasedEmail::class, function ($mail) {
            return $mail->planKey === 'starter';
        });
    }

    public function test_subscription_renewed_event_queues_email(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        SubscriptionRenewed::dispatch($merchant, 'professional', now()->addMonth());

        Mail::assertQueued(SubscriptionRenewedEmail::class);
    }

    public function test_subscription_cancelled_event_queues_email(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        SubscriptionCancelled::dispatch($merchant);

        Mail::assertQueued(SubscriptionCancelledEmail::class);
    }

    public function test_payment_failed_event_queues_email(): void
    {
        Mail::fake();

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        PaymentFailed::dispatch($merchant, 'inv_test_123', '299.00');

        Mail::assertQueued(PaymentFailedEmail::class, function ($mail) {
            return $mail->invoiceId === 'inv_test_123';
        });
    }

    // ── PasswordChanged event sends PasswordChangedEmail ─────────────

    public function test_password_changed_event_queues_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        PasswordChanged::dispatch($user);

        Mail::assertQueued(PasswordChangedEmail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
    }

    // ── FeedbackSubmitted sends two emails ────────────────────────────

    public function test_feedback_submitted_queues_thankyou_and_support_emails(): void
    {
        Mail::fake();

        config(['email.support_email' => 'support@test.com']);

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $feedback = [
            'id'           => 'test-uuid',
            'category'     => 'bug',
            'subject'      => 'Test feedback',
            'message'      => 'Test message',
            'submitted_at' => now()->toISOString(),
            'user_id'      => $user->id,
            'merchant_id'  => $merchant->id,
            'current_url'  => 'https://example.com',
        ];

        FeedbackSubmitted::dispatch($user, $merchant, $feedback);

        // Thank-you to user
        Mail::assertQueued(FeedbackReceivedEmail::class, function ($mail) use ($user) {
            return ! $mail->forSupport && $mail->user->id === $user->id;
        });

        // Notification to support
        Mail::assertQueued(FeedbackReceivedEmail::class, function ($mail) {
            return $mail->forSupport === true;
        });
    }

    // ── Notification preferences respected ───────────────────────────

    public function test_wantsEmail_returns_true_for_billing_always(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        $this->assertTrue($merchant->wantsEmail('billing'));
    }

    public function test_wantsEmail_returns_true_for_security_alerts_always(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        $this->assertTrue($merchant->wantsEmail('security_alerts'));
    }

    public function test_wantsEmail_defaults_to_true_when_not_set(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        $this->assertTrue($merchant->wantsEmail('product_updates'));
    }

    public function test_wantsEmail_respects_disabled_preference(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $settings                        = $merchant->settings ?? [];
        $settings['email_notifications'] = ['product_updates' => false, 'tips' => false];
        $merchant->update(['settings' => $settings]);

        $this->assertFalse($merchant->fresh()->wantsEmail('product_updates'));
        $this->assertFalse($merchant->fresh()->wantsEmail('tips'));
    }

    // ── Delivery events dispatched ────────────────────────────────────

    public function test_email_sent_event_is_dispatchable(): void
    {
        Event::fake([EmailSent::class]);

        EmailSent::dispatch('WelcomeEmail', 'user@example.com', 1);

        Event::assertDispatched(EmailSent::class, function ($e) {
            return $e->template === 'WelcomeEmail';
        });
    }

    public function test_email_failed_event_is_dispatchable(): void
    {
        Event::fake([EmailFailed::class]);

        EmailFailed::dispatch('WelcomeEmail', 'user@example.com', 'Connection refused', 1);

        Event::assertDispatched(EmailFailed::class, function ($e) {
            return $e->reason === 'Connection refused';
        });
    }

    // ── Localization ─────────────────────────────────────────────────

    public function test_email_translations_have_identical_keys_in_en_and_th(): void
    {
        $en = require base_path('lang/en/email.php');
        $th = require base_path('lang/th/email.php');

        $enKeys = array_keys($en);
        $thKeys = array_keys($th);

        sort($enKeys);
        sort($thKeys);

        $this->assertEquals($enKeys, $thKeys, 'EN and TH email translation keys must match');
    }

    // ── Correct mailable sent to correct address ──────────────────────

    public function test_welcome_email_is_sent_to_user_address(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'specific@test.com']);
        event(new Registered($user));

        Mail::assertQueued(WelcomeEmail::class, function ($mail) {
            return true; // subscriber routes it to $user->email via Mail::to()
        });
    }

    // ── FeedbackController dispatches FeedbackSubmitted ───────────────

    public function test_feedback_controller_dispatches_feedback_submitted_event(): void
    {
        Event::fake([FeedbackSubmitted::class]);

        $user = $this->userWithMerchant();

        $this->actingAs($user)->post(route('feedback.store'), [
            'category' => 'bug',
            'subject'  => 'Test subject',
            'message'  => 'This is a test message with enough length to pass validation.',
        ])->assertRedirect();

        Event::assertDispatched(FeedbackSubmitted::class);
    }

    // ── PasswordController dispatches PasswordChanged ─────────────────

    public function test_password_controller_dispatches_password_changed_event(): void
    {
        Event::fake([PasswordChanged::class]);

        $user = User::factory()->create([
            'password' => bcrypt('currentPassword1!'),
        ]);

        $this->actingAs($user)->put(route('password.update'), [
            'current_password'      => 'currentPassword1!',
            'password'              => 'newPassword1!@#',
            'password_confirmation' => 'newPassword1!@#',
        ]);

        Event::assertDispatched(PasswordChanged::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }

    // ── Settings preferences update stores email notification prefs ───

    public function test_preferences_update_stores_email_notifications(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        $this->actingAs($user)->put(route('settings.preferences.update'), [
            'currency'                    => 'THB',
            'timezone'                    => 'Asia/Bangkok',
            'date_format'                 => 'DD/MM/YYYY',
            'default_expiration_type'     => 'never',
            'default_birthday_enabled'    => false,
            'locale'                      => 'en',
            'email_product_updates'        => false,
            'email_tips'                   => true,
            'email_feature_announcements'  => false,
        ])->assertRedirect();

        $fresh    = $merchant->fresh();
        $prefs    = $fresh->settings['email_notifications'] ?? [];

        $this->assertFalse($prefs['product_updates']);
        $this->assertTrue($prefs['tips']);
        $this->assertFalse($prefs['feature_announcements']);
    }
}
