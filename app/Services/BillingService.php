<?php

namespace App\Services;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Events\PaymentFailed;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionPurchased;
use App\Events\SubscriptionRenewed;
use App\Events\TrialEnding;
use App\Models\Merchant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\BillingPortal\Session as PortalSession;
use Stripe\Customer;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class BillingService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    // ── Customer ─────────────────────────────────────────────────────

    /**
     * Return the Stripe customer ID for this merchant, creating one if absent.
     */
    public function createOrGetCustomer(Merchant $merchant): string
    {
        if ($merchant->stripe_customer_id) {
            return $merchant->stripe_customer_id;
        }

        $email    = $merchant->billing_email ?? $merchant->email ?? $merchant->owner?->email;
        $customer = Customer::create([
            'email'    => $email,
            'name'     => $merchant->name,
            'metadata' => ['merchant_id' => $merchant->id],
        ]);

        $merchant->update([
            'stripe_customer_id' => $customer->id,
            'billing_email'      => $email,
        ]);

        return $customer->id;
    }

    // ── Checkout ─────────────────────────────────────────────────────

    /**
     * Create a Stripe Checkout Session for a given price ID.
     * Returns the session URL for the redirect.
     */
    public function createCheckoutSession(Merchant $merchant, string $priceId): string
    {
        $customerId = $this->createOrGetCustomer($merchant);

        $session = Session::create([
            'customer'            => $customerId,
            'mode'                => 'subscription',
            'line_items'          => [
                ['price' => $priceId, 'quantity' => 1],
            ],
            'success_url'         => config('stripe.checkout.success_url'),
            'cancel_url'          => config('stripe.checkout.cancel_url'),
            'subscription_data'   => [
                'metadata' => ['merchant_id' => $merchant->id],
            ],
            'metadata'            => ['merchant_id' => $merchant->id],
            'allow_promotion_codes' => true,
        ]);

        return $session->url;
    }

    // ── Billing Portal ────────────────────────────────────────────────

    /**
     * Create a Stripe Billing Portal Session.
     * Returns the portal URL for the redirect.
     */
    public function createPortalSession(Merchant $merchant): string
    {
        $customerId = $this->createOrGetCustomer($merchant);

        $session = PortalSession::create([
            'customer'   => $customerId,
            'return_url' => config('app.url') . '/subscription',
        ]);

        return $session->url;
    }

    // ── Subscription Management ───────────────────────────────────────

    /**
     * Cancel the merchant's subscription at the end of the current period.
     */
    public function cancelSubscription(Merchant $merchant): void
    {
        if (! $merchant->stripe_subscription_id) {
            return;
        }

        $subscription = Subscription::retrieve($merchant->stripe_subscription_id);
        $subscription->cancel_at_period_end = true;
        $subscription->save();

        $merchant->update(['cancel_at_period_end' => true]);

        SubscriptionCancelled::dispatch($merchant, $subscription->current_period_end
            ? Carbon::createFromTimestamp($subscription->current_period_end)->toIso8601String()
            : null
        );
    }

    /**
     * Resume a subscription that was cancelled but still in the current period.
     */
    public function resumeSubscription(Merchant $merchant): void
    {
        if (! $merchant->stripe_subscription_id) {
            return;
        }

        Subscription::update($merchant->stripe_subscription_id, [
            'cancel_at_period_end' => false,
        ]);

        $merchant->update(['cancel_at_period_end' => false]);
    }

    /**
     * Swap the subscription to a different price (upgrade or downgrade).
     * The change takes effect at the end of the current billing period for
     * downgrades and immediately for upgrades. For simplicity we always use
     * proration to let Stripe handle the billing math.
     */
    public function swapPlan(Merchant $merchant, string $newPriceId): void
    {
        if (! $merchant->stripe_subscription_id) {
            // No existing Stripe subscription — start fresh checkout
            return;
        }

        $subscription = Subscription::retrieve($merchant->stripe_subscription_id);

        Subscription::update($merchant->stripe_subscription_id, [
            'items'               => [
                [
                    'id'    => $subscription->items->data[0]->id,
                    'price' => $newPriceId,
                ],
            ],
            'proration_behavior'  => 'create_prorations',
            'cancel_at_period_end' => false,
        ]);

        // Stripe will send a customer.subscription.updated webhook — the webhook
        // handler does the authoritative merchant state update (DECISION-055).
    }

    // ── Sync ─────────────────────────────────────────────────────────

    /**
     * Sync merchant subscription fields from a Stripe Subscription object.
     * This is the ONLY place that updates subscription state from Stripe.
     */
    public function syncFromStripeSubscription(Merchant $merchant, Subscription $subscription): void
    {
        $priceId = $subscription->items->data[0]->price->id ?? null;
        $planKey = $priceId ? $this->priceIdToPlanKey($priceId) : null;

        $status = $this->stripeStatusToSubscriptionStatus($subscription->status);

        $updateData = [
            'stripe_subscription_id' => $subscription->id,
            'stripe_price_id'        => $priceId,
            'cancel_at_period_end'   => (bool) $subscription->cancel_at_period_end,
            'subscription_renews_at' => $subscription->current_period_end
                ? Carbon::createFromTimestamp($subscription->current_period_end)
                : null,
            'subscription_status'    => $status,
        ];

        if ($planKey) {
            $updateData['subscription_plan'] = SubscriptionPlan::from($planKey);
        }

        // Clear trial fields when a paid subscription is active
        if ($status === SubscriptionStatus::Active) {
            $updateData['trial_ends_at'] = null;
        }

        $merchant->update($updateData);
    }

    // ── Webhook ───────────────────────────────────────────────────────

    /**
     * Verify the Stripe webhook signature and route the event to the
     * appropriate handler. Throws SignatureVerificationException on
     * bad signatures; returns silently on unknown event types.
     *
     * @throws SignatureVerificationException
     */
    public function handleWebhookEvent(string $payload, string $signatureHeader): void
    {
        $event = Webhook::constructEvent(
            $payload,
            $signatureHeader,
            config('stripe.webhook_secret')
        );

        // Idempotency guard — skip events already processed within 24 hours
        $cacheKey = 'stripe_event_' . $event->id;
        if (Cache::has($cacheKey)) {
            Log::debug('Stripe webhook duplicate skipped', ['event_id' => $event->id]);
            return;
        }
        Cache::put($cacheKey, true, now()->addHours(24));

        Log::info('Stripe webhook received', ['type' => $event->type, 'id' => $event->id]);

        match ($event->type) {
            'checkout.session.completed'          => $this->handleCheckoutSessionCompleted($event->data->object),
            'customer.subscription.created'       => $this->handleSubscriptionCreated($event->data->object),
            'customer.subscription.updated'       => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted'       => $this->handleSubscriptionDeleted($event->data->object),
            'invoice.paid'                        => $this->handleInvoicePaid($event->data->object),
            'invoice.payment_failed'              => $this->handleInvoicePaymentFailed($event->data->object),
            'customer.subscription.trial_will_end' => $this->handleTrialWillEnd($event->data->object),
            default => null,
        };
    }

    // ── Webhook Handlers ──────────────────────────────────────────────

    private function handleCheckoutSessionCompleted(object $session): void
    {
        $merchant = $this->merchantFromMetadata($session->metadata ?? null);
        if (! $merchant) {
            return;
        }

        // Retrieve the subscription created by this checkout session
        if (! isset($session->subscription)) {
            return;
        }

        $subscription = Subscription::retrieve($session->subscription);
        $this->syncFromStripeSubscription($merchant, $subscription);

        $priceId = $subscription->items->data[0]->price->id ?? '';
        $planKey = $this->priceIdToPlanKey($priceId);

        SubscriptionPurchased::dispatch($merchant, $planKey, $priceId, $subscription->id);
    }

    private function handleSubscriptionCreated(object $stripeSubscription): void
    {
        $merchant = $this->merchantFromStripeSubscription($stripeSubscription);
        if (! $merchant) {
            return;
        }

        $sub = Subscription::retrieve($stripeSubscription->id);
        $this->syncFromStripeSubscription($merchant, $sub);
    }

    private function handleSubscriptionUpdated(object $stripeSubscription): void
    {
        $merchant = $this->merchantFromStripeSubscription($stripeSubscription);
        if (! $merchant) {
            return;
        }

        $sub = Subscription::retrieve($stripeSubscription->id);
        $this->syncFromStripeSubscription($merchant, $sub);
    }

    private function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        $merchant = $this->merchantFromStripeSubscription($stripeSubscription);
        if (! $merchant) {
            return;
        }

        $merchant->update([
            'stripe_subscription_id' => null,
            'stripe_price_id'        => null,
            'subscription_status'    => SubscriptionStatus::Cancelled,
            'subscription_plan'      => SubscriptionPlan::Free,
            'subscription_renews_at' => null,
            'cancel_at_period_end'   => false,
        ]);

        SubscriptionCancelled::dispatch($merchant);
    }

    private function handleInvoicePaid(object $invoice): void
    {
        if (! isset($invoice->subscription)) {
            return;
        }

        $merchant = $this->merchantFromStripeCustomerId($invoice->customer ?? null);
        if (! $merchant) {
            return;
        }

        $sub = Subscription::retrieve($invoice->subscription);
        $this->syncFromStripeSubscription($merchant, $sub);

        $planKey  = $this->priceIdToPlanKey($merchant->stripe_price_id ?? '');
        $renewsAt = $merchant->subscription_renews_at;
        SubscriptionRenewed::dispatch($merchant, $planKey, $renewsAt);
    }

    private function handleInvoicePaymentFailed(object $invoice): void
    {
        $merchant = $this->merchantFromStripeCustomerId($invoice->customer ?? null);
        if (! $merchant) {
            return;
        }

        PaymentFailed::dispatch(
            $merchant,
            $invoice->id ?? null,
            isset($invoice->amount_due) ? number_format($invoice->amount_due / 100, 2) : null
        );
    }

    private function handleTrialWillEnd(object $stripeSubscription): void
    {
        $merchant = $this->merchantFromStripeSubscription($stripeSubscription);
        if (! $merchant) {
            return;
        }

        $daysRemaining = isset($stripeSubscription->trial_end)
            ? (int) max(0, ceil((($stripeSubscription->trial_end - time()) / 86400)))
            : 0;

        TrialEnding::dispatch($merchant, $daysRemaining);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Map a Stripe price ID to our local plan key (starter/professional/enterprise).
     * Returns 'free' if no match found.
     */
    public function priceIdToPlanKey(string $priceId): string
    {
        $prices = config('stripe.prices', []);

        foreach ($prices as $planKey => $intervals) {
            foreach ($intervals as $priceIdValue) {
                if ($priceId === $priceIdValue && $priceIdValue !== '') {
                    return $planKey;
                }
            }
        }

        return 'free';
    }

    /**
     * Map a Stripe subscription status string to our SubscriptionStatus enum.
     */
    private function stripeStatusToSubscriptionStatus(string $stripeStatus): SubscriptionStatus
    {
        return match ($stripeStatus) {
            'active', 'trialing'             => SubscriptionStatus::Active,
            'canceled', 'unpaid', 'paused'   => SubscriptionStatus::Cancelled,
            'past_due', 'incomplete',
            'incomplete_expired'             => SubscriptionStatus::Expired,
            default                          => SubscriptionStatus::Active,
        };
    }

    /**
     * Find the merchant whose merchant_id is in the Stripe object metadata.
     */
    private function merchantFromMetadata(?object $metadata): ?Merchant
    {
        if (! $metadata || ! isset($metadata->merchant_id)) {
            return null;
        }

        return Merchant::find((int) $metadata->merchant_id);
    }

    /**
     * Find the merchant that owns a given Stripe subscription object.
     * Tries metadata first, then falls back to stripe_subscription_id lookup.
     */
    private function merchantFromStripeSubscription(object $stripeSubscription): ?Merchant
    {
        $merchant = $this->merchantFromMetadata($stripeSubscription->metadata ?? null);

        if (! $merchant) {
            $merchant = Merchant::where('stripe_subscription_id', $stripeSubscription->id)->first();
        }

        if (! $merchant && isset($stripeSubscription->customer)) {
            $merchant = Merchant::where('stripe_customer_id', $stripeSubscription->customer)->first();
        }

        return $merchant;
    }

    /**
     * Find the merchant by Stripe customer ID.
     */
    private function merchantFromStripeCustomerId(?string $customerId): ?Merchant
    {
        if (! $customerId) {
            return null;
        }

        return Merchant::where('stripe_customer_id', $customerId)->first();
    }
}
