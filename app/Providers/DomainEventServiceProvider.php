<?php

namespace App\Providers;

use App\Enums\TransactionType;
use App\Events\Domain\MemberCreated;
use App\Events\Domain\MerchantRegistered;
use App\Events\Domain\OrderPlaced;
use App\Events\Domain\PaymentReceived;
use App\Events\Domain\PurchaseRecorded;
use App\Events\Domain\RewardRedeemed;
use App\Events\Domain\SubscriptionChanged;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Redemption;
use App\Models\Transaction;
use Illuminate\Support\ServiceProvider;

/**
 * PLATFORM-002 Part 3 — wires model lifecycle to the domain event bus.
 *
 * Hooking Eloquent lifecycle (instead of individual controllers/services)
 * guarantees every code path — UI, import, counter mode, identity flows,
 * future API — emits the same events, with zero coupling added to callers.
 *
 * Consumers subscribe with a wildcard listener on 'App\Events\Domain\*'
 * (webhooks Part 4, automation Part 6). Event registry: docs/dev/event-bus.md
 */
class DomainEventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Part 4: every domain event fans out to subscribed webhooks.
        \Illuminate\Support\Facades\Event::listen(
            'App\Events\Domain\*',
            [\App\Webhooks\WebhookDispatcher::class, 'handle'],
        );

        Member::created(fn (Member $member) => event(new MemberCreated($member)));

        Merchant::created(fn (Merchant $merchant) => event(new MerchantRegistered($merchant)));

        Merchant::updated(function (Merchant $merchant) {
            if ($merchant->wasChanged(['subscription_status', 'subscription_plan'])) {
                event(new SubscriptionChanged(
                    $merchant,
                    from: $merchant->getOriginal('subscription_status')?->value
                        ?? $merchant->getOriginal('subscription_plan')?->value,
                    to: $merchant->subscription_status?->value ?? $merchant->subscription_plan?->value,
                ));
            }
        });

        // Earn entries on the immutable ledger are the purchase signal —
        // birthday/expiry/adjust movements are deliberately not "purchases".
        Transaction::created(function (Transaction $transaction) {
            if ($transaction->type === TransactionType::Earn) {
                event(new PurchaseRecorded($transaction));
            }
        });

        Redemption::created(fn (Redemption $redemption) => event(new RewardRedeemed($redemption)));

        Order::created(fn (Order $order) => event(new OrderPlaced($order)));

        // Merchant self-reported "paid" marker (ADR-011: money never touches
        // OneMember — this only records that the merchant said so).
        Order::updated(function (Order $order) {
            if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
                event(new PaymentReceived($order));
            }
        });
    }
}
