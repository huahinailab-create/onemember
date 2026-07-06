<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Merchant;

/**
 * ADMIN-001 — platform-side merchant success/health signals for the admin
 * dashboard follow-up widget. Each signal is a count + a filter key that the
 * merchant list understands (see Admin\MerchantController), so admins can
 * click through to the exact cohort.
 */
class MerchantHealthService
{
    /** @return list<array{key:string,count:int,label_key:string}> */
    public function signals(): array
    {
        $defs = [
            'no_campaign'    => Merchant::doesntHave('loyaltyPrograms'),
            'no_reward'      => Merchant::doesntHave('rewards'),
            'no_members'     => Merchant::doesntHave('members'),
            'no_transactions'=> Merchant::doesntHave('transactions'),
            'trial_ending'   => Merchant::where('subscription_status', SubscriptionStatus::Trial)
                                    ->whereNotNull('trial_ends_at')
                                    ->whereBetween('trial_ends_at', [now(), now()->addDays(7)]),
            'extended'       => Merchant::whereHas('trialExtensions'),
            // High usage but unpaid: still on trial/expired yet has real activity.
            'high_unpaid'    => Merchant::whereIn('subscription_status', [SubscriptionStatus::Trial, SubscriptionStatus::Expired])
                                    ->has('members', '>=', 20),
            // Inactive: no transaction in the last 30 days (but has members).
            'inactive'       => Merchant::has('members')
                                    ->whereDoesntHave('transactions', fn ($q) => $q->where('created_at', '>=', now()->subDays(30))),
        ];

        $signals = [];
        foreach ($defs as $key => $query) {
            $signals[] = [
                'key'       => $key,
                'count'     => (clone $query)->count(),
                'label_key' => "admin_health.{$key}",
            ];
        }

        return $signals;
    }
}
