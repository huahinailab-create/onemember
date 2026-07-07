<?php

namespace App\Enums;

enum MerchantStatus: string
{
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return __("enums.merchant_status.{$this->value}");
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
