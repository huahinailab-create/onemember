<?php

namespace App\Events;

use App\Models\Merchant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrialStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
    ) {}
}
