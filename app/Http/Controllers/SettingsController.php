<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMerchantPreferencesRequest;
use App\Http\Requests\UpdateMerchantProfileRequest;
use App\Models\Merchant;
use App\Services\AnalyticsService;
use App\Services\MerchantBrandingService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        $activeTab = $request->input('tab', 'profile');
        if (! in_array($activeTab, ['profile', 'preferences', 'account', 'security', 'data'])) {
            $activeTab = 'profile';
        }

        $trialEndsAt        = $merchant?->trial_ends_at ?? $user->created_at->addDays(30);
        $trialDaysRemaining = $merchant ? $merchant->trialDaysRemaining() : max(0, (int) now()->diffInDays($trialEndsAt, false));

        $analytics->page('Settings');

        return view('settings.index', compact('user', 'merchant', 'activeTab', 'trialEndsAt', 'trialDaysRemaining'));
    }

    public function updateProfile(UpdateMerchantProfileRequest $request, AnalyticsService $analytics)
    {
        $user     = $request->user();
        $merchant = $user->merchant;
        $data     = $request->validated();

        // Logo removal
        if ($request->boolean('remove_logo') && $merchant) {
            (new MerchantBrandingService($merchant))->deleteLogo();
            $data['logo_path'] = null;
        }

        // Logo upload
        if ($request->hasFile('logo') && $merchant) {
            $data['logo_path'] = (new MerchantBrandingService($merchant))->storeLogo($request->file('logo'));
            $analytics->track('merchant_logo_uploaded', [], $user->id, $merchant->id);
        }

        // Remove upload-only keys that are not DB columns
        unset($data['logo'], $data['remove_logo']);

        if ($merchant) {
            $merchant->update($data);
        } else {
            Merchant::create(array_merge($data, [
                'user_id' => $user->id,
                'status'  => \App\Enums\MerchantStatus::Active,
            ]));
        }

        $brandingFields  = ['brand_color', 'secondary_color', 'business_tagline', 'receipt_footer',
                            'facebook_url', 'instagram_url', 'line_url'];
        $brandingChanged = array_filter(array_intersect_key($data, array_flip($brandingFields)));
        if (! empty($brandingChanged)) {
            $analytics->track('merchant_branding_updated', [], $user->id, $merchant?->id ?? $user->fresh()->merchant?->id);
        }

        $analytics->track('settings_updated', ['section' => 'profile'], $user->id, $user->fresh()->merchant?->id);

        return redirect(route('settings') . '?tab=profile')
            ->with('success', 'Business profile updated successfully.');
    }

    public function updatePreferences(UpdateMerchantPreferencesRequest $request, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validated();
        $settings  = $merchant->settings ?? [];

        $settings['date_format']                 = $validated['date_format'];
        $settings['default_expiration_type']     = $validated['default_expiration_type'];
        $settings['default_expiration_duration'] = $validated['default_expiration_duration'] ?? null;
        $settings['default_birthday_enabled']    = $validated['default_birthday_enabled'];
        $settings['winback_days']                = (int) ($validated['winback_days'] ?? 0);
        $settings['locale']                      = $validated['locale'];
        $settings['email_notifications']         = [
            'product_updates'        => $validated['email_product_updates'],
            'tips'                   => $validated['email_tips'],
            'feature_announcements'  => $validated['email_feature_announcements'],
        ];

        $merchant->update([
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'settings' => $settings,
        ]);

        $analytics->track('settings_updated', ['section' => 'preferences'], $request->user()->id, $merchant->id);

        return redirect(route('settings') . '?tab=preferences')
            ->with('success', __('messages.preferences_updated'));
    }

}
