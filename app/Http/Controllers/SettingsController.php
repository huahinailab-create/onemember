<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMerchantPreferencesRequest;
use App\Http\Requests\UpdateMerchantProfileRequest;
use App\Models\Merchant;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        $activeTab = $request->input('tab', 'profile');
        if (! in_array($activeTab, ['profile', 'preferences', 'account', 'security'])) {
            $activeTab = 'profile';
        }

        $trialEndsAt        = $merchant?->trial_ends_at ?? $user->created_at->addDays(30);
        $trialDaysRemaining = $merchant ? $merchant->trialDaysRemaining() : max(0, (int) now()->diffInDays($trialEndsAt, false));

        return view('settings.index', compact('user', 'merchant', 'activeTab', 'trialEndsAt', 'trialDaysRemaining'));
    }

    public function updateProfile(UpdateMerchantProfileRequest $request)
    {
        $user     = $request->user();
        $merchant = $user->merchant;
        $data     = $request->validated();

        if ($merchant) {
            $merchant->update($data);
        } else {
            Merchant::create(array_merge($data, [
                'user_id' => $user->id,
                'status'  => \App\Enums\MerchantStatus::Active,
            ]));
        }

        return redirect(route('settings') . '?tab=profile')
            ->with('success', 'Business profile updated successfully.');
    }

    public function updatePreferences(UpdateMerchantPreferencesRequest $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validated();
        $settings  = $merchant->settings ?? [];

        $settings['date_format']                 = $validated['date_format'];
        $settings['default_expiration_type']     = $validated['default_expiration_type'];
        $settings['default_expiration_duration'] = $validated['default_expiration_duration'] ?? null;
        $settings['default_birthday_enabled']    = $validated['default_birthday_enabled'];
        $settings['locale']                      = $validated['locale'];

        $merchant->update([
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'settings' => $settings,
        ]);

        return redirect(route('settings') . '?tab=preferences')
            ->with('success', __('messages.preferences_updated'));
    }

}
