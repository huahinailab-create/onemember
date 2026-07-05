<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\Redemption;
use App\Models\Reward;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberRewardRedeemedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Member $member,
        public readonly Redemption $redemption,
        public readonly Reward $reward,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.member_reward_redeemed_subject', ['merchant' => $this->member->merchant->name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.member-reward-redeemed',
        );
    }
}
