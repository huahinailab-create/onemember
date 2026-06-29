<?php

namespace App\Events;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedbackSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly ?Merchant $merchant,
        public readonly array $feedback,
    ) {}
}
