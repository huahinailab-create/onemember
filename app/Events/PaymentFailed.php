<?php

namespace App\Events;

use App\Models\Merchant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly ?string  $invoiceId = null,
        public readonly ?string  $amountDue = null,
    ) {}
}
