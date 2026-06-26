<?php

namespace App\Enums;

enum MerchantStatus: string
{
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::Active    => 'Active',
            self::Inactive  => 'Inactive',
            self::Suspended => 'Suspended',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Active    => 'badge bg-success',
            self::Inactive  => 'badge bg-secondary',
            self::Suspended => 'badge bg-danger',
        };
    }
}
