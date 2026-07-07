<?php

namespace App\Enums;

enum TransactionType: string
{
    case Earn     = 'earn';
    case Redeem   = 'redeem';
    case Adjust   = 'adjust';
    case Expire   = 'expire';
    case Birthday = 'birthday';

    public function label(): string
    {
        return __("enums.transaction_type.{$this->value}");
    }

    public function isCredit(): bool
    {
        return in_array($this, [self::Earn, self::Adjust, self::Birthday]);
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Earn     => 'badge bg-success',
            self::Redeem   => 'badge bg-primary',
            self::Adjust   => 'badge bg-warning text-dark',
            self::Expire   => 'badge bg-secondary',
            self::Birthday => 'badge bg-info text-dark',
        };
    }
}
