<?php

namespace App\Http\Controllers\DevTools;

use App\Jobs\GenerateDemoDataJob;
use App\Models\Merchant;
use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevDemoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevQuickActionsController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevDemoService $demo,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $merchants = Merchant::orderBy('name')->get();
        $stats     = $this->demo->getStats();
        return view('dev.quick-actions', compact('merchants', 'stats'));
    }

    public function createDemoMerchant(): RedirectResponse
    {
        $this->audit->log('quick.create_demo_merchant');
        $merchant = $this->demo->createDemoMerchant();
        return back()->with('success', "Demo merchant '{$merchant->name}' created (ID: {$merchant->id}).");
    }

    public function generateMembers(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:1000',
        ]);

        $count = (int) $request->count;
        $this->audit->log('quick.generate_members', Merchant::class, $request->merchant_id, ['count' => $count]);

        GenerateDemoDataJob::dispatch($request->merchant_id, 'members', $count);
        return back()->with('success', "Queued: generating {$count} members in the background.");
    }

    public function generatePurchases(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:500',
        ]);

        $this->audit->log('quick.generate_purchases', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        GenerateDemoDataJob::dispatch($request->merchant_id, 'purchases', (int) $request->count);
        return back()->with('success', "Queued: generating {$request->count} purchases.");
    }

    public function generatePoints(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:500',
        ]);

        $this->audit->log('quick.generate_points', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        GenerateDemoDataJob::dispatch($request->merchant_id, 'points', (int) $request->count);
        return back()->with('success', "Queued: generating {$request->count} loyalty point transactions.");
    }

    public function generateStamps(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:500',
        ]);

        $this->audit->log('quick.generate_stamps', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        GenerateDemoDataJob::dispatch($request->merchant_id, 'stamps', (int) $request->count);
        return back()->with('success', "Queued: generating {$request->count} stamp transactions.");
    }

    public function generateRedemptions(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:200',
        ]);

        $this->audit->log('quick.generate_redemptions', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        GenerateDemoDataJob::dispatch($request->merchant_id, 'redemptions', (int) $request->count);
        return back()->with('success', "Queued: generating {$request->count} redemptions.");
    }

    public function generateBirthdays(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:100',
        ]);

        $this->audit->log('quick.generate_birthdays', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        GenerateDemoDataJob::dispatch($request->merchant_id, 'birthday', (int) $request->count);
        return back()->with('success', "Queued: generating {$request->count} birthday members.");
    }

    public function generateNotifications(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:200',
        ]);

        $this->audit->log('quick.generate_notifications', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        GenerateDemoDataJob::dispatch($request->merchant_id, 'notifications', (int) $request->count);
        return back()->with('success', "Queued: generating {$request->count} notifications.");
    }

    public function resetDemo(Request $request): RedirectResponse
    {
        $request->validate(['merchant_id' => 'required|exists:merchants,id']);
        $merchant = Merchant::findOrFail($request->merchant_id);
        $this->audit->log('quick.reset_demo', Merchant::class, $merchant->id, ['name' => $merchant->name]);
        $this->demo->resetDemoEnvironment($merchant);
        return back()->with('success', "Demo data for '{$merchant->name}' has been reset.");
    }
}
