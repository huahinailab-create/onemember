<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Corporate Domain (onemember.co)
    |--------------------------------------------------------------------------
    | Serves the public-facing marketing website. Routes registered under this
    | domain show only corporate pages. App routes redirect to APP_DOMAIN.
    */
    'corporate' => env('CORPORATE_DOMAIN', 'onemember.co'),

    /*
    |--------------------------------------------------------------------------
    | www alias (www.onemember.co)
    |--------------------------------------------------------------------------
    | 301-redirects to the bare corporate domain. Keeps a single canonical URL.
    */
    'www' => env('CORPORATE_WWW_DOMAIN', 'www.onemember.co'),

    /*
    |--------------------------------------------------------------------------
    | Application Domain (app.onemember.co)
    |--------------------------------------------------------------------------
    | Serves the merchant dashboard, auth pages, and all application routes.
    */
    'app' => env('APP_DOMAIN', 'app.onemember.co'),
];
