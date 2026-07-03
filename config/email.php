<?php

return [

    'provider'        => env('MAIL_MAILER', 'ses'),
    'from_name'       => env('MAIL_FROM_NAME', 'OneMember'),
    'from_address'    => env('MAIL_FROM_ADDRESS', 'no-reply@onemember.co'),
    'reply_to'        => env('MAIL_REPLY_TO', ''),
    'support_email'   => env('SUPPORT_EMAIL', 'support@onemember.co'),
    'sales_email'     => env('SALES_EMAIL', 'sales@onemember.co'),
    'hello_email'     => env('HELLO_EMAIL', 'hello@onemember.co'),
    'privacy_email'   => env('PRIVACY_EMAIL', 'privacy@onemember.co'),
    'security_email'  => env('SECURITY_EMAIL', 'security@onemember.co'),
    'logo_url'        => env('EMAIL_LOGO_URL', ''),
    'company_name'    => env('EMAIL_COMPANY_NAME', 'OneMember'),
    'frontend_url'    => env('APP_URL', 'https://app.onemember.co'),
    'footer_text'     => env('EMAIL_FOOTER_TEXT', '© ' . date('Y') . ' OneMember. All rights reserved.'),
    'social' => [
        'twitter'  => env('EMAIL_SOCIAL_TWITTER', ''),
        'facebook' => env('EMAIL_SOCIAL_FACEBOOK', ''),
        'linkedin' => env('EMAIL_SOCIAL_LINKEDIN', ''),
    ],

];
