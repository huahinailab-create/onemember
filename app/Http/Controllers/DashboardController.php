<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, SubscriptionService $subscriptionService)
    {
        $merchant = $request->user()->merchant;

        // Redirect new merchants to the onboarding wizard
        if ((! $merchant || is_null($merchant->onboarding_completed_at)) && ! session('onboarding_skipped')) {
            return redirect()->route('onboarding.index');
        }

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
        ));
    }
}
