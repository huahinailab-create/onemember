<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * CUSTOMER-001A — email delivery of a one-time password. Sent synchronously
 * (not queued): a customer is sitting on the verify screen waiting for it.
 */
class CustomerOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $code)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('customer.otp_mail_subject', ['code' => $this->code]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.customer-otp', with: [
            'code'    => $this->code,
            'minutes' => config('customer_identity.otp.expires_minutes'),
        ]);
    }
}
