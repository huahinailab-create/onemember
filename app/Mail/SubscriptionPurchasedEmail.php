<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionPurchasedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly string $planKey,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.subscription_purchased_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription-purchased',
        );
    }
}
