<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CounterModeController extends Controller
{
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
