<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MerchantStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchantController extends Controller
{
    public function index(Request $request): View
    {
        $query = Merchant::with('owner')
            ->withCount(['members', 'transactions'])
            ->withCount(['members as active_members_count' => function ($q) {
                $q->where('status', 'active');
            }]);

        // Search by name, contact person, or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($plan = $request->input('plan')) {
            $query->where('subscription_plan', $plan);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($sub = $request->input('subscription_status')) {
            $query->where('subscription_status', $sub);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $merchants = $query->latest()->paginate(25)->withQueryString();

        return view('admin.merchants.index', [
            'merchants'    => $merchants,
            'plans'        => SubscriptionPlan::cases(),
            'statuses'     => MerchantStatus::cases(),
            'subStatuses'  => SubscriptionStatus::cases(),
        ]);
    }

    public function show(Merchant $merchant): View
    {
        $merchant->loadCount(['members', 'transactions', 'redemptions']);
        $merchant->load('owner');

        $activeMembers   = $merchant->members()->where('status', 'active')->count();
        $campaignCount   = $merchant->loyaltyPrograms()->count();
        $rewardCount     = $merchant->rewards()->count();

        $recentMembers = $merchant->members()
            ->latest('joined_at')
            ->take(10)
            ->get();

        $recentTransactions = $merchant->transactions()
            ->with('member')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.merchants.show', compact(
            'merchant', 'activeMembers', 'campaignCount', 'rewardCount',
            'recentMembers', 'recentTransactions',
        ));
    }
}
