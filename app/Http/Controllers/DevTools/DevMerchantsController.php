<?php

namespace App\Http\Controllers\DevTools;

use App\Enums\SubscriptionPlan;
use App\Models\Merchant;
use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevMerchantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevMerchantsController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevMerchantService $service,
    ) {
        parent::__construct($audit);
    }

    public function index(Request $request): View
    {
        $merchants = Merchant::withTrashed()->with('user')->orderBy('name')->get();
        $plans     = SubscriptionPlan::cases();
        return view('dev.merchants', compact('merchants', 'plans'));
    }

    public function destroy(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.force_delete', Merchant::class, $merchant->id, ['name' => $merchant->name]);
        $this->service->deleteMerchant($merchant);
        return redirect()->route('dev.merchants')->with('success', "Merchant permanently deleted.");
    }

    public function archive(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.archive', Merchant::class, $merchant->id);
        $this->service->archiveMerchant($merchant);
        return back()->with('success', "Merchant archived.");
    }

    public function restore(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.restore', Merchant::class, $merchant->id);
        $this->service->restoreMerchant($merchant);
        return back()->with('success', "Merchant restored.");
    }

    public function resetOnboarding(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.reset_onboarding', Merchant::class, $merchant->id);
        $this->service->resetOnboarding($merchant);
        return back()->with('success', "Onboarding reset.");
    }

    public function resetSubscription(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.reset_subscription', Merchant::class, $merchant->id);
        $this->service->resetSubscription($merchant);
        return back()->with('success', "Subscription reset.");
    }

    public function changePlan(Request $request, Merchant $merchant): RedirectResponse
    {
        $request->validate(['plan' => 'required|string']);
        $this->audit->log('merchant.change_plan', Merchant::class, $merchant->id, ['plan' => $request->plan]);
        $this->service->changePlan($merchant, $request->plan);
        return back()->with('success', "Plan changed to {$request->plan}.");
    }

    public function activateTrial(Request $request, Merchant $merchant): RedirectResponse
    {
        $days = (int) $request->get('days', 30);
        $this->audit->log('merchant.activate_trial', Merchant::class, $merchant->id, ['days' => $days]);
        $this->service->activateTrial($merchant, $days);
        return back()->with('success', "Trial activated for {$days} days.");
    }

    public function expireTrial(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.expire_trial', Merchant::class, $merchant->id);
        $this->service->expireTrial($merchant);
        return back()->with('success', "Trial expired.");
    }

    public function resetBilling(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.reset_billing', Merchant::class, $merchant->id);
        $this->service->resetBilling($merchant);
        return back()->with('success', "Billing reset.");
    }

    public function resetLoyaltyProgram(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.reset_loyalty', Merchant::class, $merchant->id);
        $this->service->resetLoyaltyProgram($merchant);
        return back()->with('success', "Loyalty program reset.");
    }

    public function resetCampaigns(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.reset_campaigns', Merchant::class, $merchant->id);
        $this->service->resetCampaigns($merchant);
        return back()->with('success', "All campaigns deleted.");
    }

    public function deleteData(Merchant $merchant): RedirectResponse
    {
        $this->audit->log('merchant.delete_all_data', Merchant::class, $merchant->id);
        $this->service->deleteAllData($merchant);
        return back()->with('success', "All merchant data deleted.");
    }
}
