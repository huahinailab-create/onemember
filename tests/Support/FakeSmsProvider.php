<?php

namespace Tests\Support;

use App\Services\CustomerIdentity\Contracts\SmsProvider;

/** Captures SMS sends for assertions (CUSTOMER-001A/B test suites). */
class FakeSmsProvider implements SmsProvider
{
    public array $sent = [];

    public function send(string $to, string $message): void
    {
        $this->sent[] = ['to' => $to, 'message' => $message];
    }
}
