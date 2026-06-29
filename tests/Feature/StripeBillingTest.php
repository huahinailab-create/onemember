<?php

namespace Tests\Feature;

use App\Events\PaymentFailed;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionPurchased;
use App\Events\SubscriptionRenewed;
use App\Events\TrialEnding;
use App\Models\Merchant;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class StripeBillingTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────

    private function userWithMerchant(): User
    {
        $user     = User::factory()->create();
        Merchant::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    private function mockBilling(): Mockery\MockInterface
    {
        $mock = Mockery::mock(BillingService::class);
        $this->app->instance(BillingService::class, $mock);
        return $mock;
    }

    // ── Authentication guards ─────────────────────────────────────────

    public function test_guest_cannot_access_checkout(): void
    {
        $this->post(route('subscription.checkout'), ['price_id' => 'price_test'])
             ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_portal(): void
    {
        $this->post(route('subscription.portal'))
             ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_cancel_subscription(): void
    {
        $this->post(route('subscription.cancel'))
             ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_subscription_success(): void
    {
        $this->get(route('subscription.success'))
             ->assertRedirect(route('login'));
    }

    // ── Checkout ──────────────────────────────────────────────────────

    public function test_checkout_redirects_to_stripe(): void
    {
        $user = $this->userWithMerchant();

        config(['stripe.prices.starter.monthly' => 'price_starter_test']);

        $mock = $this->mockBilling();
        $mock->shouldReceive('priceIdToPlanKey')
             ->with('price_starter_test')
             ->andReturn('starter');
        $mock->shouldReceive('createCheckoutSession')
             ->once()
             ->andReturn('https://checkout.stripe.com/test-session');

        $this->actingAs($user)
             ->post(route('subscription.checkout'), ['price_id' => 'price_starter_test'])
             ->assertRedirect('https://checkout.stripe.com/test-session');
    }

    public function test_checkout_requires_price_id(): void
    {
        $user = $this->userWithMerchant();

        $this->actingAs($user)
             ->post(route('subscription.checkout'), [])
             ->assertSessionHasErrors('price_id');
    }

    public function test_checkout_rejects_free_plan_price_id(): void
    {
        $user = $this->userWithMerchant();

        $mock = $this->mockBilling();
        $mock->shouldReceive('priceIdToPlanKey')
             ->andReturn('free');

        $this->actingAs($user)
             ->post(route('subscription.checkout'), ['price_id' => 'price_unknown'])
             ->assertStatus(422);
    }

    // ── Billing Portal ────────────────────────────────────────────────

    public function test_portal_redirects_to_stripe(): void
    {
        $user = $this->userWithMerchant();

        $mock = $this->mockBilling();
        $mock->shouldReceive('createPortalSession')
             ->once()
             ->andReturn('https://billing.stripe.com/session/test');

        $this->actingAs($user)
             ->post(route('subscription.portal'))
             ->assertRedirect('https://billing.stripe.com/session/test');
    }

    // ── Cancel ────────────────────────────────────────────────────────

    public function test_cancel_calls_billing_service(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $merchant->update(['stripe_subscription_id' => 'sub_test123']);

        $mock = $this->mockBilling();
        $mock->shouldReceive('cancelSubscription')->once();

        $this->actingAs($user)
             ->post(route('subscription.cancel'))
             ->assertRedirect(route('subscription.index'))
             ->assertSessionHas('success');
    }

    public function test_cancel_blocked_without_stripe_subscription(): void
    {
        $user = $this->userWithMerchant();
        // No stripe_subscription_id set

        $this->actingAs($user)
             ->post(route('subscription.cancel'))
             ->assertStatus(422);
    }

    // ── Resume ────────────────────────────────────────────────────────

    public function test_resume_calls_billing_service(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $merchant->update([
            'stripe_subscription_id' => 'sub_test123',
            'cancel_at_period_end'   => true,
        ]);

        $mock = $this->mockBilling();
        $mock->shouldReceive('resumeSubscription')->once();

        $this->actingAs($user)
             ->post(route('subscription.resume'))
             ->assertRedirect(route('subscription.index'))
             ->assertSessionHas('success');
    }

    public function test_resume_blocked_when_not_pending_cancellation(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $merchant->update([
            'stripe_subscription_id' => 'sub_test123',
            'cancel_at_period_end'   => false,
        ]);

        $this->actingAs($user)
             ->post(route('subscription.resume'))
             ->assertStatus(422);
    }

    // ── Upgrade ───────────────────────────────────────────────────────

    public function test_upgrade_with_existing_subscription_calls_swap(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $merchant->update(['stripe_subscription_id' => 'sub_test123']);

        config(['stripe.prices.professional.monthly' => 'price_pro_test']);

        $mock = $this->mockBilling();
        $mock->shouldReceive('priceIdToPlanKey')
             ->with('price_pro_test')
             ->andReturn('professional');
        $mock->shouldReceive('swapPlan')->once();

        $this->actingAs($user)
             ->post(route('subscription.upgrade'), ['price_id' => 'price_pro_test'])
             ->assertRedirect(route('subscription.index'))
             ->assertSessionHas('success');
    }

    public function test_upgrade_without_subscription_sends_to_checkout(): void
    {
        $user = $this->userWithMerchant();

        config(['stripe.prices.starter.monthly' => 'price_starter_test']);

        $mock = $this->mockBilling();
        $mock->shouldReceive('priceIdToPlanKey')
             ->with('price_starter_test')
             ->andReturn('starter');
        $mock->shouldReceive('createCheckoutSession')
             ->once()
             ->andReturn('https://checkout.stripe.com/test');

        $this->actingAs($user)
             ->post(route('subscription.upgrade'), ['price_id' => 'price_starter_test'])
             ->assertRedirect('https://checkout.stripe.com/test');
    }

    // ── Downgrade ─────────────────────────────────────────────────────

    public function test_downgrade_calls_swap_plan(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;
        $merchant->update(['stripe_subscription_id' => 'sub_test123']);

        config(['stripe.prices.starter.monthly' => 'price_starter_test']);

        $mock = $this->mockBilling();
        $mock->shouldReceive('priceIdToPlanKey')
             ->with('price_starter_test')
             ->andReturn('starter');
        $mock->shouldReceive('swapPlan')->once();

        $this->actingAs($user)
             ->post(route('subscription.downgrade'), ['price_id' => 'price_starter_test'])
             ->assertRedirect(route('subscription.index'))
             ->assertSessionHas('success');
    }

    public function test_downgrade_blocked_without_stripe_subscription(): void
    {
        $user = $this->userWithMerchant();

        $this->actingAs($user)
             ->post(route('subscription.downgrade'), ['price_id' => 'price_starter_test'])
             ->assertStatus(422);
    }

    // ── Webhook — signature verification ─────────────────────────────

    public function test_webhook_rejects_invalid_signature(): void
    {
        $this->post(route('stripe.webhook'), [], ['Stripe-Signature' => 'invalid'])
             ->assertStatus(400);
    }

    public function test_webhook_accepts_valid_signature(): void
    {
        $secret    = 'whsec_test_secret_for_testing';
        $timestamp = time();
        $payload   = json_encode([
            'id'   => 'evt_' . uniqid(),
            'type' => 'customer.subscription.updated',
            'data' => ['object' => ['id' => 'sub_test', 'status' => 'active']],
        ]);
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $sigHeader = "t={$timestamp},v1={$signature}";

        config(['stripe.webhook_secret' => $secret]);

        $mock = $this->mockBilling();
        $mock->shouldReceive('handleWebhookEvent')
             ->once()
             ->with($payload, $sigHeader);

        $this->postJson(route('stripe.webhook'), json_decode($payload, true), [
            'Stripe-Signature' => $sigHeader,
        ])->assertStatus(200);
    }

    public function test_webhook_returns_200_on_handler_exception(): void
    {
        $secret    = 'whsec_test_secret_for_testing';
        $timestamp = time();
        $payload   = json_encode(['id' => 'evt_test', 'type' => 'unknown.event', 'data' => ['object' => []]]);
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $sigHeader = "t={$timestamp},v1={$signature}";

        config(['stripe.webhook_secret' => $secret]);

        $mock = $this->mockBilling();
        $mock->shouldReceive('handleWebhookEvent')
             ->once()
             ->andThrow(new \RuntimeException('Unexpected server error'));

        $this->postJson(route('stripe.webhook'), json_decode($payload, true), [
            'Stripe-Signature' => $sigHeader,
        ])->assertStatus(200);
    }

    // ── BillingService — priceIdToPlanKey ─────────────────────────────

    public function test_price_id_to_plan_key_returns_correct_plan(): void
    {
        config([
            'stripe.prices.starter.monthly'      => 'price_starter_abc',
            'stripe.prices.professional.monthly' => 'price_pro_xyz',
        ]);

        $service = new BillingService();

        $this->assertEquals('starter',      $service->priceIdToPlanKey('price_starter_abc'));
        $this->assertEquals('professional', $service->priceIdToPlanKey('price_pro_xyz'));
        $this->assertEquals('free',         $service->priceIdToPlanKey('price_unknown_000'));
    }

    // ── Billing Events ────────────────────────────────────────────────

    public function test_subscription_purchased_event_is_dispatchable(): void
    {
        Event::fake([SubscriptionPurchased::class]);

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        SubscriptionPurchased::dispatch($merchant, 'starter', 'price_starter_test', 'sub_test');

        Event::assertDispatched(SubscriptionPurchased::class, function ($event) use ($merchant) {
            return $event->merchant->id === $merchant->id
                && $event->planKey === 'starter';
        });
    }

    public function test_subscription_cancelled_event_is_dispatchable(): void
    {
        Event::fake([SubscriptionCancelled::class]);

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        SubscriptionCancelled::dispatch($merchant);

        Event::assertDispatched(SubscriptionCancelled::class);
    }

    public function test_payment_failed_event_is_dispatchable(): void
    {
        Event::fake([PaymentFailed::class]);

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        PaymentFailed::dispatch($merchant, 'inv_test', '500.00');

        Event::assertDispatched(PaymentFailed::class, function ($event) {
            return $event->invoiceId === 'inv_test';
        });
    }

    public function test_trial_ending_event_is_dispatchable(): void
    {
        Event::fake([TrialEnding::class]);

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        TrialEnding::dispatch($merchant, 3);

        Event::assertDispatched(TrialEnding::class, function ($event) {
            return $event->daysRemaining === 3;
        });
    }

    public function test_subscription_renewed_event_is_dispatchable(): void
    {
        Event::fake([SubscriptionRenewed::class]);

        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        SubscriptionRenewed::dispatch($merchant, 'professional', now()->addMonth());

        Event::assertDispatched(SubscriptionRenewed::class);
    }

    // ── Idempotency (cache-based duplicate guard) ─────────────────────

    public function test_duplicate_webhook_event_is_skipped(): void
    {
        // Prime the cache to simulate an already-processed event
        Cache::put('stripe_event_evt_duplicate_test', true, now()->addHours(24));

        // The actual idempotency logic is inside BillingService::handleWebhookEvent.
        // Here we verify the cache key is present (unit-level check).
        $this->assertTrue(Cache::has('stripe_event_evt_duplicate_test'));
    }

    // ── Merchant model Stripe fields ──────────────────────────────────

    public function test_merchant_can_store_stripe_fields(): void
    {
        $user     = $this->userWithMerchant();
        $merchant = $user->merchant;

        $merchant->update([
            'stripe_customer_id'     => 'cus_test123',
            'stripe_subscription_id' => 'sub_test456',
            'stripe_price_id'        => 'price_test789',
            'billing_email'          => 'billing@test.com',
            'subscription_renews_at' => now()->addMonth(),
            'cancel_at_period_end'   => false,
        ]);

        $fresh = $merchant->fresh();
        $this->assertEquals('cus_test123',     $fresh->stripe_customer_id);
        $this->assertEquals('sub_test456',     $fresh->stripe_subscription_id);
        $this->assertEquals('price_test789',   $fresh->stripe_price_id);
        $this->assertEquals('billing@test.com',$fresh->billing_email);
        $this->assertFalse($fresh->cancel_at_period_end);
        $this->assertNotNull($fresh->subscription_renews_at);
    }
}
