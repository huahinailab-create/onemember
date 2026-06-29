<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics Enabled
    |--------------------------------------------------------------------------
    | When false, all AnalyticsService calls are no-ops. Safe to disable in
    | local development and test environments without code changes.
    */
    'enabled' => (bool) env('ANALYTICS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Provider
    |--------------------------------------------------------------------------
    | The active analytics provider. Currently supported values:
    |   'posthog' — PostHog product analytics
    |   'null'    — no-op provider (useful in testing or staging)
    |
    | Clarity (Microsoft) and other client-side tools are injected via the
    | layout's <head> section using their own snippet; they do not use this
    | server-side abstraction.
    */
    'provider' => env('ANALYTICS_PROVIDER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | PostHog
    |--------------------------------------------------------------------------
    */
    'posthog' => [
        'api_key'  => env('POSTHOG_API_KEY', ''),
        'host'     => env('POSTHOG_HOST', 'https://app.posthog.com'),
        'timeout'  => (int) env('POSTHOG_TIMEOUT', 2),   // seconds; keep low to avoid slowing requests
    ],

    /*
    |--------------------------------------------------------------------------
    | Sentry — Exception Reporting
    |--------------------------------------------------------------------------
    | When a DSN is provided, AnalyticsService::exception() will forward
    | captured exceptions to Sentry via the PHP SDK (if installed).
    | When the DSN is empty, the method is a no-op.
    */
    'sentry' => [
        'dsn'         => env('SENTRY_DSN', ''),
        'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),
        'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    | Granular control over which event categories are tracked.
    | All default to true when analytics is enabled.
    */
    'features' => [
        'page_views'     => (bool) env('ANALYTICS_TRACK_PAGE_VIEWS', true),
        'events'         => (bool) env('ANALYTICS_TRACK_EVENTS', true),
        'exceptions'     => (bool) env('ANALYTICS_TRACK_EXCEPTIONS', true),
        'identify'       => (bool) env('ANALYTICS_IDENTIFY', true),
    ],

];
