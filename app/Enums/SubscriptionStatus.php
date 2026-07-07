<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trial     = 'trial';
    case Active    = 'active';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __("enums.subscription_status.{$this->value}");
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Trial     => 'bg-info',
            self::Active    => 'bg-success',
            self::Expired   => 'bg-warning text-dark',
            self::Cancelled => 'bg-secondary',
        };
    }
}
