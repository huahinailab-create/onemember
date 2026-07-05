<?php

namespace App\Http\Controllers;

use App\Enums\MemberStatus;
use App\Enums\TransactionType;
use App\Events\MemberPointsEarned;
use App\Http\Requests\RecordPurchaseRequest;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Transaction;
use App\Services\AnalyticsService;

class PurchaseController extends Controller
{
    public function store(RecordPurchaseRequest $request, Member $member, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;

        abort_unless($member->merchant_id === $merchant?->id, 403);

        if ($member->trashed()) {
            return back()->withErrors(['purchase' => 'This member is archived and cannot receive purchases.']);
        }

        if ($member->status !== MemberStatus::Active) {
            return back()->withErrors(['purchase' => 'This member is not active. Only active members can receive purchases.']);
        }

        $campaign = LoyaltyProgram::where('merchant_id', $merchant->id)
                                  ->where('status', 'active')
                                  ->whereNull('deleted_at')
                                  ->oldest('id')
                                  ->first();

        if (! $campaign) {
            return back()->withErrors(['purchase' => 'No active campaign found. Please activate a campaign before recording purchases.']);
        }

        $purchaseAmount = (float) $request->input('purchase_amount');
        $settings       = $campaign->settings ?? [];

        if ($campaign->type->value === 'points') {
            $spendAmount   = max(1, (int) ($settings['spend_amount']  ?? 100));
            $pointsAwarded = max(1, (int) ($settings['points_awarded'] ?? 1));
            $earned        = (int) floor($purchaseAmount / $spendAmount) * $pointsAwarded;
        } else {
            $earned = 1;
        }

        $balanceBefore = $member->total_points;
        $balanceAfter  = $balanceBefore + $earned;

        $transaction = Transaction::create([
            'merchant_id'        => $merchant->id,
            'member_id'          => $member->id,
            'loyalty_program_id' => $campaign->id,
            'created_by'         => $request->user()->id,
            'type'               => TransactionType::Earn,
            'points'             => $earned,
            'balance_before'     => $balanceBefore,
            'balance_after'      => $balanceAfter,
            'purchase_amount'    => $purchaseAmount,
            'invoice_number'     => $request->input('invoice_number') ?: null,
            'note'               => $request->input('note') ?: null,
            'created_at'         => now(),
        ]);

        $member->total_points     = $balanceAfter;
        $member->last_activity_at = now();

        if ($campaign->type->value === 'points') {
            $member->lifetime_points += $earned;
        }

        $member->save();

        $analytics->track('purchase_recorded', ['campaign_type' => $campaign->type->value], $request->user()->id, $merchant->id);

        MemberPointsEarned::dispatch($member, $transaction, $campaign);

        // Counter Mode posts with return_to=counter to stay on the counter screen
        $route = $request->input('return_to') === 'counter'
            ? redirect()->route('counter')
            : redirect()->route('members.show', $member);

        return $route->with('purchase_success', [
                             'amount'        => $purchaseAmount,
                             'campaign_name' => $campaign->name,
                             'earned'        => $earned,
                             'type'          => $campaign->type->value,
                             'balance'       => $balanceAfter,
                             'currency'      => $merchant->currency ?? 'THB',
                             'member_name'   => $member->name,
                         ]);
    }
}
