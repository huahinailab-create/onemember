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
        return match ($this) {
            self::Trial     => 'Trial',
            self::Active    => 'Active',
            self::Expired   => 'Expired',
            self::Cancelled => 'Cancelled',
        };
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
