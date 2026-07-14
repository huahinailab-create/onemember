<?php

namespace App\Http\Controllers;

use App\Enums\MemberStatus;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Reward;
use App\Services\AnalyticsService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function show(Request $request, Member $member)
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);
        $member->loadMissing('merchant');

        $activityFilter = $request->input('activity_filter', 'all');
        if (! in_array($activityFilter, ['all', 'purchases', 'rewards', 'birthday', 'adjustments', 'expired'])) {
            $activityFilter = 'all';
        }

        $typeMap = [
            'purchases'   => 'earn',
            'rewards'     => 'redeem',
            'birthday'    => 'birthday',
            'adjustments' => 'adjust',
            'expired'     => 'expire',
        ];

        $txQuery = $member->transactions()
                          ->with([
                              'loyaltyProgram' => fn ($q) => $q->withTrashed(),
                              'createdBy',
                          ])
                          ->latest('created_at');

        if ($activityFilter !== 'all' && isset($typeMap[$activityFilter])) {
            $txQuery->where('type', $typeMap[$activityFilter]);
        }

        $transactions = $txQuery->paginate(50)->withQueryString();

        // Eligible rewards for the Redeem Reward card
        $activeCampaign  = null;
        $eligibleRewards = collect();

        if (! $member->trashed() && $member->status === MemberStatus::Active) {
            $activeCampaign = LoyaltyProgram::where('merchant_id', $member->merchant_id)
                                            ->where('status', 'active')
                                            ->whereNull('deleted_at')
                                            ->oldest('id')
                                            ->first();

            if ($activeCampaign) {
                $rewardsQuery = Reward::where('loyalty_program_id', $activeCampaign->id)
                                      ->where('status', 'active')
                                      ->where(function ($q) {
                                          $q->whereNull('quantity_available')
                                            ->orWhereRaw('quantity_available > quantity_redeemed');
                                      });

                if ($activeCampaign->type->value === 'points') {
                    $rewardsQuery->where('points_required', '<=', $member->total_points);
                } else {
                    $stampsRequired = (int) ($activeCampaign->settings['stamps_required'] ?? PHP_INT_MAX);
                    if ($member->total_points < $stampsRequired) {
                        $rewardsQuery->whereRaw('0 = 1');
                    }
                }

                $eligibleRewards = $rewardsQuery->orderBy('points_required')->get();
            }
        }

        return view('members.show', compact('member', 'transactions', 'activityFilter', 'activeCampaign', 'eligibleRewards'));
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($member->trashed(), 403);

        $member->update($request->validated());

        return redirect()->route('members.show', $member)
                         ->with('success', __('messages.member_updated'));
    }

    public function archive(Request $request, Member $member, AnalyticsService $analytics)
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($member->trashed(), 409);

        $member->delete();

        $analytics->track('member_archived', [], $request->user()->id, $request->user()->merchant?->id);

        return redirect()->route('members')->with('success', __('messages.member_archived'));
    }

    public function create(SubscriptionService $subscriptionService)
    {
        $merchant = request()->user()->merchant;
        $memberUsage = $merchant
            ? $subscriptionService->usageSummary($merchant)['members']
            : null;

        return view('members.create', compact('memberUsage'));
    }

    public function store(StoreMemberRequest $request, SubscriptionService $subscriptionService, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;

        if ($merchant && ! $subscriptionService->canCreateMember($merchant)) {
            return back()->withInput()->withErrors([
                'limit' => __('messages.member_limit_reached'),
            ]);
        }

        $member = $merchant->members()->create($request->validated());

        // PH2-001A: registering a member creates (or reuses) the customer's
        // global OneMember Identity. No profile data flows to any other
        // merchant through this link (ADR-010).
        if (config('features.identity')) {
            app(\App\Services\IdentityService::class)->ensureIdentityForMember($member);
        }

        $analytics->track('member_created', [], $request->user()->id, $merchant?->id);

        return redirect()->route('members')
            ->with('success', __('messages.member_created'))
            ->with('launch_step', 'member');
    }

    public function index(Request $request, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;
        $filter   = $request->input('filter', 'active');

        if (!in_array($filter, ['active', 'archived', 'all'])) {
            $filter = 'active';
        }

        if (!$merchant) {
            $query = Member::whereNull('id');
        } elseif ($filter === 'archived') {
            $query = Member::onlyTrashed()->where('merchant_id', $merchant->id);
        } elseif ($filter === 'all') {
            $query = Member::withTrashed()->where('merchant_id', $merchant->id);
        } else {
            $query = Member::where('merchant_id', $merchant->id);
        }

        if ($search = $request->input('search_name')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($search = $request->input('search_phone')) {
            $query->where('phone', 'like', '%' . $search . '%');
        }

        $sort      = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        if (!in_array($sort, ['name', 'birthday'])) {
            $sort = 'name';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $members = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        $analytics->page('Members');

        return view('members.index', compact('members', 'sort', 'direction', 'filter'));
    }
}
