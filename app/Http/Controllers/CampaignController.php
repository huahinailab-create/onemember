<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Http\Requests\ConfigureCampaignRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\LoyaltyProgram;
use App\Models\Reward;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
        $filter   = $request->input('filter', 'active');

        if (! in_array($filter, ['draft', 'active', 'paused', 'archived', 'all'])) {
            $filter = 'active';
        }

        if (! $merchant) {
            $query = LoyaltyProgram::whereNull('id');
        } elseif ($filter === 'archived') {
            $query = LoyaltyProgram::onlyTrashed()->where('merchant_id', $merchant->id);
        } elseif ($filter === 'all') {
            $query = LoyaltyProgram::withTrashed()->where('merchant_id', $merchant->id);
        } else {
            $query = LoyaltyProgram::where('merchant_id', $merchant->id)
                                   ->where('status', $filter);
        }

        if ($search = $request->input('search_name')) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $campaigns = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('campaigns.index', compact('campaigns', 'filter'));
    }

    public function create(SubscriptionService $subscriptionService)
    {
        $merchant = request()->user()->merchant;
        $campaignUsage = $merchant
            ? $subscriptionService->usageSummary($merchant)['campaigns']
            : null;

        return view('campaigns.create', compact('campaignUsage'));
    }

    public function store(StoreCampaignRequest $request, SubscriptionService $subscriptionService)
    {
        $merchant = $request->user()->merchant;

        if ($merchant && ! $subscriptionService->canCreateCampaign($merchant)) {
            return back()->withInput()->withErrors([
                'limit' => 'You have reached your campaign limit on your current plan. Please upgrade your subscription to create more campaigns.',
            ]);
        }

        $merchant->loyaltyPrograms()->create($request->validated());

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function show(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        $campaign->loadMissing('merchant');

        $rewardFilter = $request->input('reward_filter', 'active');
        if (! in_array($rewardFilter, ['draft', 'active', 'archived', 'all'])) {
            $rewardFilter = 'active';
        }

        if ($rewardFilter === 'archived') {
            $rewardsQuery = Reward::onlyTrashed()->where('loyalty_program_id', $campaign->id);
        } elseif ($rewardFilter === 'all') {
            $rewardsQuery = Reward::withTrashed()->where('loyalty_program_id', $campaign->id);
        } else {
            $rewardsQuery = Reward::where('loyalty_program_id', $campaign->id)
                                  ->where('status', $rewardFilter);
        }

        if ($rewardSearch = $request->input('reward_search')) {
            $rewardsQuery->where('name', 'like', '%' . $rewardSearch . '%');
        }

        $rewards = $rewardsQuery->orderBy('created_at', 'desc')->get();

        return view('campaigns.show', compact('campaign', 'rewards', 'rewardFilter'));
    }

    public function configure(ConfigureCampaignRequest $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update(['settings' => $request->validated()]);

        return redirect()->route('campaigns.show', $campaign)
                         ->with('success', 'Campaign configuration saved.');
    }

    public function update(UpdateCampaignRequest $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update($request->validated());

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated successfully.');
    }

    public function pause(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update(['status' => CampaignStatus::Paused]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign paused.');
    }

    public function archive(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 409);

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign archived.');
    }
}
