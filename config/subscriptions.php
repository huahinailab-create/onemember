<?php

/*
|--------------------------------------------------------------------------
| Subscription Plans Configuration
|--------------------------------------------------------------------------
|
| All plan names, descriptions, and feature flags live here.
| No prices are stored in this file — pricing amounts are determined
| by the Product Owner before commercial launch (DECISION-014).
| Plan limits (member caps etc.) are deferred per DECISION-013.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    |
    | Every new merchant receives a Professional trial on registration.
    | DECISION-039, docs/11-Pricing-Strategy.md
    |
    */

    'trial' => [
        'plan' => 'professional',
        'days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plans
    |--------------------------------------------------------------------------
    |
    | feature flags default to false — features must be explicitly enabled
    | per plan. Limit values of null mean TBD (deferred per DECISION-013).
    | Do not hardcode prices here.
    |
    */

    'plans' => [

        'free' => [
            'name'        => 'Free',
            'description' => 'For sole traders and micro-businesses testing OneMember or running a minimal programme.',
            'features'    => [
                'members_limit'        => null,   // TBD — DECISION-013
                'campaigns_limit'      => null,   // TBD — DECISION-013
                'rewards_limit'        => null,   // TBD — DECISION-013
                'birthday_rewards'     => false,
                'reports'              => false,
                'staff_accounts'       => false,
                'api_access'           => false,
                'priority_support'     => false,
                'custom_branding'      => false,
                'multi_location'       => false,
                'data_export'          => false,
            ],
        ],

        'starter' => [
            'name'        => 'Starter',
            'description' => 'For small businesses with a growing member base who need core loyalty features.',
            'features'    => [
                'members_limit'        => null,   // TBD — DECISION-013
                'campaigns_limit'      => null,   // TBD — DECISION-013
                'rewards_limit'        => null,   // TBD — DECISION-013
                'birthday_rewards'     => true,
                'reports'              => false,
                'staff_accounts'       => false,
                'api_access'           => false,
                'priority_support'     => false,
                'custom_branding'      => false,
                'multi_location'       => false,
                'data_export'          => true,
            ],
        ],

        'professional' => [
            'name'        => 'Professional',
            'description' => 'For established businesses that need the full feature set, automation, and reporting.',
            'features'    => [
                'members_limit'        => null,   // TBD — DECISION-013
                'campaigns_limit'      => null,   // TBD — DECISION-013
                'rewards_limit'        => null,   // TBD — DECISION-013
                'birthday_rewards'     => true,
                'reports'              => true,
                'staff_accounts'       => true,
                'api_access'           => false,
                'priority_support'     => true,
                'custom_branding'      => true,
                'multi_location'       => false,
                'data_export'          => true,
            ],
        ],

        'enterprise' => [
            'name'        => 'Enterprise',
            'description' => 'For businesses with multiple locations, large member bases, or custom integration needs.',
            'features'    => [
                'members_limit'        => null,   // Unlimited
                'campaigns_limit'      => null,   // Unlimited
                'rewards_limit'        => null,   // Unlimited
                'birthday_rewards'     => true,
                'reports'              => true,
                'staff_accounts'       => true,
                'api_access'           => true,
                'priority_support'     => true,
                'custom_branding'      => true,
                'multi_location'       => true,
                'data_export'          => true,
            ],
        ],

    ],

];
