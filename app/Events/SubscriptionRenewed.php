<?php

namespace App\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Merchant  $merchant,
        public readonly ?string   $planKey = null,
        public readonly ?\DateTimeInterface $renewsAt = null,
    ) {}
}
