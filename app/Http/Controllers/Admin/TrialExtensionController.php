<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\TrialExtension;
use Illuminate\Http\Request;

/** TRIAL-001 — admin-controlled merchant trial extension. */
class TrialExtensionController extends Controller
{
    public function store(Request $request, Merchant $merchant)
    {
        $validated = $request->validate([
            'preset'      => ['required', 'in:30,60,custom'],
            'custom_days' => ['required_if:preset,custom', 'nullable', 'integer', 'min:1', 'max:365'],
            'reason'      => ['required', 'string', 'max:255'],
        ]);

        $days = $validated['preset'] === 'custom'
            ? (int) $validated['custom_days']
            : (int) $validated['preset'];

        // Extend from the later of now / current trial end so extensions never
        // shorten an active trial.
        $base    = $merchant->trial_ends_at && $merchant->trial_ends_at->isFuture()
            ? $merchant->trial_ends_at
            : now();
        $newEnds = $base->copy()->addDays($days);

        $previous = $merchant->trial_ends_at;

        $merchant->update([
            'trial_ends_at'       => $newEnds,
            'subscription_status' => SubscriptionStatus::Trial,
        ]);

        TrialExtension::create([
            'merchant_id'            => $merchant->id,
            'admin_user_id'          => $request->user()->id,
            'days'                   => $days,
            'previous_trial_ends_at' => $previous,
            'new_trial_ends_at'      => $newEnds,
            'reason'                 => $validated['reason'],
        ]);

        AuditLog::record('trial.extended', $merchant, [
            'trial_ends_at' => $previous?->toIso8601String(),
        ], [
            'trial_ends_at' => $newEnds->toIso8601String(),
            'days'          => $days,
            'reason'        => $validated['reason'],
        ], $merchant->id);

        return back()->with('success', __('admin.trial_extended', ['days' => $days]));
    }
}
