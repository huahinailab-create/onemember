<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;

class SubscriptionController extends Controller
{
    // ── Plan Overview ─────────────────────────────────────────────────

    public function index(Request $request, SubscriptionService $subscriptionService, AnalyticsService $analytics): \Illuminate\View\View
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        $plans          = config('subscriptions.plans', []);
        $effectivePlan  = $merchant ? $subscriptionService->effectivePlanKey($merchant) : 'free';
        $usageSummary   = $merchant ? $subscriptionService->usageSummary($merchant) : null;
        $stripePrices   = config('stripe.prices', []);

        $analytics->page('Subscription');
        $analytics->track('subscription_viewed', [], $user->id, $merchant?->id);

        return view('subscription.index', compact(
            'user',
            'merchant',
            'plans',
            'effectivePlan',
            'usageSummary',
            'stripePrices',
        ));
    }

    // ── Checkout ──────────────────────────────────────────────────────

    public function checkout(Request $request, BillingService $billing, AnalyticsService $analytics): RedirectResponse
    {
        $request->validate([
            'price_id' => ['required', 'string'],
        ]);

        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $priceId = $request->input('price_id');

        // Verify the price ID belongs to a known plan
        $planKey = $billing->priceIdToPlanKey($priceId);
        abort_if($planKey === 'free', 422);

        $analytics->track('checkout_started', ['plan' => $planKey], $request->user()->id, $merchant->id);

        $checkoutUrl = $billing->createCheckoutSession($merchant, $priceId);

        return redirect()->away($checkoutUrl);
    }

    // ── Checkout Success ──────────────────────────────────────────────

    public function success(Request $request, AnalyticsService $analytics): \Illuminate\View\View
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        if ($merchant) {
            $analytics->track('checkout_completed', [], $user->id, $merchant->id);
        }

        return view('subscription.success', compact('merchant'));
    }

    // ── Billing Portal ────────────────────────────────────────────────

    public function portal(Request $request, BillingService $billing, AnalyticsService $analytics): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $analytics->track('portal_opened', [], $request->user()->id, $merchant->id);

        $portalUrl = $billing->createPortalSession($merchant);

        return redirect()->away($portalUrl);
    }

    // ── Cancel Subscription ───────────────────────────────────────────

    public function cancel(Request $request, BillingService $billing, AnalyticsService $analytics): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);
        abort_unless($merchant->stripe_subscription_id, 422);

        $billing->cancelSubscription($merchant);

        $analytics->track('subscription_cancelled', [], $request->user()->id, $merchant->id);

        return redirect()->route('subscription.index')
                         ->with('success', __('subscription.cancelled_notice'));
    }

    // ── Resume Subscription ───────────────────────────────────────────

    public function resume(Request $request, BillingService $billing, AnalyticsService $analytics): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);
        abort_unless($merchant->stripe_subscription_id, 422);
        abort_unless($merchant->cancel_at_period_end, 422);

        $billing->resumeSubscription($merchant);

        $analytics->track('subscription_resumed', [], $request->user()->id, $merchant->id);

        return redirect()->route('subscription.index')
                         ->with('success', __('subscription.resumed_notice'));
    }

    // ── Upgrade ───────────────────────────────────────────────────────

    public function upgrade(Request $request, BillingService $billing, AnalyticsService $analytics): RedirectResponse
    {
        $request->validate([
            'price_id' => ['required', 'string'],
        ]);

        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $newPriceId = $request->input('price_id');
        $planKey    = $billing->priceIdToPlanKey($newPriceId);
        abort_if($planKey === 'free', 422);

        if (! $merchant->stripe_subscription_id) {
            // No existing Stripe subscription — send to checkout
            $analytics->track('checkout_started', ['plan' => $planKey], $request->user()->id, $merchant->id);
            $checkoutUrl = $billing->createCheckoutSession($merchant, $newPriceId);
            return redirect()->away($checkoutUrl);
        }

        $billing->swapPlan($merchant, $newPriceId);

        $analytics->track('subscription_upgraded', ['plan' => $planKey], $request->user()->id, $merchant->id);

        return redirect()->route('subscription.index')
                         ->with('success', __('subscription.upgraded_notice'));
    }

    // ── Downgrade ─────────────────────────────────────────────────────

    public function downgrade(Request $request, BillingService $billing, AnalyticsService $analytics): RedirectResponse
    {
        $request->validate([
            'price_id' => ['required', 'string'],
        ]);

        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);
        abort_unless($merchant->stripe_subscription_id, 422);

        $newPriceId = $request->input('price_id');
        $planKey    = $billing->priceIdToPlanKey($newPriceId);

        $billing->swapPlan($merchant, $newPriceId);

        $analytics->track('subscription_downgraded', ['plan' => $planKey], $request->user()->id, $merchant->id);

        return redirect()->route('subscription.index')
                         ->with('success', __('subscription.downgraded_notice'));
    }

    // ── Webhook ───────────────────────────────────────────────────────

    public function webhook(Request $request, BillingService $billing): Response
    {
        $payload   = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        try {
            $billing->handleWebhookEvent($payload, $signature);
        } catch (SignatureVerificationException) {
            return response('Invalid signature', 400);
        } catch (\Throwable $e) {
            // Log unexpected errors but always return 200 to Stripe so it does
            // not retry an event that caused a server-side error unrelated to
            // the event content itself. Retrying would not help in this case.
            \Illuminate\Support\Facades\Log::error('Stripe webhook handler error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        return response('OK', 200);
    }
}
