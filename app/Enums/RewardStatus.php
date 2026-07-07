<?php

namespace App\Enums;

enum RewardStatus: string
{
    case Draft  = 'draft';
    case Active = 'active';

    public function label(): string
    {
        return __("enums.reward_status.{$this->value}");
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft  => 'badge bg-secondary',
            self::Active => 'badge bg-success',
        };
    }
}
