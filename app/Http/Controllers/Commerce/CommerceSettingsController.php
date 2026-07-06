<?php

namespace App\Http\Controllers\Commerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * APP-001 — Commerce App fulfillment settings (merchant settings JSON,
 * `commerce` key). Merchant controls fulfillment entirely (ADR-011):
 * pickup, own delivery (with radius/fee — restaurants set their own rules),
 * shipping. No payment gateway anywhere.
 */
class CommerceSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $merchant = $request->user()->merchant;

        return view('commerce.settings', [
            'merchant' => $merchant,
            'commerce' => $merchant->settings['commerce'] ?? [],
        ]);
    }

    public function update(Request $request)
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'pickup_enabled'     => ['nullable', 'boolean'],
            'delivery_enabled'   => ['nullable', 'boolean'],
            'delivery_radius_km' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'delivery_fee'       => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'shipping_enabled'   => ['nullable', 'boolean'],
            'shipping_fee'       => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'payment_instructions' => ['nullable', 'string', 'max:500'],
        ]);

        $settings = array_merge($merchant->settings ?? [], [
            'commerce' => [
                'pickup_enabled'     => (bool) ($validated['pickup_enabled'] ?? false),
                'delivery_enabled'   => (bool) ($validated['delivery_enabled'] ?? false),
                'delivery_radius_km' => $validated['delivery_radius_km'] ?? null,
                'delivery_fee'       => $validated['delivery_fee'] ?? null,
                'shipping_enabled'   => (bool) ($validated['shipping_enabled'] ?? false),
                'shipping_fee'       => $validated['shipping_fee'] ?? null,
                'payment_instructions' => $validated['payment_instructions'] ?? null,
                // payment_qr_path is managed by APP-003 (upload)
                'payment_qr_path'    => $merchant->settings['commerce']['payment_qr_path'] ?? null,
            ],
        ]);

        $merchant->update(['settings' => $settings]);

        return redirect()->route('commerce.settings')
            ->with('success', __('commerce.settings_saved'));
    }
}
