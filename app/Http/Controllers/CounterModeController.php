<?php

namespace App\Http\Controllers;

use App\Enums\MemberStatus;
use App\Models\Member;
use App\Services\AnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CounterModeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        if (! ($merchant->settings['counter_mode'] ?? false)) {
            return redirect()->route('dashboard')
                ->with('error', __('mobile.counter_disabled_notice'));
        }

        app(\App\Services\LaunchChecklistService::class)->markFlag($merchant, 'counter_tried');

        $query   = trim((string) $request->query('q', ''));
        $members = null;

        if ($query !== '') {
            $members = Member::where('merchant_id', $merchant->id)
                ->where('status', MemberStatus::Active)
                ->where(function ($q) use ($query) {
                    $q->where('phone', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%")
                      ->orWhere('member_code', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->take(10)
                ->get();
        }

        return view('counter.index', compact('query', 'members'));
    }

    public function toggle(Request $request, AnalyticsService $analytics): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $current  = (bool) ($merchant->settings['counter_mode'] ?? false);
        $settings = array_merge($merchant->settings ?? [], ['counter_mode' => ! $current]);

        $merchant->update(['settings' => $settings]);

        $analytics->track(
            'feature_used',
            ['feature' => 'counter_mode', 'enabled' => ! $current],
            $request->user()->id,
            $merchant->id,
        );

        return back();
    }
}
