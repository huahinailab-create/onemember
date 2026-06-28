<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case Free         = 'free';
    case Starter      = 'starter';
    case Professional = 'professional';
    case Enterprise   = 'enterprise';

    public function label(): string
    {
        return match ($this) {
            self::Free         => config('subscriptions.plans.free.name', 'Free'),
            self::Starter      => config('subscriptions.plans.starter.name', 'Starter'),
            self::Professional => config('subscriptions.plans.professional.name', 'Professional'),
            self::Enterprise   => config('subscriptions.plans.enterprise.name', 'Enterprise'),
        };
    }
}
