<?php

// LAUNCH-001, evolved by MERCHANT-READY-001 / MR-001 (merchant launch
// dashboard) and MR-003 (guided launch journey).
return [
    'title'        => 'Get ready to launch',
    'launch_ready' => 'Launch Ready',

    // Checklist items (fixed priority order)
    'profile'    => 'Complete your business profile',
    'logo'       => 'Upload your logo',
    'store_url'  => 'Configure your store URL',
    'product'    => 'Add your first product',
    'campaign'   => 'Create your first campaign',
    'reward'     => 'Add your first reward',
    'member'     => 'Add your first member',
    'qr_poster'  => 'View and print your QR poster',
    'storefront' => 'Visit your storefront',

    // Next recommended action (one per item)
    'next_title'        => 'Next step',
    'next_cta'          => 'Start',
    'action_profile'    => 'Complete your business profile',
    'action_logo'       => 'Upload your logo',
    'action_store_url'  => 'Set your store URL',
    'action_product'    => 'Add your first product',
    'action_campaign'   => 'Create your first campaign',
    'action_reward'     => 'Add your first reward',
    'action_member'     => 'Add your first member',
    'action_qr_poster'  => 'Print your QR poster',
    'action_storefront' => 'Visit your storefront',

    // MR-003 — encouraging progress copy (never technical)
    'steps_left' => '{1} Just 1 step to go — you\'re almost there!|[2,*] :count steps to go — you\'re doing great!',
    'sr_done'    => 'completed',

    // MR-003 — why each step matters (shown after completing it)
    'why_profile'    => 'Your business details appear on your join page, storefront and receipts — customers know exactly who you are.',
    'why_logo'       => 'Your logo now appears on your join page, storefront and printed materials — customers recognise you instantly.',
    'why_store_url'  => 'Your store URL is the link customers use to find you and join your programme.',
    'why_product'    => 'Products appear automatically in your storefront.',
    'why_campaign'   => 'Your campaign is how members earn points or stamps with every purchase.',
    'why_reward'     => 'Rewards give members a reason to keep coming back — they can start redeeming right away.',
    'why_member'     => 'Your member starts earning right away — every visit now builds loyalty.',
    'why_qr_poster'  => 'Customers scan your poster to join your programme in seconds.',
    'why_storefront' => 'You\'ve seen your store exactly as customers see it.',

    // MR-003 — first-launch celebration
    'celebrate_heading'       => 'Congratulations!',
    'celebrate_body'          => 'Your business is now ready to welcome customers.',
    'celebrate_dashboard_cta' => 'See your dashboard',
    'qa_storefront'           => 'View storefront',
    'qa_poster'               => 'Print QR poster',
    'qa_member'               => 'Add a customer',
    'qa_guide'                => 'Read the merchant guide',

    // Merchant health card
    'health_title'      => 'Store health',
    'health_profile'    => 'Business Profile',
    'health_logo'       => 'Logo',
    'health_store_url'  => 'Store URL',
    'health_products'   => 'Products',
    'health_campaigns'  => 'Campaigns',
    'health_members'    => 'Members',
    'health_storefront' => 'Storefront',
    'health_launch'     => 'Launch progress',

    // Status indicator text (also the accessible label)
    'status_green' => 'Ready',
    'status_amber' => 'Attention',
    'status_red'   => 'Missing',
];
