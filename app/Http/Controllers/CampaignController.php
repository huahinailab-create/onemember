<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Enums\TransactionType;
use App\Http\Requests\ConfigureCampaignRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\LoyaltyProgram;
use App\Models\Reward;
use App\Models\Transaction;
use App\Services\AnalyticsService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
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

        $analytics->page('Campaigns');

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

    public function store(StoreCampaignRequest $request, SubscriptionService $subscriptionService, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;

        if ($merchant && ! $subscriptionService->canCreateCampaign($merchant)) {
            return back()->withInput()->withErrors([
                'limit' => __('messages.campaign_limit_reached'),
            ]);
        }

        $merchant->loyaltyPrograms()->create($request->validated());

        $analytics->track('campaign_created', [], $request->user()->id, $merchant?->id);

        return redirect()->route('campaigns.index')
            ->with('success', __('messages.campaign_created'))
            ->with('launch_step', 'campaign');
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

    public function analytics(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        $campaign->loadMissing('merchant');

        $base = Transaction::where('loyalty_program_id', $campaign->id);

        // ── Campaign breakdown ───────────────────────────────────────────
        $pointsIssued   = (int) (clone $base)->whereIn('type', [TransactionType::Earn, TransactionType::Birthday])->sum('points');
        $pointsRedeemed = (int) abs((clone $base)->where('type', TransactionType::Redeem)->sum('points'));
        $pointsExpired  = (int) abs((clone $base)->where('type', TransactionType::Expire)->sum('points'));
        $purchaseCount  = (int) (clone $base)->where('type', TransactionType::Earn)->count();
        $purchaseTotal  = (float) (clone $base)->where('type', TransactionType::Earn)->sum('purchase_amount');

        // ── Member engagement ────────────────────────────────────────────
        $participatingMembers = (clone $base)->distinct('member_id')->count('member_id');
        $activeLast30         = (clone $base)->where('created_at', '>=', now()->subDays(30))
                                             ->distinct('member_id')->count('member_id');
        $topMembers = (clone $base)->whereIn('type', [TransactionType::Earn, TransactionType::Birthday])
            ->selectRaw('member_id, SUM(points) as points_earned, COUNT(*) as visit_count')
            ->groupBy('member_id')
            ->orderByDesc('points_earned')
            ->take(5)
            ->with('member')
            ->get();

        // ── 30-day activity trend (daily earn counts) ────────────────────
        $trendRaw = (clone $base)->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw("DATE(created_at) as day, COUNT(*) as tx_count")
            ->groupBy('day')
            ->pluck('tx_count', 'day');
        $trend = collect(range(29, 0))->map(function ($daysAgo) use ($trendRaw) {
            $day = now()->subDays($daysAgo)->format('Y-m-d');
            return ['day' => $day, 'count' => (int) ($trendRaw[$day] ?? 0)];
        });

        // ── Reward performance ───────────────────────────────────────────
        $rewardPerformance = Reward::withTrashed()
            ->where('loyalty_program_id', $campaign->id)
            ->withCount('redemptions')
            ->orderByDesc('redemptions_count')
            ->get();

        return view('campaigns.analytics', compact(
            'campaign', 'pointsIssued', 'pointsRedeemed', 'pointsExpired',
            'purchaseCount', 'purchaseTotal', 'participatingMembers',
            'activeLast30', 'topMembers', 'trend', 'rewardPerformance',
        ));
    }

    public function configure(ConfigureCampaignRequest $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update(['settings' => $request->validated()]);

        return redirect()->route('campaigns.show', $campaign)
                         ->with('success', __('messages.campaign_configured'));
    }

    public function update(UpdateCampaignRequest $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update($request->validated());

        return redirect()->route('campaigns.show', $campaign)->with('success', __('messages.campaign_updated'));
    }

    public function pause(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update(['status' => CampaignStatus::Paused]);

        return redirect()->route('campaigns.show', $campaign)->with('success', __('messages.campaign_paused'));
    }

    public function archive(Request $request, LoyaltyProgram $campaign, AnalyticsService $analytics)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 409);

        $campaign->delete();

        $analytics->track('campaign_archived', [], $request->user()->id, $request->user()->merchant?->id);

        return redirect()->route('campaigns.index')->with('success', __('messages.campaign_archived'));
    }
}
