<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantProfileRequest;
use App\Models\Merchant;
use App\Services\AnalyticsService;
use App\Services\MerchantBrandingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MerchantProfileController extends Controller
{
    public function __construct(private readonly AnalyticsService $analytics) {}

    public function edit(Request $request): View
    {
        $merchant = $request->user()->merchant;

        return view('merchant.profile.edit', compact('merchant'));
    }

    public function update(MerchantProfileRequest $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        $data     = $request->validated();

        // Logo removal
        if ($request->boolean('remove_logo') && $merchant) {
            (new MerchantBrandingService($merchant))->deleteLogo();
            $data['logo_path'] = null;
        }

        // Logo upload
        if ($request->hasFile('logo')) {
            $branding        = new MerchantBrandingService($merchant);
            $data['logo_path'] = $branding->storeLogo($request->file('logo'));

            $this->analytics->track('merchant_logo_uploaded', [], $request->user()->id, $merchant?->id);
        }

        // Remove upload-only keys that are not DB columns
        unset($data['logo'], $data['remove_logo']);

        if ($merchant) {
            $hadBranding = $merchant->brand_color || $merchant->secondary_color;
            $merchant->update($data);

            $brandingFields = ['brand_color', 'secondary_color', 'business_tagline', 'receipt_footer',
                               'facebook_url', 'instagram_url', 'line_url', 'website'];
            $brandingChanged = array_intersect_key($data, array_flip($brandingFields));

            if (! empty($brandingChanged)) {
                $eventName = $hadBranding ? 'merchant_branding_updated' : 'merchant_branding_updated';
                $this->analytics->track($eventName, [], $request->user()->id, $merchant->id);
            }
        } else {
            $data['user_id'] = $request->user()->id;
            $data['slug']    = Str::slug($data['name']);
            Merchant::create($data);
        }

        return redirect()->route('merchant.profile.edit')
            ->with('success', 'Merchant profile saved successfully.');
    }
}
