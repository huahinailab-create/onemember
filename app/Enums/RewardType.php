<?php

namespace App\Enums;

enum RewardType: string
{
    case FreeItem           = 'free_item';
    case DiscountPercentage = 'discount_percentage';
    case DiscountAmount     = 'discount_amount';
    case Voucher            = 'voucher';
    case Custom             = 'custom';

    public function label(): string
    {
        return match($this) {
            self::FreeItem           => 'Free Item',
            self::DiscountPercentage => 'Discount Percentage',
            self::DiscountAmount     => 'Discount Amount',
            self::Voucher            => 'Voucher',
            self::Custom             => 'Custom Reward',
        };
    }
}
