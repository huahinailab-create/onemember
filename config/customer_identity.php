<?php

// CUSTOMER-001A — OneMember Identity Foundation. One person, one identity,
// many merchants. This file owns every tunable of the customer (not
// merchant) authentication stack: OTP behaviour, phone normalization, and
// the SMS provider binding. See docs/OMOS/12-ADR/ADR-016 and DECISION-100.

return [

    /*
    |--------------------------------------------------------------------------
    | OTP behaviour
    |--------------------------------------------------------------------------
    | Codes are numeric, stored HASHED (never plaintext), single-use, and
    | expire quickly. Attempt limits defend the small code space; rate
    | limits (below) defend the send path.
    */
    'otp' => [
        'length'          => 6,
        'expires_minutes' => 5,
        'max_attempts'    => 5,   // wrong guesses before the code is dead
        'resend_seconds'  => 60,  // cooldown between sends to one destination
        'max_per_hour'    => 5,   // sends per destination per hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Login throttling (password method)
    |--------------------------------------------------------------------------
    */
    'login' => [
        'max_attempts'  => 5,     // per identifier+IP …
        'decay_seconds' => 300,   // … per 5 minutes, then locked out
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS provider
    |--------------------------------------------------------------------------
    | Class-name binding for App\Services\CustomerIdentity\Contracts\SmsProvider.
    | 'log' writes codes to the Laravel log — the ONLY provider that exists
    | today (no production SMS integration yet, per the sprint charter; no
    | fake production sending). A real provider (e.g. Thai SMS gateway) is a
    | new class + this one config line.
    */
    'sms_provider' => env('CUSTOMER_SMS_PROVIDER', 'log'),

    'sms_providers' => [
        'log' => \App\Services\CustomerIdentity\LogSmsProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Phone normalization (E.164)
    |--------------------------------------------------------------------------
    | Dial code + national-number length rules per supported country.
    | trim_leading_zero: national format starts with 0 which is dropped when
    | prefixing the dial code (TH 081-234-5678 → +66812345678).
    */
    'phone_countries' => [
        'TH' => ['dial_code' => '66', 'trim_leading_zero' => true, 'national_lengths' => [9]],
        'MM' => ['dial_code' => '95', 'trim_leading_zero' => true, 'national_lengths' => [8, 9, 10]],
    ],

    'default_phone_country' => 'TH',

];
