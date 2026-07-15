<?php

namespace App\Services\Wallet;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\RewardStatus;
use App\Models\Customer;
use App\Models\CustomerMemberLink;
use App\Models\Member;
use App\Models\Order;
use App\Models\Reward;
use App\Models\Transaction;
use Illuminate\Support\Collection;

/**
 * CUSTOMER-001C — the OneMember Wallet read model. The wallet is the
 * customer's home in the ecosystem: every merchant relationship, reward,
 * and activity in one place — while each merchant's loyalty programme stays
 * its own (points are NEVER combined across merchants, ADR-010).
 *
 * Everything here is read-only aggregation over the customer's own
 * consented CustomerMemberLinks; no merchant data outside those links is
 * ever reachable. Future wallet surfaces (gift cards, subscriptions,
 * appointments, membership cards) plug in as new aggregate methods without
 * touching the existing ones.
 */
class WalletService
{
    /** Live memberships with everything the wallet cards need. */
    public function memberships(Customer $customer): Collection
    {
        return $customer->liveLinks()
            ->with([
                'member' => fn ($q) => $q->withTrashed(),
                'member.merchant',
                'member.merchant.loyaltyPrograms' => fn ($q) => $q->where('status', CampaignStatus::Active),
            ])
            ->orderByDesc('linked_at')
            ->get()
            ->filter(fn (CustomerMemberLink $link) => $link->member !== null && $link->member->merchant !== null);
    }

    /** One membership, ONLY if it belongs to this customer (else null). */
    public function membership(Customer $customer, string $memberUuid): ?CustomerMemberLink
    {
        return $this->memberships($customer)
            ->first(fn (CustomerMemberLink $link) => $link->member->public_uuid === $memberUuid);
    }

    /** Home-screen summary numbers. */
    public function summary(Customer $customer): array
    {
        $memberships = $this->memberships($customer);

        return [
            'merchants'         => $memberships->count(),
            'rewards_available' => $this->rewardsByMerchant($customer)
                ->flatten(1)
                ->where('status', 'available')
                ->count(),
        ];
    }

    /**
     * Rewards across all memberships, grouped by merchant name. Statuses are
     * only what the domain truly has (no invented "expired": rewards have no
     * expiry — redemptions do): `available` = active reward, enough points,
     * stock remaining; `coming_soon` = active reward the member can't use yet.
     *
     * @return Collection<string, Collection<int, array>>
     */
    public function rewardsByMerchant(Customer $customer): Collection
    {
        return $this->memberships($customer)
            ->mapWithKeys(function (CustomerMemberLink $link) {
                $member  = $link->member;
                $rewards = Reward::where('merchant_id', $member->merchant_id)
                    ->where('status', RewardStatus::Active)
                    ->orderBy('points_required')
                    ->get()
                    ->map(fn (Reward $reward) => [
                        'reward'   => $reward,
                        'member'   => $member,
                        'merchant' => $member->merchant,
                        'status'   => $this->rewardStatus($reward, $member),
                    ]);

                return [$member->merchant->displayName() => $rewards];
            })
            ->filter(fn (Collection $rewards) => $rewards->isNotEmpty());
    }

    private function rewardStatus(Reward $reward, Member $member): string
    {
        $inStock   = $reward->remainingQuantity() === null || $reward->remainingQuantity() > 0;
        $affordable = $member->total_points >= $reward->points_required;

        return ($inStock && $affordable) ? 'available' : 'coming_soon';
    }

    /**
     * Chronological activity, newest first: joins, every loyalty transaction
     * (earn / redeem / adjust / expire / birthday), and wallet orders. Each
     * item is a uniform array the view can render without model-specific
     * branching.
     */
    public function activity(Customer $customer, int $limit = 25): Collection
    {
        $memberships = $this->memberships($customer);
        $memberIds   = $memberships->pluck('member.id');
        $merchantNames = $memberships->mapWithKeys(
            fn (CustomerMemberLink $link) => [$link->member->merchant_id => $link->member->merchant->displayName()]
        );

        $joins = $memberships->map(fn (CustomerMemberLink $link) => [
            'type'     => 'joined',
            'merchant' => $link->member->merchant->displayName(),
            'points'   => null,
            'note'     => null,
            'at'       => $link->linked_at ?? $link->created_at,
        ]);

        $transactions = Transaction::whereIn('member_id', $memberIds)
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Transaction $tx) => [
                'type'     => $tx->type->value,
                'merchant' => $merchantNames[$tx->merchant_id] ?? '',
                'points'   => $tx->points,
                'note'     => $tx->note,
                'at'       => $tx->created_at,
            ]);

        $orders = $this->orders($customer)
            ->take($limit)
            ->map(fn (Order $order) => [
                'type'     => 'order',
                'merchant' => $order->merchant->displayName(),
                'points'   => null,
                'note'     => __('customer_wallet.order_number', ['number' => $order->id]),
                'at'       => $order->created_at,
            ]);

        return $joins->concat($transactions)->concat($orders)
            ->sortByDesc('at')
            ->take($limit)
            ->values();
    }

    /**
     * Wallet order history: orders placed while signed in (customer_id,
     * CUSTOMER-001C onwards) plus orders on linked member records. Only data
     * that genuinely exists — guest orders without either link stay invisible.
     */
    public function orders(Customer $customer): Collection
    {
        $memberIds = $this->memberships($customer)->pluck('member.id');

        return Order::with(['items', 'merchant'])
            ->where(function ($query) use ($customer, $memberIds) {
                $query->where('customer_id', $customer->id)
                    ->orWhereIn('member_id', $memberIds);
            })
            ->latest('created_at')
            ->get()
            ->filter(fn (Order $order) => $order->merchant !== null)
            ->values();
    }

    /** The label key for a membership's balance: points or stamps. */
    public function balanceUnit(CustomerMemberLink $link): string
    {
        $programme = $link->member->merchant->loyaltyPrograms->first();

        return $programme?->type === LoyaltyProgramType::Stamps ? 'stamps' : 'points';
    }
}
