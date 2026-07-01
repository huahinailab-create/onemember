<?php

namespace App\Http\Controllers\DevTools;

use App\Models\Member;
use App\Models\Merchant;
use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevMemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevMembersController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevMemberService $service,
    ) {
        parent::__construct($audit);
    }

    public function index(Request $request): View
    {
        $members   = collect();
        $query     = $request->get('q');
        $merchants = Merchant::withTrashed()->orderBy('name')->get();

        if ($query) {
            $members = Member::withTrashed()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%")
                      ->orWhere('member_code', 'like', "%{$query}%");
                })
                ->with('merchant')
                ->limit(20)
                ->get();
        } elseif ($request->filled('merchant_id')) {
            $members = Member::withTrashed()
                ->where('merchant_id', $request->merchant_id)
                ->with('merchant')
                ->limit(50)
                ->get();
        }

        return view('dev.members', compact('members', 'query', 'merchants'));
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->audit->log('member.force_delete', Member::class, $member->id, ['name' => $member->name]);
        $this->service->deleteMember($member);
        return back()->with('success', "Member permanently deleted.");
    }

    public function archive(Member $member): RedirectResponse
    {
        $this->audit->log('member.archive', Member::class, $member->id);
        $this->service->archiveMember($member);
        return back()->with('success', "Member archived.");
    }

    public function restore(Member $member): RedirectResponse
    {
        $this->audit->log('member.restore', Member::class, $member->id);
        $this->service->restoreMember($member);
        return back()->with('success', "Member restored.");
    }

    public function resetPoints(Member $member): RedirectResponse
    {
        $this->audit->log('member.reset_points', Member::class, $member->id);
        $this->service->resetPoints($member);
        return back()->with('success', "Points reset to 0.");
    }

    public function setPoints(Request $request, Member $member): RedirectResponse
    {
        $request->validate(['points' => 'required|integer|min:0']);
        $this->audit->log('member.set_points', Member::class, $member->id, ['points' => $request->points]);
        $this->service->setPoints($member, (int) $request->points);
        return back()->with('success', "Points set to {$request->points}.");
    }

    public function addPoints(Request $request, Member $member): RedirectResponse
    {
        $request->validate(['points' => 'required|integer|min:1']);
        $this->audit->log('member.add_points', Member::class, $member->id, ['points' => $request->points]);
        $this->service->addPoints($member, (int) $request->points);
        return back()->with('success', "Added {$request->points} points.");
    }

    public function deductPoints(Request $request, Member $member): RedirectResponse
    {
        $request->validate(['points' => 'required|integer|min:1']);
        $this->audit->log('member.deduct_points', Member::class, $member->id, ['points' => $request->points]);
        $this->service->deductPoints($member, (int) $request->points);
        return back()->with('success', "Deducted {$request->points} points.");
    }

    public function resetStamps(Member $member): RedirectResponse
    {
        $this->audit->log('member.reset_stamps', Member::class, $member->id);
        $this->service->resetStamps($member);
        return back()->with('success', "Stamps reset.");
    }

    public function deleteTransactions(Member $member): RedirectResponse
    {
        $this->audit->log('member.delete_transactions', Member::class, $member->id);
        $this->service->deleteTransactions($member);
        return back()->with('success', "Transactions deleted.");
    }

    public function deleteRedemptions(Member $member): RedirectResponse
    {
        $this->audit->log('member.delete_redemptions', Member::class, $member->id);
        $this->service->deleteRedemptions($member);
        return back()->with('success', "Redemptions deleted.");
    }

    public function deleteNotifications(Member $member): RedirectResponse
    {
        $this->audit->log('member.delete_notifications', Member::class, $member->id);
        $this->service->deleteNotifications($member);
        return back()->with('success', "Notifications deleted.");
    }

    public function regenerateQr(Member $member): RedirectResponse
    {
        $this->audit->log('member.regenerate_qr', Member::class, $member->id);
        $this->service->regenerateQr($member);
        return back()->with('success', "QR code regenerated.");
    }
}
