<?php

// ป้ายกำกับสถานะและประเภทสำหรับ App\Enums (BETA-007C).

return [

    'member_status' => [
        'active'   => 'ใช้งาน',
        'inactive' => 'ไม่ได้ใช้งาน',
        'blocked'  => 'ถูกระงับ',
    ],

    'merchant_status' => [
        'active'    => 'ใช้งาน',
        'inactive'  => 'ไม่ได้ใช้งาน',
        'suspended' => 'ถูกระงับ',
    ],

    'campaign_status' => [
        'draft'  => 'ร่าง',
        'active' => 'ใช้งาน',
        'paused' => 'หยุดชั่วคราว',
    ],

    'campaign_type' => [
        'points'   => 'คะแนน',
        'stamps'   => 'แสตมป์',
        'tiers'    => 'ระดับสมาชิก',
        'cashback' => 'เงินคืน',
    ],

    'reward_status' => [
        'draft'  => 'ร่าง',
        'active' => 'ใช้งาน',
    ],

    'reward_type' => [
        'free_item'           => 'ของฟรี',
        'discount_percentage' => 'ส่วนลดเป็นเปอร์เซ็นต์',
        'discount_amount'     => 'ส่วนลดเป็นจำนวนเงิน',
        'voucher'             => 'บัตรกำนัล',
        'custom'              => 'รางวัลกำหนดเอง',
    ],

    'redemption_status' => [
        'pending'   => 'รอดำเนินการ',
        'used'      => 'ใช้แล้ว',
        'expired'   => 'หมดอายุ',
        'cancelled' => 'ยกเลิก',
    ],

    'subscription_status' => [
        'trial'     => 'ทดลองใช้',
        'active'    => 'ใช้งาน',
        'expired'   => 'หมดอายุ',
        'cancelled' => 'ยกเลิก',
    ],

    'transaction_type' => [
        'earn'     => 'ได้รับคะแนน',
        'redeem'   => 'แลกรางวัล',
        'adjust'   => 'ปรับยอด',
        'expire'   => 'หมดอายุ',
        'birthday' => 'โบนัสวันเกิด',
    ],

    'birthday_reward_type' => [
        'points'   => 'คะแนนโบนัส',
        'reward'   => 'ของรางวัลฟรี',
        'discount' => 'ส่วนลด',
    ],

];
