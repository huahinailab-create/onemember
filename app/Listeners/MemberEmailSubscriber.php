<?php

namespace App\Listeners;

use App\Events\EmailFailed;
use App\Events\EmailSent;
use App\Events\MemberBirthdayBonusAwarded;
use App\Events\MemberPointsEarned;
use App\Events\MemberRewardRedeemed;
use App\Mail\MemberBirthdayEmail;
use App\Mail\MemberPointsEarnedEmail;
use App\Mail\MemberRewardRedeemedEmail;
use App\Models\Member;
use App\Services\EmailLogger;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;

class MemberEmailSubscriber
{
    public function __construct(private readonly EmailLogger $logger) {}

    public function handleMemberPointsEarned(MemberPointsEarned $event): void
    {
        $member = $event->member;
        if (! $this->memberWantsEmail($member)) {
            return;
        }
        $this->send($member, new MemberPointsEarnedEmail($member, $event->transaction, $event->campaign), 'MemberPointsEarnedEmail');
    }

    public function handleMemberRewardRedeemed(MemberRewardRedeemed $event): void
    {
        $member = $event->member;
        if (! $this->memberWantsEmail($member)) {
            return;
        }
        $this->send($member, new MemberRewardRedeemedEmail($member, $event->redemption, $event->reward), 'MemberRewardRedeemedEmail');
    }

    public function handleMemberBirthdayBonusAwarded(MemberBirthdayBonusAwarded $event): void
    {
        $member = $event->member;
        if (! $this->memberWantsEmail($member)) {
            return;
        }
        $this->send($member, new MemberBirthdayEmail($member, $event->bonusPoints), 'MemberBirthdayEmail');
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(MemberPointsEarned::class,         [static::class, 'handleMemberPointsEarned']);
        $events->listen(MemberRewardRedeemed::class,       [static::class, 'handleMemberRewardRedeemed']);
        $events->listen(MemberBirthdayBonusAwarded::class, [static::class, 'handleMemberBirthdayBonusAwarded']);
    }

    // Member must have an email address and the merchant must not have
    // switched off member notifications in settings.
    private function memberWantsEmail(Member $member): bool
    {
        return $member->email
            && $member->merchant
            && $member->merchant->wantsEmail('member_notifications');
    }

    private function send(Member $member, \Illuminate\Mail\Mailable $mailable, string $template): void
    {
        $merchantId = $member->merchant_id;
        $locale     = $member->merchant->settings['locale'] ?? config('app.locale');

        $this->logger->sending($template, $member->email, $merchantId);

        try {
            Mail::to($member->email)->locale($locale)->queue($mailable);
            $this->logger->sent($template, $member->email, config('mail.default', 'log'), $merchantId);
            EmailSent::dispatch($template, $member->email, $merchantId);
        } catch (\Throwable $e) {
            $this->logger->failed($template, $member->email, $e->getMessage(), $merchantId);
            EmailFailed::dispatch($template, $member->email, $e->getMessage(), $merchantId);
        }
    }
}
