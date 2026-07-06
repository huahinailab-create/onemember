<?php

// CORE-001 — merchant-selectable countries (ISO 3166-1 alpha-2 => native/
// English display name). Grows freely; ordering: launch market first, then
// alphabetical. Country choice drives Country Extension availability
// (ADR-012 Layer 3) and localization defaults.
return [
    'default' => 'TH',

    'list' => [
        'TH' => 'ไทย (Thailand)',
        'AU' => 'Australia',
        'CA' => 'Canada',
        'DE' => 'Deutschland (Germany)',
        'ES' => 'España (Spain)',
        'FR' => 'France',
        'GB' => 'United Kingdom',
        'HK' => '香港 (Hong Kong)',
        'ID' => 'Indonesia',
        'IN' => 'भारत (India)',
        'JP' => '日本 (Japan)',
        'KH' => 'កម្ពុជា (Cambodia)',
        'KR' => '한국 (South Korea)',
        'LA' => 'ລາວ (Laos)',
        'MM' => 'မြန်မာ (Myanmar)',
        'MY' => 'Malaysia',
        'NZ' => 'New Zealand',
        'PH' => 'Philippines',
        'SG' => 'Singapore',
        'TW' => '台灣 (Taiwan)',
        'US' => 'United States',
        'VN' => 'Việt Nam (Vietnam)',
    ],

    // Terms bundle version recorded at acceptance (GLOBAL-001 §10).
    // All legal wording is DRAFT PENDING LEGAL REVIEW (DR-33).
    'terms_version' => 'v1-draft-2026-07',
];
