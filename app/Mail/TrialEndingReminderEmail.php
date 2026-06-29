<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingReminderEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly int $daysRemaining,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.trial_ending_subject', ['days' => $this->daysRemaining]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-ending-reminder',
        );
    }
}
