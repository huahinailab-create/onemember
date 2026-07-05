<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MerchantStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Redemption;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Platform totals ──────────────────────────────────────────────────
        $totalMerchants      = Merchant::count();
        $activeMerchants     = Merchant::where('status', MerchantStatus::Active)->count();
        $inactiveMerchants   = Merchant::whereIn('status', [MerchantStatus::Inactive, MerchantStatus::Suspended])->count();
        $trialMerchants      = Merchant::where('subscription_status', SubscriptionStatus::Trial)->count();
        $paidMerchants       = Merchant::where('subscription_status', SubscriptionStatus::Active)->count();
        $freeMerchants       = Merchant::where('subscription_plan', SubscriptionPlan::Free)->count();

        $totalMembers        = Member::count();
        $membersToday        = Member::whereDate('created_at', today())->count();

        $totalTransactions   = Transaction::count();
        $transactionsToday   = Transaction::whereDate('created_at', today())->count();

        $rewardsRedeemed     = Redemption::count();
        $redemptionsToday    = Redemption::whereDate('created_at', today())->count();

        // ── Recent registrations ────────────────────────────────────────────
        $recentMerchants = Merchant::with('owner')
            ->latest()
            ->take(10)
            ->get();

        // ── Analytics: new merchants ─────────────────────────────────────────
        $newMerchantsToday     = Merchant::whereDate('created_at', today())->count();
        $newMerchantsThisWeek  = Merchant::where('created_at', '>=', now()->startOfWeek())->count();
        $newMerchantsThisMonth = Merchant::where('created_at', '>=', now()->startOfMonth())->count();

        // ── Analytics: new members ───────────────────────────────────────────
        $newMembersToday     = Member::whereDate('created_at', today())->count();
        $newMembersThisWeek  = Member::where('created_at', '>=', now()->startOfWeek())->count();
        $newMembersThisMonth = Member::where('created_at', '>=', now()->startOfMonth())->count();

        // ── Analytics: top performers ────────────────────────────────────────
        $topByMembers = Merchant::withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(5)
            ->get();

        $topByTransactions = Merchant::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();

        // ── Analytics: attention needed ──────────────────────────────────────
        $zeroMembers       = Merchant::doesntHave('members')->count();
        $notOnboarded      = Merchant::whereNull('onboarding_completed_at')->count();
        $trialEndingSoon   = Merchant::where('subscription_status', SubscriptionStatus::Trial)
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->count();

        return view('admin.dashboard', compact(
            'totalMerchants', 'activeMerchants', 'inactiveMerchants',
            'trialMerchants', 'paidMerchants', 'freeMerchants',
            'totalMembers', 'membersToday',
            'totalTransactions', 'transactionsToday',
            'rewardsRedeemed', 'redemptionsToday',
            'recentMerchants',
            'newMerchantsToday', 'newMerchantsThisWeek', 'newMerchantsThisMonth',
            'newMembersToday', 'newMembersThisWeek', 'newMembersThisMonth',
            'topByMembers', 'topByTransactions',
            'zeroMembers', 'notOnboarded', 'trialEndingSoon',
        ));
    }
}
