<?php

namespace App\Mail;

use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberPointsEarnedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Member $member,
        public readonly Transaction $transaction,
        public readonly LoyaltyProgram $campaign,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.member_points_earned_subject', ['merchant' => $this->member->merchant->name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.member-points-earned',
        );
    }
}
