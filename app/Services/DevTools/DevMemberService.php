<?php

namespace App\Services\DevTools;

use App\Models\Member;
use App\Models\Merchant;
use Illuminate\Support\Facades\DB;

class DevMemberService
{
    public function deleteMember(Member $member): void
    {
        DB::transaction(function () use ($member) {
            $member->transactions()->forceDelete();
            $member->redemptions()->forceDelete();
            $member->forceDelete();
        });
    }

    public function archiveMember(Member $member): void
    {
        $member->delete();
    }

    public function restoreMember(Member $member): void
    {
        $member->restore();
    }

    public function resetPoints(Member $member): void
    {
        $member->forceFill(['total_points' => 0, 'lifetime_points' => 0])->save();
        $member->transactions()->where('type', 'points')->forceDelete();
    }

    public function setPoints(Member $member, int $points): void
    {
        $member->forceFill(['total_points' => max(0, $points)])->save();
    }

    public function addPoints(Member $member, int $points): void
    {
        $member->increment('total_points', $points);
        $member->increment('lifetime_points', $points);
    }

    public function deductPoints(Member $member, int $points): void
    {
        $member->decrement('total_points', min($points, $member->total_points));
    }

    public function resetStamps(Member $member): void
    {
        DB::table('stamp_cards')->where('member_id', $member->id)->delete();
    }

    public function deleteTransactions(Member $member): void
    {
        $member->transactions()->forceDelete();
    }

    public function deleteRedemptions(Member $member): void
    {
        $member->redemptions()->forceDelete();
    }

    public function deleteNotifications(Member $member): void
    {
        DB::table('notifications')->where('notifiable_id', $member->id)->delete();
    }

    public function regenerateQr(Member $member): void
    {
        $member->forceFill(['public_uuid' => (string) \Illuminate\Support\Str::uuid()])->save();
    }
}
