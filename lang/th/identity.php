<?php

return [
    // Merchant scan-to-join
    'add_button'          => 'เพิ่มสมาชิก OneMember เดิม',
    'add_title'           => 'เพิ่มสมาชิก OneMember เดิม',
    'scan_heading'        => 'สแกนบัตร OneMember ของลูกค้า',
    'scan_hint'           => 'ให้ลูกค้าเปิดบัตร OneMember แล้วสแกนรหัส QR (หรือวางรหัสที่สแกนได้ด้านล่าง)',
    'scan_label'          => 'รหัส QR ที่สแกน',
    'scan_privacy_note'   => 'QR มีเพียงรหัส OneMember ที่ปลอดภัย — ไม่มีข้อมูลส่วนตัว',
    'scan_submit'         => 'ค้นหาตัวตน OneMember',
    'error_invalid_qr'    => 'รหัส QR นี้ไม่ใช่บัตร OneMember ที่ถูกต้อง',
    'error_already_member'=> 'ลูกค้าคนนี้เป็นสมาชิกของร้านคุณอยู่แล้ว',
    'error_name_required' => 'ต้องแชร์ชื่อเพื่อสร้างสมาชิกภาพ',

    // Consent screen (customer-facing, on the merchant device)
    'consent_title'       => 'ความยินยอมของลูกค้า',
    'consent_heading'     => 'เข้าร่วม :merchant?',
    'consent_phone_hint'  => 'เบอร์ที่ยืนยันแล้ว: :phone',
    'consent_body'        => ':merchant ต้องการเพิ่มคุณเป็นสมาชิกและขอข้อมูลต่อไปนี้จากตัวตน OneMember ของคุณ เอาเครื่องหมายออกจากข้อมูลที่ไม่ต้องการแชร์ — ร้านจะได้รับเฉพาะข้อมูลที่คุณอนุมัติเท่านั้น',
    'field_name'          => 'ชื่อ',
    'field_phone'         => 'เบอร์โทรศัพท์',
    'field_email'         => 'อีเมล',
    'field_birthday'      => 'วันเกิด (สำหรับรางวัลวันเกิด)',
    'field_postal_code'   => 'รหัสไปรษณีย์',
    'field_required'      => 'จำเป็น',
    'consent_footnote'    => 'แต้มสะสมของคุณที่ร้านอื่นแยกจากกันโดยสิ้นเชิง ร้านนี้จะไม่เห็นสมาชิกภาพอื่นของคุณ และคุณสามารถทบทวนการตัดสินใจได้ทุกเมื่อ',
    'consent_approve'     => 'ฉันอนุมัติ — เข้าร่วมเลย',
    'consent_decline'     => 'ยกเลิก',
    'join_success'        => 'เพิ่มสมาชิกจากตัวตน OneMember :id แล้ว — ไม่มีบัญชีซ้ำ ไม่ต้องพิมพ์ใหม่',

    // OneMember Card
    'card_title'          => 'บัตร OneMember',
    'card_label'          => 'บัตรประจำตัว',
    'card_scan_hint'      => 'แสดง QR นี้ที่ร้าน OneMember ใดก็ได้เพื่อสมัครในไม่กี่วินาที',
    'card_privacy'        => 'QR นี้มีเพียงโทเคนที่ปลอดภัย — ไม่มีข้อมูลส่วนตัวของคุณ',
];
