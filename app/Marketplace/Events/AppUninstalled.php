<?php

namespace App\Marketplace\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;

/** PLATFORM-002 — fired after an App is uninstalled (data retained, DR-34). */
class AppUninstalled
{
    use Dispatchable;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly string $appKey,
    ) {
    }
}
