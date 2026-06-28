<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Centralised security event logger.
 *
 * All security-relevant events pass through here.
 * To add a new event type, add a public method and call $this->write().
 *
 * Never log: passwords, tokens, session IDs, cookies, API keys, or any secrets.
 */
class SecurityLogger
{
    // ---------------------------------------------------------------
    // Authentication
    // ---------------------------------------------------------------

    public function loginSucceeded(int $userId, string $email, ?int $merchantId = null): void
    {
        $this->write('auth.login.succeeded', $userId, $email, $merchantId);
    }

    public function loginFailed(string $email): void
    {
        $this->write('auth.login.failed', null, $email, null);
    }

    public function logout(int $userId, string $email, ?int $merchantId = null): void
    {
        $this->write('auth.logout', $userId, $email, $merchantId);
    }

    public function passwordResetRequested(string $email): void
    {
        $this->write('auth.password.reset_requested', null, $email, null);
    }

    public function passwordResetCompleted(int $userId, string $email, ?int $merchantId = null): void
    {
        $this->write('auth.password.reset_completed', $userId, $email, $merchantId);
    }

    public function passwordChanged(int $userId, string $email, ?int $merchantId = null): void
    {
        $this->write('auth.password.changed', $userId, $email, $merchantId);
    }

    public function emailVerified(int $userId, string $email, ?int $merchantId = null): void
    {
        $this->write('auth.email.verified', $userId, $email, $merchantId);
    }

    // ---------------------------------------------------------------
    // Merchant
    // ---------------------------------------------------------------

    public function merchantRegistered(int $userId, string $email): void
    {
        $this->write('merchant.registered', $userId, $email, null);
    }

    public function merchantOnboardingCompleted(int $userId, string $email, int $merchantId): void
    {
        $this->write('merchant.onboarding.completed', $userId, $email, $merchantId);
    }

    // ---------------------------------------------------------------
    // Subscription
    // ---------------------------------------------------------------

    public function trialExpired(int $merchantId, string $merchantName, string $fromPlan): void
    {
        $this->write('subscription.trial.expired', null, null, $merchantId, [
            'merchant_name' => $merchantName,
            'from_plan'     => $fromPlan,
            'to_plan'       => 'free',
        ]);
    }

    public function subscriptionStatusChanged(int $merchantId, string $fromStatus, string $toStatus): void
    {
        $this->write('subscription.status.changed', null, null, $merchantId, [
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
        ]);
    }

    public function subscriptionPlanChanged(int $merchantId, string $fromPlan, string $toPlan): void
    {
        $this->write('subscription.plan.changed', null, null, $merchantId, [
            'from_plan' => $fromPlan,
            'to_plan'   => $toPlan,
        ]);
    }

    // ---------------------------------------------------------------
    // Internal
    // ---------------------------------------------------------------

    private function write(
        string  $event,
        ?int    $userId,
        ?string $email,
        ?int    $merchantId,
        array   $context = []
    ): void {
        $payload = array_filter([
            'event'       => $event,
            'user_id'     => $userId,
            'merchant_id' => $merchantId,
            'email'       => $email,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ], fn ($v) => $v !== null);

        if ($context) {
            $payload['context'] = $context;
        }

        Log::channel('security')->info($event, $payload);
    }
}
