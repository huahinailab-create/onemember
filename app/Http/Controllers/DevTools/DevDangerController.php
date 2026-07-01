<?php

namespace App\Http\Controllers\DevTools;

use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use App\Services\DevTools\DevAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DevDangerController extends DevController
{
    public function __construct(DevAuditLogger $audit)
    {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $stats = [
            'users'    => User::count(),
            'members'  => Member::count(),
            'merchants'=> Merchant::count(),
        ];
        return view('dev.danger', compact('stats'));
    }

    public function truncateMembers(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => 'required|in:DELETE']);
        $this->audit->log('danger.truncate_members');
        DB::transaction(function () {
            DB::table('transactions')->delete();
            DB::table('redemptions')->delete();
            Member::withTrashed()->forceDelete();
        });
        return back()->with('success', "All members and their data permanently deleted.");
    }

    public function truncateUsers(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => 'required|in:DELETE']);
        $this->audit->log('danger.truncate_users');
        DB::transaction(function () {
            // Keep current user
            User::where('id', '!=', auth()->id())->forceDelete();
        });
        return back()->with('success', "All users (except you) permanently deleted.");
    }

    public function nukeDatabaseExceptCurrentUser(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => 'required|in:DELETE']);
        $this->audit->log('danger.nuke_database');
        DB::transaction(function () {
            DB::table('transactions')->delete();
            DB::table('redemptions')->delete();
            Member::withTrashed()->forceDelete();
            Merchant::withTrashed()->where('user_id', '!=', auth()->id())->forceDelete();
            User::where('id', '!=', auth()->id())->forceDelete();
        });
        return back()->with('success', "Database cleared (your user preserved).");
    }
}
