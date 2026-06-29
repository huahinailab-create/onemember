<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EmailLogger
{
    private function log(string $level, string $event, array $context = []): void
    {
        Log::channel('email')->{$level}($event, $context);
    }

    public function sending(string $template, string $recipient, ?int $merchantId = null): void
    {
        $this->log('info', 'email.sending', [
            'template'    => $template,
            'recipient'   => $this->maskEmail($recipient),
            'merchant_id' => $merchantId,
        ]);
    }

    public function sent(string $template, string $recipient, string $provider, ?int $merchantId = null): void
    {
        $this->log('info', 'email.sent', [
            'template'    => $template,
            'recipient'   => $this->maskEmail($recipient),
            'provider'    => $provider,
            'merchant_id' => $merchantId,
            'status'      => 'queued',
        ]);
    }

    public function failed(string $template, string $recipient, string $reason, ?int $merchantId = null): void
    {
        $this->log('error', 'email.failed', [
            'template'    => $template,
            'recipient'   => $this->maskEmail($recipient),
            'reason'      => $reason,
            'merchant_id' => $merchantId,
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if (strlen($local) <= 2) {
            return str_repeat('*', strlen($local)) . '@' . $domain;
        }
        return $local[0] . str_repeat('*', strlen($local) - 2) . $local[-1] . '@' . $domain;
    }
}
