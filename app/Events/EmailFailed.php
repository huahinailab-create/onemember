<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class EmailFailed
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public readonly string $template,
        public readonly string $recipient,
        public readonly string $reason,
        public readonly ?int $merchantId = null,
    ) {}
}
