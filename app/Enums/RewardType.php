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
        return __("enums.reward_type.{$this->value}");
    }
}
