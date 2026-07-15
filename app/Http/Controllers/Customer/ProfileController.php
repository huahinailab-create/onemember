<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CUSTOMER-001A — the signed-in customer's profile: identity summary
 * (OneMember ID, verification badges) and editable personal details.
 * Contact changes that require re-verification live in AccountController.
 */
class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('customer.profile', ['customer' => $request->user('customer')]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => ['required', 'string', 'max:100'],
            'last_name'    => ['nullable', 'string', 'max:100'],
            'nickname'     => ['nullable', 'string', 'max:100'],
            'display_name' => ['nullable', 'string', 'max:150'],
            'birthday'     => ['nullable', 'date', 'before:today'],
            'locale'       => ['required', Rule::in(['en', 'th'])],
        ]);

        $customer = $request->user('customer');
        $customer->fill($validated);
        // Keep the canonical card name aligned with the structured name.
        $customer->name = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));
        $customer->save();

        $request->session()->put('locale', $customer->locale);

        return redirect()->route('customer.profile')
            ->with('status', __('customer.profile_updated'));
    }
}
