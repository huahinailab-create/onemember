<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Enums\TransactionType;
use App\Events\Domain\MemberCreated;
use App\Events\Domain\MerchantRegistered;
use App\Events\Domain\OrderPlaced;
use App\Events\Domain\PaymentReceived;
use App\Events\Domain\PurchaseRecorded;
use App\Events\Domain\RewardRedeemed;
use App\Events\Domain\SubscriptionChanged;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Redemption;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/** PLATFORM-002 Part 3 — model lifecycle emits stable domain events. */
class DomainEventBusTest extends TestCase
{
    use RefreshDatabase;

    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create(['user_id' => $user->id]);
    }

    public function test_member_creation_emits_member_created(): void
    {
        Event::fake([MemberCreated::class]);

        $member = Member::factory()->create(['merchant_id' => $this->merchant->id]);

        Event::assertDispatched(MemberCreated::class, function (MemberCreated $e) use ($member) {
            return $e->name() === 'member.created'
                && $e->merchantId() === $this->merchant->id
                && $e->payload()['member_id'] === $member->id;
        });
    }

    public function test_merchant_creation_emits_merchant_registered(): void
    {
        Event::fake([MerchantRegistered::class]);

        $owner = User::factory()->create();
        Merchant::factory()->create(['user_id' => $owner->id]);

        Event::assertDispatched(MerchantRegistered::class, fn ($e) => $e->name() === 'merchant.registered');
    }

    public function test_earn_transaction_emits_purchase_recorded_but_adjust_does_not(): void
    {
        Event::fake([PurchaseRecorded::class]);

        $member  = Member::factory()->create(['merchant_id' => $this->merchant->id]);
        $program = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id]);

        Transaction::create([
            'merchant_id' => $this->merchant->id, 'member_id' => $member->id,
            'loyalty_program_id' => $program->id, 'type' => TransactionType::Earn,
            'points' => 10, 'balance_before' => 0, 'balance_after' => 10,
        ]);
        Transaction::create([
            'merchant_id' => $this->merchant->id, 'member_id' => $member->id,
            'loyalty_program_id' => $program->id, 'type' => TransactionType::Adjust,
            'points' => 5, 'balance_before' => 10, 'balance_after' => 15,
        ]);

        Event::assertDispatchedTimes(PurchaseRecorded::class, 1);
    }

    public function test_redemption_emits_reward_redeemed(): void
    {
        Event::fake([RewardRedeemed::class]);

        $member  = Member::factory()->create(['merchant_id' => $this->merchant->id]);
        $program = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id]);
        $reward  = Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $program->id]);
        $ledger  = Transaction::create([
            'merchant_id' => $this->merchant->id, 'member_id' => $member->id,
            'loyalty_program_id' => $program->id, 'type' => TransactionType::Redeem,
            'points' => -100, 'balance_before' => 100, 'balance_after' => 0,
        ]);

        Redemption::create([
            'merchant_id' => $this->merchant->id, 'member_id' => $member->id,
            'reward_id' => $reward->id, 'transaction_id' => $ledger->id,
            'points_used' => 100, 'status' => 'used',
        ]);

        Event::assertDispatched(RewardRedeemed::class, fn ($e) => $e->name() === 'reward.redeemed');
    }

    public function test_order_lifecycle_emits_order_placed_and_payment_received(): void
    {
        Event::fake([OrderPlaced::class, PaymentReceived::class]);

        $order = Order::create([
            'merchant_id' => $this->merchant->id, 'customer_name' => 'Somchai',
            'customer_phone' => '0812345678', 'fulfillment_type' => 'pickup',
            'status' => 'placed', 'payment_status' => 'unpaid',
            'subtotal' => 100, 'fulfillment_fee' => 0, 'total' => 100,
        ]);

        Event::assertDispatched(OrderPlaced::class);
        Event::assertNotDispatched(PaymentReceived::class);

        $order->update(['payment_status' => 'paid']);

        Event::assertDispatched(PaymentReceived::class, fn ($e) => $e->name() === 'payment.received');
    }

    public function test_subscription_status_change_emits_subscription_changed(): void
    {
        Event::fake([SubscriptionChanged::class]);

        $this->merchant->update(['subscription_status' => SubscriptionStatus::Active]);

        Event::assertDispatched(SubscriptionChanged::class, fn ($e) => $e->to === 'active');

        // Unrelated updates stay silent.
        $this->merchant->update(['city' => 'Bangkok']);
        Event::assertDispatchedTimes(SubscriptionChanged::class, 1);
    }
}
