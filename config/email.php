<?php

return [

    'provider'        => env('MAIL_MAILER', 'ses'),
    'from_name'       => env('MAIL_FROM_NAME', 'OneMember'),
    'from_address'    => env('MAIL_FROM_ADDRESS', 'noreply@onemember.app'),
    'reply_to'        => env('MAIL_REPLY_TO', ''),
    'support_email'   => env('SUPPORT_EMAIL', 'support@onemember.app'),
    'logo_url'        => env('EMAIL_LOGO_URL', ''),
    'company_name'    => env('EMAIL_COMPANY_NAME', 'OneMember'),
    'frontend_url'    => env('APP_URL', 'https://onemember.app'),
    'footer_text'     => env('EMAIL_FOOTER_TEXT', '© ' . date('Y') . ' OneMember. All rights reserved.'),
    'social' => [
        'twitter'  => env('EMAIL_SOCIAL_TWITTER', ''),
        'facebook' => env('EMAIL_SOCIAL_FACEBOOK', ''),
        'linkedin' => env('EMAIL_SOCIAL_LINKEDIN', ''),
    ],

];
