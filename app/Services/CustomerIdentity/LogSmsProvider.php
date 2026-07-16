<?php

namespace App\Services\CustomerIdentity;

use App\Services\CustomerIdentity\Contracts\SmsProvider;
use Illuminate\Support\Facades\Log;

/**
 * CUSTOMER-001A — the development SMS provider: writes the message to the
 * application log instead of sending. This is the only provider that
 * exists today; production SMS is a future integration (new class + the
 * customer_identity.sms_provider config value).
 */
class LogSmsProvider implements SmsProvider
{
    public function send(string $to, string $message): void
    {
        Log::info('[customer-identity] SMS (log provider)', [
            'to'      => $to,
            'message' => $message,
        ]);
    }
}
