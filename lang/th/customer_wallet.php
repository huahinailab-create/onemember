<?php

// CUSTOMER-001C — OneMember Wallet: บ้านของลูกค้าในระบบ OneMember
// "ความสัมพันธ์ของฉันกับร้านค้าใกล้บ้านอยู่ที่นี่"

return [

    // เมนู
    'nav_label'       => 'เมนูวอลเล็ต',
    'nav_home'        => 'หน้าแรก',
    'nav_memberships' => 'ร้านของฉัน',
    'nav_rewards'     => 'รางวัล',
    'nav_activity'    => 'กิจกรรม',
    'nav_orders'      => 'ออเดอร์',

    // หน้าแรก
    'home_title'      => 'OneMember Wallet',
    'home_welcome'    => 'ยินดีต้อนรับกลับ คุณ:name',
    'summary_label'   => 'สรุปวอลเล็ต',
    'stat_merchants'  => 'ร้านที่คุณเป็นสมาชิก',
    'stat_rewards'    => 'รางวัลที่พร้อมแลก',
    'see_all'         => 'ดูทั้งหมด',
    'quick_links'     => 'ทางลัด',

    // ร้านของฉัน
    'memberships_title'      => 'ร้านของฉัน',
    'memberships_sub'        => 'ทุกร้านที่รู้จักคุณ แต้มของแต่ละร้านแยกกัน — เป็นของคุณที่ร้านนั้น',
    'member_since'           => 'สมาชิกตั้งแต่ :date',
    'last_visit'             => 'มาล่าสุด :date',
    'last_visit_label'       => 'มาล่าสุด',
    'status_active'          => 'ใช้งานอยู่',
    'unit_points'            => 'แต้ม',
    'unit_stamps'            => 'แสตมป์',
    'rewards_count'          => '{0}ยังไม่มีรางวัลพร้อมแลก|{1}มี 1 รางวัลพร้อมแลก|[2,*]มี :count รางวัลพร้อมแลก',
    'empty_memberships'      => 'ยังไม่มีร้านที่เป็นสมาชิก',
    'empty_memberships_hint' => 'สแกน QR ของร้านบน OneMember เพื่อเข้าร่วม — ร้านของคุณจะมาอยู่ที่นี่',

    // รายละเอียดสมาชิก
    'back_to_memberships'    => 'ร้านของฉัน',
    'membership_info'        => 'ข้อมูลสมาชิก',
    'member_code'            => 'รหัสสมาชิก',
    'status'                 => 'สถานะ',
    'campaign'               => 'โปรแกรมสะสมแต้ม',
    'recent_transactions'    => 'รายการล่าสุด',
    'empty_transactions'     => 'ยังไม่มีรายการ',
    'empty_rewards_merchant' => 'ร้านนี้ยังไม่มีรางวัล',
    'merchant_contact'       => 'ติดต่อร้าน',
    'contact_phone'          => 'โทรศัพท์',
    'contact_email'          => 'อีเมล',
    'contact_address'        => 'ที่อยู่',
    'contact_none'           => 'ร้านยังไม่ได้แชร์ช่องทางติดต่อ',
    'open_storefront'        => 'เปิดหน้าร้าน',

    // รางวัล
    'rewards_title'      => 'รางวัลของฉัน',
    'rewards_sub'        => 'ทุกอย่างที่คุณรอคอย แยกตามร้าน',
    'points_required'    => ':count แต้ม',
    'reward_available'   => 'พร้อมแลก',
    'reward_coming_soon' => 'อีกนิดเดียว',
    'redeem'             => 'แลกรางวัล',
    'redeem_not_yet'     => 'การแลกผ่านวอลเล็ตกำลังจะมาเร็ว ๆ นี้ — ตอนนี้แลกได้ที่หน้าร้าน',
    'empty_rewards'      => 'ยังไม่มีรางวัล',
    'empty_rewards_hint' => 'รางวัลจากร้านที่คุณเป็นสมาชิกจะแสดงที่นี่',

    // กิจกรรม
    'activity_title'    => 'กิจกรรม',
    'activity_sub'      => 'เรื่องราวของคุณกับทุกร้าน เรียงจากใหม่สุด',
    'activity_joined'   => 'เข้าร่วม :merchant',
    'activity_earn'     => 'ได้รับแต้มที่ :merchant',
    'activity_redeem'   => 'แลกรางวัลที่ :merchant',
    'activity_adjust'   => 'ปรับแต้มที่ :merchant',
    'activity_expire'   => 'แต้มหมดอายุที่ :merchant',
    'activity_birthday' => 'รางวัลวันเกิดจาก :merchant',
    'activity_order'    => 'สั่งซื้อจาก :merchant',
    'empty_activity'    => 'ยังไม่มีอะไรที่นี่ — การมาเยือน แต้ม และออเดอร์ของคุณจะแสดงที่นี่',

    // ออเดอร์
    'orders_title'      => 'ออเดอร์ของฉัน',
    'orders_sub'        => 'ออเดอร์ที่คุณสั่งขณะเข้าสู่ระบบ OneMember',
    'order_number'      => 'ออเดอร์ #:number',
    'order_total'       => 'รวม :total',
    'order_address'     => 'ที่อยู่จัดส่งที่ใช้',
    'reorder'           => 'สั่งอีกครั้ง',
    'empty_orders'      => 'ยังไม่มีออเดอร์',
    'empty_orders_hint' => 'เข้าสู่ระบบก่อนสั่งซื้อ แล้วออเดอร์ของคุณจะถูกเก็บไว้ที่นี่',

    // การตั้งค่า
    'preferences_title'       => 'การติดต่อ',
    'pref_channel'            => 'ให้ร้านติดต่อคุณทางไหน?',
    'pref_channel_email'      => 'อีเมล',
    'pref_channel_sms'        => 'SMS',
    'pref_channel_none'       => 'ไม่ต้องติดต่อ',
    'pref_marketing'          => 'ฉันอยากรับข่าวสารและโปรโมชั่น',
    'pref_marketing_note'     => 'เปลี่ยนใจได้ทุกเมื่อ เราไม่ขายข้อมูลของคุณ',
    'pref_notifications_soon' => 'การตั้งค่าการแจ้งเตือนกำลังจะมาเร็ว ๆ นี้',
    'save_preferences'        => 'บันทึกการตั้งค่า',
    'preferences_saved'       => 'บันทึกการตั้งค่าแล้ว',

    // ฟีเจอร์ในอนาคต
    'coming_soon_title'     => 'เติบโตไปกับคุณ',
    'coming_soon'           => 'เร็ว ๆ นี้',
    'soon_membership_cards' => 'บัตรสมาชิก',
    'soon_gift_cards'       => 'บัตรของขวัญ',
    'soon_subscriptions'    => 'แพ็กเกจรายเดือน',
    'soon_appointments'     => 'นัดหมาย',
    'soon_bookings'         => 'การจอง',
    'soon_digital_wallet'   => 'กระเป๋าเงินดิจิทัล',

];
