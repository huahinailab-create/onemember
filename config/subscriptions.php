<?php

/*
|--------------------------------------------------------------------------
| Subscription Plans Configuration
|--------------------------------------------------------------------------
|
| All plan names, descriptions, feature flags, and limits live here.
| No prices are stored in this file — pricing amounts are determined
| by the Product Owner before commercial launch (DECISION-014).
|
| LIMITS NOTE (DECISION-013):
| Limit values are PLACEHOLDERS only. Final limits will be confirmed
| after beta testing and recorded in docs/08-Product-Decisions.md.
| null = unlimited.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    |
    | Every new merchant receives a Professional trial on registration.
    | DECISION-039, DECISION-040, docs/11-Pricing-Strategy.md
    |
    */

    'trial' => [
        'plan' => 'professional',
        'days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Warning Thresholds
    |--------------------------------------------------------------------------
    |
    | Controls when usage warnings are shown to merchants.
    |
    */

    'warning_threshold'       => 80,   // % — show yellow warning above this
    'limit_reached_threshold' => 100,  // % — block creation at or above this

    /*
    |--------------------------------------------------------------------------
    | Plans
    |--------------------------------------------------------------------------
    */

    'plans' => [

        'free' => [
            'name'        => 'Free',
            'description' => 'For sole traders and micro-businesses testing OneMember or running a minimal programme.',

            // PLACEHOLDER limits — finalise after beta (DECISION-013)
            'limits' => [
                'members'              => 100,  // PO-confirmed (DECISION-081): free plan = 100 members
                'campaigns'            => 1,    // PLACEHOLDER
                'rewards_per_campaign' => 3,    // PLACEHOLDER
                'staff_users'          => 0,    // PLACEHOLDER
            ],

            'features' => [
                'birthday_rewards'  => false,
                'reports'           => false,
                'staff_accounts'    => false,
                'api_access'        => false,
                'priority_support'  => false,
                'custom_branding'   => false,
                'multi_location'    => false,
                'data_export'       => false,
            ],
        ],

        'starter' => [
            'name'        => 'Starter',
            'description' => 'For small businesses with a growing member base who need core loyalty features.',

            // PLACEHOLDER limits — finalise after beta (DECISION-013)
            'limits' => [
                'members'              => 500,  // PLACEHOLDER
                'campaigns'            => 3,    // PLACEHOLDER
                'rewards_per_campaign' => 10,   // PLACEHOLDER
                'staff_users'          => 2,    // PLACEHOLDER
            ],

            'features' => [
                'birthday_rewards'  => true,
                'reports'           => false,
                'staff_accounts'    => false,
                'api_access'        => false,
                'priority_support'  => false,
                'custom_branding'   => false,
                'multi_location'    => false,
                'data_export'       => true,
            ],
        ],

        'professional' => [
            'name'        => 'Professional',
            'description' => 'For established businesses that need the full feature set, automation, and reporting.',

            // null = unlimited
            'limits' => [
                'members'              => null,
                'campaigns'            => null,
                'rewards_per_campaign' => null,
                'staff_users'          => null,
            ],

            'features' => [
                'birthday_rewards'  => true,
                'reports'           => true,
                'staff_accounts'    => true,
                'api_access'        => false,
                'priority_support'  => true,
                'custom_branding'   => true,
                'multi_location'    => false,
                'data_export'       => true,
            ],
        ],

        'enterprise' => [
            'name'        => 'Enterprise',
            'description' => 'For businesses with multiple locations, large member bases, or custom integration needs.',

            // null = unlimited
            'limits' => [
                'members'              => null,
                'campaigns'            => null,
                'rewards_per_campaign' => null,
                'staff_users'          => null,
            ],

            'features' => [
                'birthday_rewards'  => true,
                'reports'           => true,
                'staff_accounts'    => true,
                'api_access'        => true,
                'priority_support'  => true,
                'custom_branding'   => true,
                'multi_location'    => true,
                'data_export'       => true,
            ],
        ],

    ],

];
