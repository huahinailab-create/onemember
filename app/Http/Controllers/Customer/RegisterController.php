<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Services\CustomerIdentity\OtpService;
use App\Services\CustomerIdentity\PhoneNumberService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * CUSTOMER-001A — customer registration. The customer supplies phone,
 * email, or both; a password is OPTIONAL (accounts may be OTP-only). The
 * account is created immediately, then the chosen primary identifier is
 * verified by OTP before the first sign-in completes.
 *
 * Registration necessarily validates identifier uniqueness (an industry-
 * standard, rate-limited existence signal — documented in ADR-016); every
 * LOGIN path stays fully generic.
 */
class RegisterController extends Controller
{
    public function __construct(
        private readonly OtpService $otp,
        private readonly PhoneNumberService $phones,
    ) {
    }

    public function show()
    {
        return view('customer.auth.register', [
            'countries' => array_keys(config('customer_identity.phone_countries')),
        ]);
    }

    public function store(Request $request)
    {
        $country = $request->input('country', config('customer_identity.default_phone_country'));

        // Normalize BEFORE validating so uniqueness checks run against the
        // stored (E.164 / lowercased) form, not the free-typed one.
        $request->merge([
            'phone' => $request->filled('phone') ? $this->phones->normalize($request->input('phone'), $country) : null,
            'email' => $request->filled('email') ? mb_strtolower(trim($request->input('email'))) : null,
        ]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            'country'    => ['required', Rule::in(array_keys(config('customer_identity.phone_countries')))],
            'phone'      => [
                'required_without:email', 'nullable', 'string',
                // normalize() returned null → the input wasn't a valid number
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('phone') === false && $value === null) {
                        return;
                    }
                    if ($value === null) {
                        $fail(__('customer.phone_invalid'));
                    }
                },
                Rule::unique('customers', 'phone'),
            ],
            'email'      => ['required_without:phone', 'nullable', 'email', 'max:255', Rule::unique('customers', 'email')],
            'password'   => ['nullable', 'confirmed', Password::defaults()],
        ], [], [
            'first_name' => __('customer.field_first_name'),
            'phone'      => __('customer.field_phone'),
            'email'      => __('customer.field_email'),
        ]);

        $customer = Customer::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'] ?? null,
            'phone'      => $validated['phone'] ?? null,
            'email'      => $validated['email'] ?? null,
            'password'   => $validated['password'] ?? null,
            'country'    => $validated['country'],
            'locale'     => app()->getLocale(),
            'status'     => Customer::STATUS_ACTIVE,
        ]);

        // Verify the primary identifier (phone wins when both were given).
        $destination = $customer->phone ?? $customer->email;
        $this->otp->send($destination, CustomerOtp::PURPOSE_LOGIN, $customer);

        $request->session()->put('customer_otp', [
            'destination' => $destination,
            'purpose'     => CustomerOtp::PURPOSE_LOGIN,
        ]);

        return redirect()->route('customer.otp.form')
            ->with('status', __('customer.register_verify_prompt'));
    }
}
