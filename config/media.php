<?php

// OMEGA-001C — Unified Media Foundation. Centralizes what was previously
// hardcoded per-controller (mimes, max size) so every future module
// (logos, avatars, staff photos, galleries, ...) validates and stores
// through one config, not copy-pasted Request rules.
// See docs/OMOS/12-ADR/ADR-013-Unified-Media-Foundation.md.

return [

    // Allowed image mime types for MediaService::validationRules(). Kept
    // identical to the current Product-image / payment-QR behaviour.
    'image_mimes' => ['jpg', 'jpeg', 'png', 'webp'],

    // Max upload size in kilobytes (Laravel `max:` rule units).
    'max_image_kb' => 2048,

    // Disk used for all media today. Swapping this to an s3-compatible
    // disk (R2, Spaces, Azure, Backblaze) later requires no business-logic
    // changes — see Part 5 of ADR-013.
    'default_disk' => 'public',

    // Longest-edge cap applied by GdImagePipeline (never upscales).
    'max_edge' => 1200,

    // WebP re-encode quality, used only when a future caller opts into
    // conversion via MediaService::optimize(). Not applied today.
    'webp_quality' => 82,

    // Named variant sizes for the future variant pipeline (Part 4 of
    // ADR-013). Declared but not generated until a module opts in.
    'variants' => [
        'thumbnail' => ['width' => 150,  'height' => 150,  'fit' => 'cover'],
        'medium'    => ['width' => 600,  'height' => 600,  'fit' => 'contain'],
        'large'     => ['width' => 1600, 'height' => 1600, 'fit' => 'contain'],
    ],

    // Per-collection storage path prefixes. Keeps merchant-scoped isolation
    // (`{collection}/{merchant_id}`) declared in one place instead of
    // string-built inline in each controller.
    'collections' => [
        'products'       => 'products',
        'payment_qr'     => 'payment-qr',
        'merchant_logos' => 'merchant-logos',
    ],
];
