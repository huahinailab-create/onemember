<?php

namespace App\Http\Controllers;

use App\Enums\MemberStatus;
use App\Enums\RedemptionStatus;
use App\Enums\TransactionType;
use App\Events\MemberRewardRedeemed;
use App\Http\Requests\RedeemRewardRequest;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Redemption;
use App\Models\Reward;
use App\Models\Transaction;
use App\Services\AnalyticsService;

class RedemptionController extends Controller
{
    public function store(RedeemRewardRequest $request, Member $member, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;

        abort_unless($member->merchant_id === $merchant?->id, 403);

        if ($member->trashed()) {
            return back()->withErrors(['redemption' => 'This member is archived and cannot redeem rewards.']);
        }

        if ($member->status !== MemberStatus::Active) {
            return back()->withErrors(['redemption' => 'This member is not active.']);
        }

        // Load the reward and verify it belongs to this merchant
        $reward = Reward::where('id', $request->input('reward_id'))
                        ->where('merchant_id', $merchant->id)
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                        ->first();

        if (! $reward) {
            return back()->withErrors(['redemption' => 'This reward is no longer available.']);
        }

        // Find the active campaign via the reward
        $campaign = LoyaltyProgram::where('id', $reward->loyalty_program_id)
                                  ->where('status', 'active')
                                  ->whereNull('deleted_at')
                                  ->first();

        if (! $campaign) {
            return back()->withErrors(['redemption' => 'The campaign for this reward is not active.']);
        }

        // Check remaining quantity
        if ($reward->quantity_available !== null && $reward->quantity_redeemed >= $reward->quantity_available) {
            return back()->withErrors(['redemption' => 'This reward has no remaining quantity.']);
        }

        // Determine points to deduct and validate eligibility
        if ($campaign->type->value === 'points') {
            $pointsRequired = (int) $reward->points_required;

            if ($member->total_points < $pointsRequired) {
                return back()->withErrors(['redemption' => 'This member does not have enough points for this reward.']);
            }
        } else {
            // Stamps: member must have completed the stamp card
            $stampsRequired = (int) ($campaign->settings['stamps_required'] ?? PHP_INT_MAX);

            if ($member->total_points < $stampsRequired) {
                return back()->withErrors(['redemption' => 'This member has not completed the stamp card yet.']);
            }

            $pointsRequired = $stampsRequired;
        }

        $balanceBefore = $member->total_points;
        $balanceAfter  = $balanceBefore - $pointsRequired;

        // Create the debit transaction first (redemption table requires transaction_id)
        $transaction = Transaction::create([
            'merchant_id'        => $merchant->id,
            'member_id'          => $member->id,
            'loyalty_program_id' => $campaign->id,
            'created_by'         => $request->user()->id,
            'type'               => TransactionType::Redeem,
            'points'             => -$pointsRequired,
            'balance_before'     => $balanceBefore,
            'balance_after'      => $balanceAfter,
            'created_at'         => now(),
        ]);

        // Create the redemption record
        $redemption = Redemption::create([
            'merchant_id'    => $merchant->id,
            'member_id'      => $member->id,
            'reward_id'      => $reward->id,
            'transaction_id' => $transaction->id,
            'used_by'        => $request->user()->id,
            'status'         => RedemptionStatus::Used,
            'points_used'    => $pointsRequired,
            'redeemed_at'    => now(),
        ]);

        // Increment quantity counter for limited rewards
        if ($reward->quantity_available !== null) {
            $reward->increment('quantity_redeemed');
        }

        // Update member balance
        $member->total_points     = $balanceAfter;
        $member->last_activity_at = now();
        $member->save();

        $analytics->track('reward_redeemed', ['campaign_type' => $campaign->type->value], $request->user()->id, $merchant->id);

        MemberRewardRedeemed::dispatch($member, $redemption, $reward);

        return redirect()->route('members.show', $member)
                         ->with('redemption_success', [
                             'reward_name'  => $reward->name,
                             'points_used'  => $pointsRequired,
                             'balance'      => $balanceAfter,
                             'type'         => $campaign->type->value,
                         ]);
    }
}
