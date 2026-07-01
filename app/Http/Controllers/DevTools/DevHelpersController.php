<?php

namespace App\Http\Controllers\DevTools;

use App\Models\Merchant;
use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevHelpersController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $merchants = Merchant::withTrashed()->orderBy('name')->get();
        return view('dev.helpers', compact('merchants'));
    }

    public function generateMembers(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:1000',
        ]);

        $count = (int) $request->count;
        $this->audit->log('helpers.generate_members', Merchant::class, $request->merchant_id, ['count' => $count]);

        try {
            $this->system->generateFakeMembers($request->merchant_id, $count);
            return back()->with('success', "{$count} fake members generated.");
        } catch (\Throwable $e) {
            return back()->with('error', "Failed: {$e->getMessage()}");
        }
    }

    public function generateTransactions(Request $request): RedirectResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'count'       => 'required|integer|min:1|max:500',
        ]);
        $this->audit->log('helpers.generate_transactions', Merchant::class, $request->merchant_id, ['count' => $request->count]);
        // Stub — extend with TransactionFactory when needed
        return back()->with('success', "Transaction generation stub (extend with TransactionFactory).");
    }
}
