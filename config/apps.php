<?php

// CORE-002 — OneMember Apps registry (ADR-012 Layer 2).
// Apps are optional per-merchant installs; merchants install only what they
// need. The Core stays lightweight. status: 'available' installs today;
// 'coming_soon' shows as a marketplace placeholder (no SDK in this phase —
// all Apps are first-party modules inside the monolith).
return [
    'registry' => [
        'commerce' => [
            'icon'   => 'bi-shop',
            'status' => 'available',
        ],
        'restaurant' => [
            'icon'   => 'bi-cup-hot',
            'status' => 'coming_soon',
        ],
        'appointments' => [
            'icon'   => 'bi-calendar-check',
            'status' => 'coming_soon',
        ],
        'inventory' => [
            'icon'   => 'bi-box-seam',
            'status' => 'coming_soon',
        ],
        'ai_marketing' => [
            'icon'   => 'bi-stars',
            'status' => 'coming_soon',
        ],
        'crm' => [
            'icon'   => 'bi-people',
            'status' => 'coming_soon',
        ],
        'pos' => [
            'icon'   => 'bi-upc-scan',
            'status' => 'coming_soon',
        ],
        'accounting' => [
            'icon'   => 'bi-journal-text',
            'status' => 'coming_soon',
        ],
        'staff' => [
            'icon'   => 'bi-person-badge',
            'status' => 'coming_soon',
        ],
        'hotel' => [
            'icon'   => 'bi-building',
            'status' => 'coming_soon',
        ],
    ],
];
