<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Models\LoyaltyProgram;
use App\Models\Reward;
use App\Services\AnalyticsService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function create(Request $request, LoyaltyProgram $campaign, SubscriptionService $subscriptionService)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);
        $campaign->loadMissing('merchant');

        $merchant    = $request->user()->merchant;
        $rewardUsage = $merchant ? [
            'used'       => $subscriptionService->rewardUsageCount($campaign),
            'limit'      => $subscriptionService->featureLimit($merchant, 'rewards_per_campaign'),
            'unlimited'  => $subscriptionService->isUnlimited($merchant, 'rewards_per_campaign'),
            'percentage' => $subscriptionService->rewardUsagePercentage($merchant, $campaign),
            'level'      => $subscriptionService->rewardWarningLevel($merchant, $campaign),
        ] : null;

        return view('rewards.create', compact('campaign', 'rewardUsage'));
    }

    public function store(StoreRewardRequest $request, LoyaltyProgram $campaign, SubscriptionService $subscriptionService, AnalyticsService $analytics)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $merchant = $request->user()->merchant;
        if ($merchant && ! $subscriptionService->canCreateReward($merchant, $campaign)) {
            return back()->withInput()->withErrors([
                'limit' => 'You have reached the reward limit for this campaign on your current plan. Please upgrade your subscription to add more rewards.',
            ]);
        }

        $data = $request->validated();
        unset($data['unlimited']);

        if ($campaign->type->value === 'stamps') {
            $data['points_required'] = null;
        }

        $data['merchant_id']        = $campaign->merchant_id;
        $data['loyalty_program_id'] = $campaign->id;

        Reward::create($data);

        $analytics->track('reward_created', [], $request->user()->id, $campaign->merchant_id);

        return redirect(route('campaigns.show', $campaign) . '?active_tab=rewards')
               ->with('success', __('messages.reward_created'))
               ->with('launch_step', 'reward');
    }

    public function show(Request $request, LoyaltyProgram $campaign, Reward $reward)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_unless($reward->loyalty_program_id === $campaign->id, 403);
        $campaign->loadMissing('merchant');

        return view('rewards.show', compact('campaign', 'reward'));
    }

    public function update(UpdateRewardRequest $request, LoyaltyProgram $campaign, Reward $reward)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_unless($reward->loyalty_program_id === $campaign->id, 403);
        abort_if($reward->trashed(), 403);

        $data = $request->validated();
        unset($data['unlimited']);

        if ($campaign->type->value === 'stamps') {
            $data['points_required'] = null;
        }

        $reward->update($data);

        return redirect(route('campaigns.rewards.show', [$campaign, $reward]))
               ->with('success', 'Reward updated successfully.');
    }

    public function archive(Request $request, LoyaltyProgram $campaign, Reward $reward, AnalyticsService $analytics)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_unless($reward->loyalty_program_id === $campaign->id, 403);
        abort_if($reward->trashed(), 409);

        $reward->delete();

        $analytics->track('reward_archived', [], $request->user()->id, $campaign->merchant_id);

        return redirect(route('campaigns.show', $campaign) . '?active_tab=rewards')
               ->with('success', 'Reward archived.');
    }
}
