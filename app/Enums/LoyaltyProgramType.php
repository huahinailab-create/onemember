<?php

namespace App\Enums;

enum LoyaltyProgramType: string
{
    case Points   = 'points';
    case Stamps   = 'stamps';
    case Tiers    = 'tiers';
    case Cashback = 'cashback';

    public function label(): string
    {
        return match($this) {
            self::Points   => 'Points',
            self::Stamps   => 'Stamps',
            self::Tiers    => 'Tiers',
            self::Cashback => 'Cashback',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Points   => 'bi-star-fill',
            self::Stamps   => 'bi-grid-3x3-gap-fill',
            self::Tiers    => 'bi-bar-chart-fill',
            self::Cashback => 'bi-cash-coin',
        };
    }
}
