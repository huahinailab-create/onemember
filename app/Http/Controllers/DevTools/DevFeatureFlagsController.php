<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use Illuminate\View\View;

class DevFeatureFlagsController extends DevController
{
    public function __construct(DevAuditLogger $audit)
    {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $flags = [
            'DEV_TOOLS_ENABLED'           => env('DEV_TOOLS_ENABLED'),
            'APP_ENV'                     => env('APP_ENV'),
            'APP_DEBUG'                   => env('APP_DEBUG') ? 'true' : 'false',
            'ANALYTICS_ENABLED'           => env('ANALYTICS_ENABLED') ? 'true' : 'false',
            'QUEUE_CONNECTION'            => env('QUEUE_CONNECTION'),
            'CACHE_STORE'                 => env('CACHE_STORE'),
            'MAIL_MAILER'                 => env('MAIL_MAILER'),
            'RESEND_API_KEY_SET'          => env('RESEND_API_KEY') ? 'yes' : 'no',
            'STRIPE_SECRET_KEY_SET'       => env('STRIPE_SECRET_KEY') ? 'yes' : 'no',
            'STRIPE_WEBHOOK_SECRET_SET'   => env('STRIPE_WEBHOOK_SECRET') ? 'yes' : 'no',
        ];

        return view('dev.feature-flags', compact('flags'));
    }
}
