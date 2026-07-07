<?php

namespace App\Enums;

enum BirthdayRewardType: string
{
    case Points   = 'points';
    case Reward   = 'reward';
    case Discount = 'discount';

    public function label(): string
    {
        return __("enums.birthday_reward_type.{$this->value}");
    }
}
