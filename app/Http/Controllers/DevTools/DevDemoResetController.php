<?php

namespace App\Http\Controllers\DevTools;

use App\Models\Merchant;
use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevDemoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevDemoResetController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevDemoService $demo,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $merchants = Merchant::withTrashed()->with('user')->orderBy('name')->get();
        $stats     = $this->demo->getStats();
        return view('dev.demo-reset', compact('merchants', 'stats'));
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'confirm'     => 'required|in:DELETE',
        ]);

        $merchant = Merchant::findOrFail($request->merchant_id);
        $this->audit->log('demo.reset', Merchant::class, $merchant->id, ['name' => $merchant->name]);
        $this->demo->resetDemoEnvironment($merchant);

        return back()->with('success', "Demo environment for '{$merchant->name}' has been reset. All demo data deleted.");
    }
}
