<?php

namespace App\Enums;

enum BirthdayRewardType: string
{
    case Points   = 'points';
    case Reward   = 'reward';
    case Discount = 'discount';

    public function label(): string
    {
        return match($this) {
            self::Points   => 'Bonus Points',
            self::Reward   => 'Free Reward',
            self::Discount => 'Discount',
        };
    }
}
