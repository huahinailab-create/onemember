<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly string $invoiceId,
        public readonly string $amountDue,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.payment_failed_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-failed',
        );
    }
}
