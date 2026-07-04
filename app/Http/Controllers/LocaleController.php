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

        // Redirect to the explicit return URL if provided, otherwise back to current page
        $returnUrl = $request->input('return_url');
        if ($returnUrl && str_starts_with($returnUrl, url('/'))) {
            return redirect()->to($returnUrl);
        }

        return redirect()->back(fallback: url('/'));
    }
}
