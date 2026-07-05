<?php

namespace App\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WinbackAlertReady
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly Collection $members,
        public readonly int $days,
    ) {}
}
