<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Services\AnalyticsService;
use App\Services\MerchantIntelligenceService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        SubscriptionService $subscriptionService,
        AnalyticsService $analytics,
        MerchantIntelligenceService $intelligence
    ) {
        $merchant = $request->user()->merchant;

        // Redirect new merchants to the onboarding wizard
        if ((! $merchant || is_null($merchant->onboarding_completed_at)) && ! session('onboarding_skipped')) {
            return redirect()->route('onboarding.index');
        }

        $analytics->page('Dashboard');
        $analytics->track('dashboard_viewed', [], $request->user()->id, $merchant?->id);

        if (! $merchant) {
            return view('dashboard', [
                'totalActiveMembers'  => 0,
                'activeCampaignCount' => 0,
                'redeemedToday'       => 0,
                'pointsIssuedToday'   => 0,
                'recentActivity'      => collect(),
                'topMembers'          => collect(),
                'activeCampaigns'     => collect(),
                'hasAnyMembers'       => false,
                'hasAnyCampaigns'     => false,
                'hasAnyRewards'       => false,
                'firstCampaignId'     => null,
                'subscriptionUsage'   => null,
                'insights'            => [],
                'healthScore'         => ['score' => 0, 'label' => 'new_business', 'label_text' => __('intelligence.health_new_business'), 'explanation' => __('intelligence.health_new_business_explanation'), 'badge_class' => 'bg-secondary'],
                'opportunities'       => [],
                'launchChecklist'     => null,
                'winbackCount'        => 0,
                'winbackDays'         => 0,
            ]);
        }

        // usageSummary already counts members — read from it to avoid a duplicate query.
        $subscriptionUsage  = $subscriptionService->usageSummary($merchant);
        $totalActiveMembers = $subscriptionUsage['members']['used'];

        $activeCampaignCount = $merchant->loyaltyPrograms()
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->count();

        $redeemedToday = $merchant->redemptions()
            ->whereDate('redeemed_at', today())
            ->count();

        $pointsIssuedToday = (int) $merchant->transactions()
            ->where('type', TransactionType::Earn)
            ->whereDate('created_at', today())
            ->sum('points');

        $recentActivity = $merchant->transactions()
            ->with([
                'member',
                'loyaltyProgram' => fn ($q) => $q->withTrashed(),
            ])
            ->latest('created_at')
            ->limit(10)
            ->get();

        $topMembers = $merchant->members()
            ->orderByDesc('total_points')
            ->limit(5)
            ->get();

        $activeCampaigns = $merchant->loyaltyPrograms()
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->withCount(['rewards as rewards_count' => fn ($q) => $q->whereNull('deleted_at')])
            ->get();

        // Short-circuit: skip the withTrashed query when active members already exist.
        $hasAnyMembers   = $totalActiveMembers > 0 || $merchant->members()->withTrashed()->exists();
        $hasAnyCampaigns = $merchant->loyaltyPrograms()->withTrashed()->exists();
        $hasAnyRewards   = $merchant->rewards()->withTrashed()->exists();

        $firstCampaign   = $merchant->loyaltyPrograms()->withTrashed()->oldest('id')->first();
        $firstCampaignId = $firstCampaign?->id;

        $insights     = $intelligence->getInsights($merchant);
        $healthScore  = $intelligence->getHealthScore($merchant);
        $opportunities = $intelligence->getOpportunities($merchant);

        // LAUNCH-001 merchant success checklist
        $launchChecklist = app(\App\Services\LaunchChecklistService::class)->for($merchant);

        // Win-back alert (MVP-008): members inactive past the configured threshold
        $winbackDays  = (int) ($merchant->settings['winback_days'] ?? 0);
        $winbackCount = $winbackDays > 0
            ? $merchant->members()
                ->where('status', \App\Enums\MemberStatus::Active)
                ->where('last_activity_at', '<', now()->subDays($winbackDays))
                ->count()
            : 0;

        return view('dashboard', compact(
            'totalActiveMembers',
            'activeCampaignCount',
            'redeemedToday',
            'pointsIssuedToday',
            'recentActivity',
            'topMembers',
            'activeCampaigns',
            'hasAnyMembers',
            'hasAnyCampaigns',
            'hasAnyRewards',
            'firstCampaignId',
            'subscriptionUsage',
            'insights',
            'healthScore',
            'opportunities',
            'winbackCount',
            'winbackDays',
            'launchChecklist',
        ));
    }
}
