<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WinbackAlertEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Merchant $merchant,
        public readonly Collection $members,
        public readonly int $days,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.winback_subject', ['count' => $this->members->count()]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.winback-alert',
        );
    }
}
