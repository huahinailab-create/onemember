<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const SUPPORTED = ['en', 'th'];

    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            return redirect()->back(fallback: url('/'));
        }

        session(['locale' => $locale]);

        $merchant = $request->user()?->merchant;
        if ($merchant) {
            $settings           = $merchant->settings ?? [];
            $settings['locale'] = $locale;
            $merchant->update(['settings' => $settings]);
        }

        // Redirect to the explicit return URL if it belongs to a known domain
        $returnUrl  = $request->input('return_url');
        $allowedPrefixes = [
            'https://' . config('domains.app'),
            'http://'  . config('domains.app'),
            'https://' . config('domains.corporate'),
            'http://'  . config('domains.corporate'),
        ];
        $allowed = $returnUrl && collect($allowedPrefixes)->contains(
            fn ($prefix) => str_starts_with($returnUrl, $prefix)
        );
        if ($allowed) {
            return redirect()->to($returnUrl);
        }

        return redirect()->back(fallback: url('/'));
    }
}
