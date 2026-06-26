<?php

namespace App\Enums;

enum RewardType: string
{
    case Discount = 'discount';
    case FreeItem = 'free_item';
    case Gift     = 'gift';
    case Cashback = 'cashback';

    public function label(): string
    {
        return match($this) {
            self::Discount => 'Discount',
            self::FreeItem => 'Free Item',
            self::Gift     => 'Gift',
            self::Cashback => 'Cashback',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Discount => 'bi-percent',
            self::FreeItem => 'bi-gift',
            self::Gift     => 'bi-box-seam',
            self::Cashback => 'bi-cash-coin',
        };
    }
}
