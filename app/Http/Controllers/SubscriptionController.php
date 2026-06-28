<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request, SubscriptionService $subscriptionService)
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        $plans          = config('subscriptions.plans', []);
        $effectivePlan  = $merchant ? $subscriptionService->effectivePlanKey($merchant) : 'free';
        $usageSummary   = $merchant ? $subscriptionService->usageSummary($merchant) : null;

        return view('subscription.index', compact(
            'user',
            'merchant',
            'plans',
            'effectivePlan',
            'usageSummary',
        ));
    }
}
