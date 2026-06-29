<?php

namespace App\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionPurchased
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly string   $planKey,
        public readonly string   $stripePriceId,
        public readonly ?string  $stripeSubscriptionId = null,
    ) {}
}
