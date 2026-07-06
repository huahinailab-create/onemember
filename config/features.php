<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OneMember Identity platform (PH2-001A)
    |--------------------------------------------------------------------------
    | Gates the OneMember Card, scan-to-join workflow, and identity creation
    | surfaces. Disable to dark-ship: routes 404 and member registration
    | stops creating identities.
    */
    'identity' => env('FEATURE_IDENTITY', true),
];
