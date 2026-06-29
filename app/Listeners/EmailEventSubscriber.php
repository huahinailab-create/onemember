<?php

namespace App\Listeners;

use App\Events\EmailFailed;
use App\Events\EmailSent;
use App\Events\EmailSending;
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
use App\Services\EmailLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;

class EmailEventSubscriber
{
    public function __construct(private readonly EmailLogger $logger) {}

    public function handleRegistered(Registered $event): void
    {
        $user = $event->user;
        $this->send($user->email, new WelcomeEmail($user), 'WelcomeEmail');
    }

    public function handleVerified(Verified $event): void
    {
        $user = $event->user;
        $this->send($user->email, new EmailVerifiedEmail($user), 'EmailVerifiedEmail');
    }

    public function handleTrialStarted(TrialStarted $event): void
    {
        $merchant = $event->merchant;
        if (! $merchant->wantsEmail('product_updates')) {
            return;
        }
        $user = $merchant->owner;
        if (! $user) {
            return;
        }
        $this->send($user->email, new TrialStartedEmail($merchant), 'TrialStartedEmail', $merchant->id);
    }

    public function handleTrialEnding(TrialEnding $event): void
    {
        $merchant = $event->merchant;
        $user     = $merchant->owner;
        if (! $user) {
            return;
        }
        $this->send($user->email, new TrialEndingReminderEmail($merchant, $event->daysRemaining), 'TrialEndingReminderEmail', $merchant->id);
    }

    public function handleSubscriptionPurchased(SubscriptionPurchased $event): void
    {
        $merchant = $event->merchant;
        $user     = $merchant->owner;
        if (! $user) {
            return;
        }
        $this->send($user->email, new SubscriptionPurchasedEmail($merchant, $event->planKey), 'SubscriptionPurchasedEmail', $merchant->id);
    }

    public function handleSubscriptionRenewed(SubscriptionRenewed $event): void
    {
        $merchant = $event->merchant;
        $user     = $merchant->owner;
        if (! $user) {
            return;
        }
        $this->send($user->email, new SubscriptionRenewedEmail($merchant, $event->planKey, $event->renewsAt), 'SubscriptionRenewedEmail', $merchant->id);
    }

    public function handleSubscriptionCancelled(SubscriptionCancelled $event): void
    {
        $merchant = $event->merchant;
        $user     = $merchant->owner;
        if (! $user) {
            return;
        }
        $this->send($user->email, new SubscriptionCancelledEmail($merchant), 'SubscriptionCancelledEmail', $merchant->id);
    }

    public function handlePaymentFailed(PaymentFailed $event): void
    {
        $merchant = $event->merchant;
        $user     = $merchant->owner;
        if (! $user) {
            return;
        }
        $this->send($user->email, new PaymentFailedEmail($merchant, $event->invoiceId, $event->amountDue), 'PaymentFailedEmail', $merchant->id);
    }

    public function handlePasswordChanged(PasswordChanged $event): void
    {
        $user = $event->user;
        $this->send($user->email, new PasswordChangedEmail($user), 'PasswordChangedEmail');
    }

    public function handleFeedbackSubmitted(FeedbackSubmitted $event): void
    {
        $user     = $event->user;
        $feedback = $event->feedback;

        // Thank-you to submitter
        $this->send($user->email, new FeedbackReceivedEmail($user, $feedback, false), 'FeedbackReceivedEmail');

        // Support notification
        $supportEmail = config('email.support_email');
        if ($supportEmail) {
            $this->send($supportEmail, new FeedbackReceivedEmail($user, $feedback, true), 'FeedbackSupportEmail');
        }
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Registered::class,           [static::class, 'handleRegistered']);
        $events->listen(Verified::class,             [static::class, 'handleVerified']);
        $events->listen(TrialStarted::class,         [static::class, 'handleTrialStarted']);
        $events->listen(TrialEnding::class,          [static::class, 'handleTrialEnding']);
        $events->listen(SubscriptionPurchased::class,[static::class, 'handleSubscriptionPurchased']);
        $events->listen(SubscriptionRenewed::class,  [static::class, 'handleSubscriptionRenewed']);
        $events->listen(SubscriptionCancelled::class,[static::class, 'handleSubscriptionCancelled']);
        $events->listen(PaymentFailed::class,        [static::class, 'handlePaymentFailed']);
        $events->listen(PasswordChanged::class,      [static::class, 'handlePasswordChanged']);
        $events->listen(FeedbackSubmitted::class,    [static::class, 'handleFeedbackSubmitted']);
    }

    private function send(string $to, \Illuminate\Mail\Mailable $mailable, string $template, ?int $merchantId = null): void
    {
        $this->logger->sending($template, $to, $merchantId);

        try {
            Mail::to($to)->queue($mailable);
            $this->logger->sent($template, $to, config('mail.default', 'ses'), $merchantId);
            EmailSent::dispatch($template, $to, $merchantId);
        } catch (\Throwable $e) {
            $this->logger->failed($template, $to, $e->getMessage(), $merchantId);
            EmailFailed::dispatch($template, $to, $e->getMessage(), $merchantId);
        }
    }
}
