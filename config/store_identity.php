<?php

// OMEGA-001E — Store Identity & Public URL Foundation. Single source of
// truth for words a merchant's Store URL (the `merchants.slug` column)
// may never take, because they either collide with a real top-level
// application route or would be confusing/unsafe as a public path
// segment. See docs/OMOS/12-ADR/ADR-015-Store-Identity-and-Public-URL.md
// and DECISION-098.
//
// Referenced by App\Services\StoreIdentity\StoreIdentityService — never
// duplicate this list elsewhere.

return [

    'reserved_words' => [
        // Explicitly named in the OMEGA-001E spec
        'admin', 'api', 'login', 'register', 'dashboard', 'store', 'queue',
        'booking', 'commerce', 'settings', 'support', 'help', 'docs',
        'privacy', 'terms',

        // Additional real top-level route segments today (routes/web.php) —
        // reserved defensively in case a future scheme ever serves a
        // merchant's Store URL directly under the app root rather than
        // under /store/{slug}.
        'join', 'members', 'campaigns', 'rewards', 'transactions', 'reports',
        'apps', 'launch-kit', 'counter', 'onboarding', 'subscription',
        'identity', 'media', 'storage', 'build', 'up', 'verify-email',
        'forgot-password', 'reset-password', 'confirm-password', 'logout',
        'go-live', 'control-room',
    ],

];
