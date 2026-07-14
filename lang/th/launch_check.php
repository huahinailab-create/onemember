<?php

// LAUNCH-001, evolved by MERCHANT-READY-001 / MR-001 (merchant launch
// dashboard) and MR-003 (guided launch journey).
return [
    'title'        => 'เตรียมพร้อมเปิดร้าน',
    'launch_ready' => 'พร้อมเปิดตัว',

    // Checklist items (fixed priority order)
    'profile'    => 'กรอกข้อมูลธุรกิจให้ครบ',
    'logo'       => 'อัปโหลดโลโก้ของคุณ',
    'store_url'  => 'ตั้งค่า URL ร้านของคุณ',
    'product'    => 'เพิ่มสินค้าชิ้นแรกของคุณ',
    'campaign'   => 'สร้างแคมเปญแรกของคุณ',
    'reward'     => 'เพิ่มรางวัลแรกของคุณ',
    'member'     => 'เพิ่มสมาชิกคนแรกของคุณ',
    'qr_poster'  => 'ดูและพิมพ์โปสเตอร์ QR ของคุณ',
    'storefront' => 'เยี่ยมชมหน้าร้านของคุณ',

    // Next recommended action (one per item)
    'next_title'        => 'ขั้นตอนถัดไป',
    'next_cta'          => 'เริ่ม',
    'action_profile'    => 'กรอกข้อมูลธุรกิจให้ครบ',
    'action_logo'       => 'อัปโหลดโลโก้ของคุณ',
    'action_store_url'  => 'ตั้งค่า URL ร้านของคุณ',
    'action_product'    => 'เพิ่มสินค้าชิ้นแรกของคุณ',
    'action_campaign'   => 'สร้างแคมเปญแรกของคุณ',
    'action_reward'     => 'เพิ่มรางวัลแรกของคุณ',
    'action_member'     => 'เพิ่มสมาชิกคนแรกของคุณ',
    'action_qr_poster'  => 'พิมพ์โปสเตอร์ QR ของคุณ',
    'action_storefront' => 'เยี่ยมชมหน้าร้านของคุณ',

    // MR-003 — encouraging progress copy (never technical)
    'steps_left' => '{1} เหลืออีกเพียง 1 ขั้นตอน — ใกล้เสร็จแล้ว!|[2,*] เหลืออีก :count ขั้นตอน — ทำได้ดีมาก!',
    'sr_done'    => 'เสร็จแล้ว',

    // MR-003 — why each step matters (shown after completing it)
    'why_profile'    => 'ข้อมูลธุรกิจของคุณจะแสดงบนหน้าสมัครสมาชิก หน้าร้าน และใบเสร็จ — ลูกค้ารู้ทันทีว่าคุณคือใคร',
    'why_logo'       => 'โลโก้ของคุณจะแสดงบนหน้าสมัครสมาชิก หน้าร้าน และสื่อสิ่งพิมพ์ — ลูกค้าจำร้านคุณได้ทันที',
    'why_store_url'  => 'URL ร้านคือลิงก์ที่ลูกค้าใช้ค้นหาร้านและสมัครเข้าร่วมโปรแกรมของคุณ',
    'why_product'    => 'สินค้าจะแสดงบนหน้าร้านของคุณโดยอัตโนมัติ',
    'why_campaign'   => 'แคมเปญคือวิธีที่สมาชิกสะสมแต้มหรือสแตมป์จากการซื้อทุกครั้ง',
    'why_reward'     => 'รางวัลทำให้สมาชิกอยากกลับมาอีก — เริ่มแลกได้ทันที',
    'why_member'     => 'สมาชิกของคุณเริ่มสะสมได้ทันที — ทุกครั้งที่มาคือความภักดีที่เพิ่มขึ้น',
    'why_qr_poster'  => 'ลูกค้าสแกนโปสเตอร์เพื่อสมัครเข้าร่วมโปรแกรมได้ในไม่กี่วินาที',
    'why_storefront' => 'คุณได้เห็นหน้าร้านในแบบเดียวกับที่ลูกค้าเห็นแล้ว',

    // MR-003 — first-launch celebration
    'celebrate_heading'       => 'ยินดีด้วย!',
    'celebrate_body'          => 'ธุรกิจของคุณพร้อมต้อนรับลูกค้าแล้ว',
    'celebrate_dashboard_cta' => 'ดูแดชบอร์ดของคุณ',
    'qa_storefront'           => 'ดูหน้าร้าน',
    'qa_poster'               => 'พิมพ์โปสเตอร์ QR',
    'qa_member'               => 'เพิ่มลูกค้า',
    'qa_guide'                => 'อ่านคู่มือร้านค้า',

    // Merchant health card
    'health_title'      => 'ความพร้อมของร้าน',
    'health_profile'    => 'ข้อมูลธุรกิจ',
    'health_logo'       => 'โลโก้',
    'health_store_url'  => 'URL ร้าน',
    'health_products'   => 'สินค้า',
    'health_campaigns'  => 'แคมเปญ',
    'health_members'    => 'สมาชิก',
    'health_storefront' => 'หน้าร้าน',
    'health_launch'     => 'ความคืบหน้าการเปิดตัว',

    // Status indicator text (also the accessible label)
    'status_green' => 'พร้อม',
    'status_amber' => 'ควรปรับปรุง',
    'status_red'   => 'ยังไม่มี',
];
