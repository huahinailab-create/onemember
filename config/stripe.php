<?php

/*
|--------------------------------------------------------------------------
| Stripe Billing Configuration
|--------------------------------------------------------------------------
|
| All Stripe credentials and price IDs are read from environment variables.
| No secrets are hardcoded here. See .env.example for required keys.
|
| DECISION-055: Stripe is the billing source of truth. Subscription state
| changes only from verified webhook events.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    */

    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
    'secret_key'      => env('STRIPE_SECRET_KEY', ''),
    'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | ISO 4217 currency code (lowercase). THB for Thai Baht.
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'thb'),

    /*
    |--------------------------------------------------------------------------
    | Price IDs
    |--------------------------------------------------------------------------
    |
    | Stripe Price IDs for each plan. Create these in the Stripe Dashboard
    | under Products → Pricing, then paste the price_XXXX IDs into .env.
    |
    | These IDs differ between test mode and live mode — always use the
    | correct mode's IDs in each environment.
    |
    */

    'prices' => [
        'starter' => [
            'monthly' => env('STRIPE_PRICE_STARTER_MONTHLY', ''),
        ],
        'professional' => [
            'monthly' => env('STRIPE_PRICE_PROFESSIONAL_MONTHLY', ''),
        ],
        'enterprise' => [
            'monthly' => env('STRIPE_PRICE_ENTERPRISE_MONTHLY', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Checkout Configuration
    |--------------------------------------------------------------------------
    */

    'checkout' => [
        // Redirect to this URL after a successful payment
        'success_url' => env('APP_URL') . '/subscription/success?session_id={CHECKOUT_SESSION_ID}',
        // Redirect to this URL when the user cancels the checkout
        'cancel_url'  => env('APP_URL') . '/subscription',
    ],

];
