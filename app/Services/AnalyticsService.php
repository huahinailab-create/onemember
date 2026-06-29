<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Vendor-agnostic analytics abstraction.
 *
 * All product analytics and exception reporting flows through this service.
 * Controllers and models never call provider SDKs directly — this class is
 * the single integration point. Swapping or adding providers requires changes
 * only here.
 *
 * All public methods are safe to call even when analytics is disabled.
 * They silently return without throwing exceptions.
 */
class AnalyticsService
{
    private bool $enabled;
    private string $provider;
    private array $config;

    public function __construct()
    {
        $this->config   = config('analytics', []);
        $this->enabled  = (bool) ($this->config['enabled'] ?? false);
        $this->provider = $this->config['provider'] ?? 'null';
    }

    // -----------------------------------------------------------------------
    // Identity
    // -----------------------------------------------------------------------

    /**
     * Identify a merchant in the analytics provider.
     * Call after merchant onboarding completes.
     */
    public function identifyMerchant(Merchant $merchant): void
    {
        if (! $this->canTrack('identify')) {
            return;
        }

        $this->dispatch('identify', [
            'distinct_id' => 'merchant_' . $merchant->id,
            'properties'  => [
                'merchant_id'   => $merchant->id,
                'business_type' => $merchant->business_type,
                'plan'          => $merchant->subscription_plan?->value,
                'status'        => $merchant->subscription_status?->value,
                'currency'      => $merchant->currency,
                'timezone'      => $merchant->timezone,
                'country'       => $merchant->country,
                'created_at'    => $merchant->created_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Identify a user in the analytics provider.
     */
    public function identifyUser(User $user, ?Merchant $merchant = null): void
    {
        if (! $this->canTrack('identify')) {
            return;
        }

        $properties = [
            'user_id'   => $user->id,
            'email'     => $user->email,
            'name'      => $user->name,
            'locale'    => app()->getLocale(),
        ];

        if ($merchant) {
            $properties['merchant_id']   = $merchant->id;
            $properties['business_type'] = $merchant->business_type;
            $properties['plan']          = $merchant->subscription_plan?->value;
        }

        $this->dispatch('identify', [
            'distinct_id' => 'user_' . $user->id,
            'properties'  => $properties,
        ]);
    }

    // -----------------------------------------------------------------------
    // Page views
    // -----------------------------------------------------------------------

    /**
     * Track a page view.
     *
     * @param  string       $name    Human-readable page name (e.g. 'Dashboard')
     * @param  string|null  $url     Current URL; defaults to request()->fullUrl()
     * @param  array        $extra   Additional context properties
     */
    public function page(string $name, ?string $url = null, array $extra = []): void
    {
        if (! $this->canTrack('page_views')) {
            return;
        }

        $this->dispatch('page', array_merge([
            'name' => $name,
            'url'  => $url ?? request()->fullUrl(),
        ], $extra));
    }

    // -----------------------------------------------------------------------
    // Product events
    // -----------------------------------------------------------------------

    /**
     * Track a named product event.
     *
     * @param  string  $event       Snake-case event name (e.g. 'campaign_created')
     * @param  array   $properties  Key/value pairs attached to the event
     * @param  int|null $userId     The acting user's ID (resolved from Auth if null)
     * @param  int|null $merchantId The acting merchant's ID
     */
    public function track(
        string $event,
        array $properties = [],
        ?int $userId = null,
        ?int $merchantId = null
    ): void {
        if (! $this->canTrack('events')) {
            return;
        }

        $properties['app_version']  = config('app.version', '1.0');
        $properties['environment']  = config('app.env', 'production');
        $properties['locale']       = app()->getLocale();

        if ($userId !== null) {
            $properties['user_id'] = $userId;
        }
        if ($merchantId !== null) {
            $properties['merchant_id'] = $merchantId;
        }

        $this->dispatch('track', [
            'event'      => $event,
            'properties' => $properties,
        ]);
    }

    /**
     * Track a feature usage event.
     *
     * @param  string  $feature     Feature name (e.g. 'birthday_reward')
     * @param  array   $properties  Additional context
     */
    public function feature(string $feature, array $properties = []): void
    {
        $this->track('feature_used', array_merge(['feature' => $feature], $properties));
    }

    // -----------------------------------------------------------------------
    // Exception reporting
    // -----------------------------------------------------------------------

    /**
     * Report an exception to the configured error tracking provider.
     * No-ops when SENTRY_DSN is empty or analytics.features.exceptions is false.
     */
    public function exception(Throwable $e, array $context = []): void
    {
        if (! $this->canTrack('exceptions')) {
            return;
        }

        $dsn = $this->config['sentry']['dsn'] ?? '';

        if (empty($dsn)) {
            return;
        }

        try {
            // Only call the Sentry SDK if it is installed. This avoids a hard
            // dependency — the SDK is optional. Operators who want Sentry add it
            // to composer.json; operators who don't are unaffected.
            if (class_exists(\Sentry\SentrySdk::class) && \Sentry\SentrySdk::getCurrentHub()->getClient()) {
                if (! empty($context)) {
                    \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($context): void {
                        foreach ($context as $key => $value) {
                            $scope->setExtra($key, $value);
                        }
                    });
                }
                \Sentry\captureException($e);
            }
        } catch (Throwable) {
            // Exception reporting must never cause further exceptions.
        }
    }

    // -----------------------------------------------------------------------
    // Health / Activation metrics
    // -----------------------------------------------------------------------

    /**
     * Return activation metrics for a merchant.
     * All values are timestamps (Carbon) or null. No side-effects.
     */
    public function activationMetrics(Merchant $merchant): array
    {
        $firstCampaign = $merchant->loyaltyPrograms()
            ->withTrashed()
            ->oldest('id')
            ->first(['id', 'created_at']);

        $firstMember = $merchant->members()
            ->withTrashed()
            ->oldest('id')
            ->first(['id', 'created_at']);

        $firstPurchase = null;
        $firstRedemption = null;

        if ($firstMember) {
            $firstPurchase = $merchant->transactions()
                ->where('type', 'earn')
                ->oldest('created_at')
                ->first(['id', 'created_at']);

            $firstRedemption = $merchant->transactions()
                ->where('type', 'redeem')
                ->oldest('created_at')
                ->first(['id', 'created_at']);
        }

        $onboardingCompletedAt = $merchant->onboarding_completed_at;
        $registeredAt          = $merchant->created_at;

        return [
            'registered_at'             => $registeredAt,
            'onboarding_completed_at'   => $onboardingCompletedAt,
            'first_campaign_created_at' => $firstCampaign?->created_at,
            'first_member_added_at'     => $firstMember?->created_at,
            'first_purchase_at'         => $firstPurchase?->created_at,
            'first_redemption_at'       => $firstRedemption?->created_at,

            // Time deltas in minutes from registration (null if milestone not reached)
            'minutes_to_first_campaign' => $firstCampaign && $registeredAt
                ? $registeredAt->diffInMinutes($firstCampaign->created_at)
                : null,
            'minutes_to_first_member' => $firstMember && $registeredAt
                ? $registeredAt->diffInMinutes($firstMember->created_at)
                : null,
            'minutes_to_first_purchase' => $firstPurchase && $registeredAt
                ? $registeredAt->diffInMinutes($firstPurchase->created_at)
                : null,
            'minutes_to_first_redemption' => $firstRedemption && $registeredAt
                ? $registeredAt->diffInMinutes($firstRedemption->created_at)
                : null,

            // Fully activated = has reached first redemption
            'is_fully_activated' => $firstRedemption !== null,
        ];
    }

    /**
     * Return a simple activity score for a merchant.
     * Higher = more active. Returns an integer 0–100.
     * Scoring is additive; future sprints may refine the weights.
     */
    public function merchantActivityScore(Merchant $merchant): int
    {
        $score = 0;

        // Onboarding completed
        if ($merchant->onboarding_completed_at) {
            $score += 10;
        }

        // Has at least one campaign
        if ($merchant->loyaltyPrograms()->whereNull('deleted_at')->exists()) {
            $score += 10;
        }

        // Member count tiers
        $memberCount = $merchant->members()->count();
        $score += match (true) {
            $memberCount >= 100 => 20,
            $memberCount >= 50  => 15,
            $memberCount >= 10  => 10,
            $memberCount >= 1   => 5,
            default             => 0,
        };

        // Has purchases in the last 30 days
        $recentPurchases = $merchant->transactions()
            ->where('type', 'earn')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $score += match (true) {
            $recentPurchases >= 100 => 25,
            $recentPurchases >= 30  => 20,
            $recentPurchases >= 10  => 15,
            $recentPurchases >= 1   => 5,
            default                 => 0,
        };

        // Has at least one reward redeemed
        if ($merchant->redemptions()->exists()) {
            $score += 20;
        }

        // Active subscription (not free or expired)
        $plan = $merchant->subscription_plan?->value;
        if (in_array($plan, ['starter', 'professional', 'enterprise'])) {
            $score += 15;
        }

        return min($score, 100);
    }

    // -----------------------------------------------------------------------
    // Internal dispatch
    // -----------------------------------------------------------------------

    /**
     * Dispatch a payload to the configured provider.
     * Catches all exceptions to ensure analytics never breaks the application.
     */
    private function dispatch(string $method, array $payload): void
    {
        try {
            match ($this->provider) {
                'posthog' => $this->sendToPostHog($method, $payload),
                default   => null, // 'null' provider — discard
            };
        } catch (Throwable $e) {
            // Log locally but never propagate — analytics failures are silent.
            Log::channel('single')->debug('AnalyticsService dispatch failed', [
                'provider' => $this->provider,
                'method'   => $method,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function sendToPostHog(string $method, array $payload): void
    {
        $apiKey = $this->config['posthog']['api_key'] ?? '';
        $host   = rtrim($this->config['posthog']['host'] ?? 'https://app.posthog.com', '/');

        if (empty($apiKey)) {
            return;
        }

        // PostHog expects a "capture" call for both page views and events.
        $event = match ($method) {
            'page'     => '$pageview',
            'identify' => null,          // handled separately below
            default    => $payload['event'] ?? $method,
        };

        if ($method === 'identify') {
            $body = [
                'api_key'    => $apiKey,
                'event'      => '$identify',
                'distinct_id' => $payload['distinct_id'] ?? 'anonymous',
                'properties'  => $payload['properties'] ?? [],
            ];
        } else {
            $body = [
                'api_key'     => $apiKey,
                'event'       => $event,
                'distinct_id' => 'server',
                'properties'  => $payload['properties'] ?? $payload,
            ];
        }

        $timeout = (int) ($this->config['posthog']['timeout'] ?? 2);

        // Use native PHP stream context to keep dependencies minimal.
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\nAccept: application/json",
                'content' => json_encode($body),
                'timeout' => $timeout,
                'ignore_errors' => true,
            ],
        ]);

        @file_get_contents("{$host}/capture/", false, $context);
    }

    /**
     * Check whether a specific tracking feature is enabled.
     */
    private function canTrack(string $feature): bool
    {
        if (! $this->enabled) {
            return false;
        }

        return (bool) ($this->config['features'][$feature] ?? true);
    }
}
