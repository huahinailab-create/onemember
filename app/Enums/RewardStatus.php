<?php

namespace App\Enums;

enum RewardStatus: string
{
    case Draft  = 'draft';
    case Active = 'active';

    public function label(): string
    {
        return match($this) {
            self::Draft  => 'Draft',
            self::Active => 'Active',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft  => 'badge bg-secondary',
            self::Active => 'badge bg-success',
        };
    }
}
