<?php

namespace App\Enums;

enum RedemptionStatus: string
{
    case Pending   = 'pending';
    case Used      = 'used';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending',
            self::Used      => 'Used',
            self::Expired   => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending   => 'badge bg-warning text-dark',
            self::Used      => 'badge bg-success',
            self::Expired   => 'badge bg-secondary',
            self::Cancelled => 'badge bg-danger',
        };
    }
}
