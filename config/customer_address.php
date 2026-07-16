<?php

// CUSTOMER-001B — Customer Address Book. One customer, one address book,
// many merchants. This file owns the country model: which address fields a
// country uses, what each administrative level is called there, what is
// required, and how a saved address is displayed. Adding a country is a new
// entry here — no schema change, no code change. See ADR-017 / DECISION-101.
//
// The schema stores administrative areas generically (admin_area_1 = the
// largest: province/state/region … admin_area_4 = the smallest: ward/
// village) so no Thailand assumption ever reaches the database.

return [

    'default_country' => 'TH',

    /*
    |--------------------------------------------------------------------------
    | Address book limits & labels
    |--------------------------------------------------------------------------
    | Addresses are unlimited by charter; max_addresses is a sanity cap
    | against abuse, not a product limit. Suggested labels seed the label
    | picker — customers may always type their own.
    */
    'max_addresses' => 100,

    'suggested_labels' => ['home', 'work', 'office', 'parents', 'hotel', 'other'],

    /*
    |--------------------------------------------------------------------------
    | Country model
    |--------------------------------------------------------------------------
    | fields    — which optional columns this country's form shows, in form
    |             order. line1, recipient_name, phone, label are universal
    |             and always shown.
    | required  — which of those fields are mandatory there.
    | levels    — lang-key suffix for each administrative level, so the form
    |             says "Province" in Thailand and "State / Region" in
    |             Myanmar. Missing level = country does not use it.
    | postal_code_pattern — server-side regex when postal_code is present.
    */
    'countries' => [

        'TH' => [
            'fields'   => ['line1', 'line2', 'admin_area_3', 'admin_area_2', 'admin_area_1', 'postal_code', 'building', 'floor', 'unit', 'landmark', 'delivery_instructions'],
            'required' => ['line1', 'admin_area_3', 'admin_area_2', 'admin_area_1', 'postal_code'],
            'levels'   => [
                'admin_area_1' => 'province',      // จังหวัด
                'admin_area_2' => 'district',      // อำเภอ/เขต
                'admin_area_3' => 'subdistrict',   // ตำบล/แขวง
            ],
            'postal_code_pattern' => '/^[0-9]{5}$/',
        ],

        'MM' => [
            'fields'   => ['line1', 'line2', 'admin_area_4', 'admin_area_3', 'admin_area_2', 'admin_area_1', 'postal_code', 'building', 'floor', 'unit', 'landmark', 'delivery_instructions'],
            'required' => ['line1', 'admin_area_3', 'admin_area_1'],
            'levels'   => [
                'admin_area_1' => 'state_region',  // State / Region
                'admin_area_2' => 'district',
                'admin_area_3' => 'township',
                'admin_area_4' => 'ward_village',  // Ward / Village
            ],
            'postal_code_pattern' => '/^[0-9]{5}$/',
        ],

    ],

];
