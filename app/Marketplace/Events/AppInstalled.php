<?php

namespace App\Marketplace\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;

/** PLATFORM-002 — fired after an App is installed for a merchant. */
class AppInstalled
{
    use Dispatchable;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly string $appKey,
        public readonly string $version,
    ) {
    }
}
