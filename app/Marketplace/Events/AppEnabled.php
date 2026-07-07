<?php

namespace App\Marketplace\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;

/** PLATFORM-002 — fired when an installed App is re-enabled. */
class AppEnabled
{
    use Dispatchable;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly string $appKey,
    ) {
    }
}
