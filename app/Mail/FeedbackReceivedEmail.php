<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReceivedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly array $feedback,
        public readonly bool $forSupport = false,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->forSupport
            ? __('email.feedback_support_subject', ['category' => $this->feedback['category'] ?? 'general'])
            : __('email.feedback_thankyou_subject');

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: $this->forSupport ? 'emails.feedback-support' : 'emails.feedback-received',
        );
    }
}
