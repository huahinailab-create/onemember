<?php

// User-facing labels for App\Enums (BETA-007C).
// Enum label() methods resolve here so status badges and type labels
// follow the interface language instead of being hardcoded English.

return [

    'member_status' => [
        'active'   => 'Active',
        'inactive' => 'Inactive',
        'blocked'  => 'Blocked',
    ],

    'merchant_status' => [
        'active'    => 'Active',
        'inactive'  => 'Inactive',
        'suspended' => 'Suspended',
    ],

    'campaign_status' => [
        'draft'  => 'Draft',
        'active' => 'Active',
        'paused' => 'Paused',
    ],

    'campaign_type' => [
        'points'   => 'Points',
        'stamps'   => 'Stamps',
        'tiers'    => 'Tiers',
        'cashback' => 'Cashback',
    ],

    'reward_status' => [
        'draft'  => 'Draft',
        'active' => 'Active',
    ],

    'reward_type' => [
        'free_item'           => 'Free Item',
        'discount_percentage' => 'Discount Percentage',
        'discount_amount'     => 'Discount Amount',
        'voucher'             => 'Voucher',
        'custom'              => 'Custom Reward',
    ],

    'redemption_status' => [
        'pending'   => 'Pending',
        'used'      => 'Used',
        'expired'   => 'Expired',
        'cancelled' => 'Cancelled',
    ],

    'subscription_status' => [
        'trial'     => 'Trial',
        'active'    => 'Active',
        'expired'   => 'Expired',
        'cancelled' => 'Cancelled',
    ],

    'transaction_type' => [
        'earn'     => 'Earned',
        'redeem'   => 'Redeemed',
        'adjust'   => 'Adjusted',
        'expire'   => 'Expired',
        'birthday' => 'Birthday Bonus',
    ],

    'birthday_reward_type' => [
        'points'   => 'Bonus Points',
        'reward'   => 'Free Reward',
        'discount' => 'Discount',
    ],

];
