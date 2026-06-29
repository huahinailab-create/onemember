<?php

namespace App\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrialEnding
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly int      $daysRemaining,
    ) {}
}
