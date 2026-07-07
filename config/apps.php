<?php

// CORE-002 — OneMember Apps registry (ADR-012 Layer 2).
// PLATFORM-002 — entries are full App Manifests (see App\Marketplace\Manifest;
// every field beyond icon/status is optional). Apps are optional per-merchant
// installs; the Core stays lightweight. status: 'available' installs today;
// 'coming_soon' shows as a marketplace placeholder. All Apps are first-party
// modules inside the monolith; third-party apps would plug in through the
// same manifest + provider shape without Core changes.
return [
    'registry' => [
        'commerce' => [
            'icon'          => 'bi-shop',
            'status'        => 'available',
            'version'       => '1.1.0',
            'category'      => 'sales',
            'permissions'   => ['commerce.products.manage', 'commerce.orders.manage', 'commerce.settings.manage'],
            'feature_flags' => [],
            'docs'          => 'docs/dev/apps/commerce.md',
        ],
        'queue' => [
            'icon'          => 'bi-people-fill',
            'status'        => 'available',
            'version'       => '1.0.0',
            'category'      => 'operations',
            'permissions'   => ['queue.tickets.manage', 'queue.counters.manage'],
            'feature_flags' => ['sms_notifications' => false, 'line_notifications' => false],
            'default_config' => ['avg_service_minutes' => 5],
            'migrations'    => 'app/Apps/Queue/database/migrations',
            'provider'      => \App\Apps\Queue\QueueAppProvider::class,
            'docs'          => 'docs/dev/apps/queue.md',
        ],
        'restaurant' => [
            'icon'     => 'bi-cup-hot',
            'status'   => 'coming_soon',
            'category' => 'vertical',
        ],
        'appointments' => [
            'icon'     => 'bi-calendar-check',
            'status'   => 'coming_soon',
            'category' => 'operations',
        ],
        'inventory' => [
            'icon'     => 'bi-box-seam',
            'status'   => 'coming_soon',
            'category' => 'operations',
            // Procurement's goods-received hooks target this app (PLATFORM-002 Part 9).
        ],
        'ai_marketing' => [
            'icon'     => 'bi-stars',
            'status'   => 'coming_soon',
            'category' => 'growth',
        ],
        'crm' => [
            'icon'     => 'bi-people',
            'status'   => 'coming_soon',
            'category' => 'growth',
        ],
        'pos' => [
            'icon'     => 'bi-upc-scan',
            'status'   => 'coming_soon',
            'category' => 'sales',
        ],
        'accounting' => [
            'icon'     => 'bi-journal-text',
            'status'   => 'coming_soon',
            'category' => 'back_office',
        ],
        'staff' => [
            'icon'     => 'bi-person-badge',
            'status'   => 'coming_soon',
            'category' => 'back_office',
        ],
        'hotel' => [
            'icon'     => 'bi-building',
            'status'   => 'coming_soon',
            'category' => 'vertical',
        ],
    ],
];
