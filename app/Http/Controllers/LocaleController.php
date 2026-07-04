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
            return back();
        }

        // Persist in session for all users (guests + authenticated)
        session(['locale' => $locale]);

        // Persist in merchant settings when logged in
        $merchant = $request->user()?->merchant;
        if ($merchant) {
            $settings           = $merchant->settings ?? [];
            $settings['locale'] = $locale;
            $merchant->update(['settings' => $settings]);
        }

        return back();
    }
}
