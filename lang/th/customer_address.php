<?php

// CUSTOMER-001B — สมุดที่อยู่ของลูกค้า + การเลือกที่อยู่ตอนสั่งซื้อ

return [

    // สมุดที่อยู่
    'index_title'   => 'ที่อยู่ของฉัน',
    'index_sub'     => 'สมุดที่อยู่ของคุณใช้ได้กับทุกร้านบน OneMember — บันทึกครั้งเดียว ใช้ได้ทุกที่',
    'add_address'   => 'เพิ่มที่อยู่',
    'new_title'     => 'ที่อยู่ใหม่',
    'edit_title'    => 'แก้ไขที่อยู่',
    'form_sub'      => 'ร้านค้าจะเห็นเฉพาะที่อยู่ที่คุณเลือกใช้ในออเดอร์นั้นเท่านั้น',
    'search'        => 'ค้นหา',
    'search_hint'   => 'ค้นหาที่อยู่…',
    'empty_book'    => 'ยังไม่มีที่อยู่ที่บันทึกไว้ เพิ่มไว้แล้วไม่ต้องพิมพ์ซ้ำอีก',
    'empty_search'  => 'ไม่พบที่อยู่ที่ตรงกับคำค้นหา',

    // ฟิลด์
    'field_label'        => 'ชื่อที่อยู่',
    'field_label_hint'   => 'บ้าน, ที่ทำงาน…',
    'field_country'      => 'ประเทศ',
    'field_recipient'    => 'ชื่อผู้รับ',
    'field_phone'        => 'เบอร์ติดต่อ',
    'field_line1'        => 'ที่อยู่',
    'field_line1_hint'   => 'บ้านเลขที่ ถนน หมู่บ้าน…',
    'field_line2'        => 'ที่อยู่ (บรรทัดที่ 2)',
    'field_building'     => 'อาคาร',
    'field_floor'        => 'ชั้น',
    'field_unit'         => 'ห้อง / ยูนิต',
    'field_postal_code'  => 'รหัสไปรษณีย์',
    'field_landmark'     => 'จุดสังเกต',
    'field_landmark_hint' => 'ใกล้กับ…',
    'field_instructions' => 'คำแนะนำการจัดส่ง',

    // ระดับเขตการปกครอง (ตามประเทศ กำหนดใน config)
    'level_province'     => 'จังหวัด',
    'level_district'     => 'อำเภอ / เขต',
    'level_subdistrict'  => 'ตำบล / แขวง',
    'level_state_region' => 'รัฐ / ภูมิภาค',
    'level_township'     => 'เมือง (Township)',
    'level_ward_village' => 'แขวง / หมู่บ้าน',

    // ประเทศ
    'country_TH' => 'ไทย',
    'country_MM' => 'เมียนมา',

    // ชื่อที่อยู่แนะนำ
    'label_home'    => 'บ้าน',
    'label_work'    => 'ที่ทำงาน',
    'label_office'  => 'ออฟฟิศ',
    'label_parents' => 'บ้านพ่อแม่',
    'label_hotel'   => 'โรงแรม',
    'label_other'   => 'อื่น ๆ',

    // ปุ่มและป้าย
    'save'           => 'บันทึกที่อยู่',
    'cancel'         => 'ยกเลิก',
    'edit'           => 'แก้ไข',
    'delete'         => 'ลบ',
    'delete_confirm' => 'ลบที่อยู่นี้หรือไม่?',
    'archive'        => 'เก็บเข้าคลัง',
    'restore'        => 'นำกลับมาใช้',
    'duplicate'      => 'ทำสำเนา',
    'make_default'   => 'ตั้งเป็นค่าเริ่มต้น',
    'set_as_default' => 'ตั้งเป็นที่อยู่เริ่มต้นของฉัน',
    'default_badge'  => 'ค่าเริ่มต้น',
    'archived_badge' => 'เก็บในคลัง',
    'copy_suffix'    => '(สำเนา)',

    // ข้อความแจ้งผล
    'saved'       => 'บันทึกที่อยู่แล้ว',
    'deleted'     => 'ลบที่อยู่แล้ว',
    'archived'    => 'เก็บที่อยู่เข้าคลังแล้ว',
    'restored'    => 'นำที่อยู่กลับมาใช้แล้ว',
    'default_set' => 'อัปเดตที่อยู่เริ่มต้นแล้ว',
    'duplicated'  => 'ทำสำเนาที่อยู่แล้ว — คุณกำลังแก้ไขสำเนา',
    'limit_reached' => 'คุณบันทึกที่อยู่ครบจำนวนสูงสุดแล้ว',

    // ตัวช่วยแสดงผล
    'floor_short' => 'ชั้น :floor',
    'unit_short'  => 'ห้อง :unit',
    'near'        => 'ใกล้ :landmark',

    // หน้าชำระเงิน
    'deliver_to'        => 'จัดส่งไปที่',
    'checkout_add_new'  => 'เพิ่มที่อยู่ใหม่',
    'checkout_save'     => 'บันทึกที่อยู่นี้ลงสมุดที่อยู่ของฉัน',
    'checkout_manage'   => 'จัดการที่อยู่',
    'checkout_signin'   => 'เข้าสู่ระบบเพื่อใช้ที่อยู่ที่บันทึกไว้',

];
