<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMerchantLocalizationRequest;
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
        if (! in_array($activeTab, ['profile', 'preferences', 'localization', 'account', 'security', 'data'])) {
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

        // OMEGA-001E — "Store URL" is optional on this form: a blank value
        // means "leave it as-is", not "clear it". Only ever change the
        // slug when the merchant explicitly typed a new one.
        if (empty($data['slug'])) {
            unset($data['slug']);
        } elseif ($merchant && $data['slug'] !== $merchant->slug) {
            $analytics->track('merchant_store_url_changed', [
                'from' => $merchant->slug, 'to' => $data['slug'],
            ], $user->id, $merchant->id);
        }

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

    /**
     * OMEGA-001E — live Store URL availability check, called from Settings
     * > Business Profile as the merchant types. Read-only; makes no
     * changes. Sanitizes the same way StoreIdentityService::uniqueSlugFor()
     * does, so what's shown as "available" matches what would actually be
     * accepted on save.
     */
    public function checkStoreUrlAvailability(Request $request, \App\Services\StoreIdentity\StoreIdentityService $identity)
    {
        $merchant  = $request->user()->merchant;
        $sanitized = $identity->sanitize((string) $request->query('slug', ''));

        return response()->json([
            'sanitized' => $sanitized,
            'available' => $sanitized !== '' && $identity->isAvailable($sanitized, $merchant?->id),
            'reserved'  => $sanitized !== '' && $identity->isReserved($sanitized),
        ]);
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
        $settings['email_notifications']         = [
            'product_updates'        => $validated['email_product_updates'],
            'tips'                   => $validated['email_tips'],
            'feature_announcements'  => $validated['email_feature_announcements'],
        ];

        // BETA-008B: the global fields moved to the Localization tab; they
        // remain accepted here (optional) for backwards compatibility.
        if (array_key_exists('locale', $validated)) {
            $settings['locale'] = $validated['locale'];
        }

        $update = ['settings' => $settings];
        foreach (['currency', 'timezone', 'country'] as $column) {
            if (array_key_exists($column, $validated)) {
                $update[$column] = $validated[$column];
            }
        }

        $merchant->update($update);

        $analytics->track('settings_updated', ['section' => 'preferences'], $request->user()->id, $merchant->id);

        return redirect(route('settings') . '?tab=preferences')
            ->with('success', __('messages.preferences_updated'));
    }

    /** BETA-008B — Global Settings / Localization tab. */
    public function updateLocalization(UpdateMerchantLocalizationRequest $request, AnalyticsService $analytics)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validated();
        $settings  = $merchant->settings ?? [];

        $settings['locale'] = $validated['locale'];

        // Primary currency always leads the accepted list; extras keep the
        // submitted order. Display only — no conversion (future work).
        $settings['accepted_currencies'] = array_values(array_unique(array_merge(
            [$validated['currency']],
            $validated['accepted_currencies'] ?? [],
        )));

        // Ordered list; the first entry is the default customer-facing
        // language on storefront/portal/join pages.
        $settings['customer_languages'] = array_values(array_unique($validated['customer_languages']));

        $merchant->update([
            'country'  => $validated['country'],
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'settings' => $settings,
        ]);

        $analytics->track('settings_updated', ['section' => 'localization'], $request->user()->id, $merchant->id);

        return redirect(route('settings') . '?tab=localization')
            ->with('success', __('settings.localization_updated'));
    }

}
