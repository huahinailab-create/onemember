<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerOtp;
use App\Services\CustomerIdentity\OtpService;
use App\Services\CustomerIdentity\PhoneNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * CUSTOMER-001A — account security settings. Changing email or phone
 * ALWAYS re-verifies: an OTP is sent to the NEW destination and the change
 * only applies when that code verifies (the OTP row's destination is the
 * pending value — no pending-change columns exist).
 */
class AccountController extends Controller
{
    public function __construct(
        private readonly OtpService $otp,
        private readonly PhoneNumberService $phones,
    ) {
    }

    public function settings(Request $request)
    {
        return view('customer.settings', ['customer' => $request->user('customer')]);
    }

    /**
     * CUSTOMER-001C — wallet preferences: communication channel + marketing
     * consent, stored in the extensible customers.preferences JSON.
     */
    public function updatePreferences(Request $request)
    {
        $customer = $request->user('customer');

        $data = $request->validate([
            'communication_channel' => ['required', 'in:email,sms,none'],
            'marketing_opt_in'      => ['nullable', 'boolean'],
        ]);

        $customer->update(['preferences' => array_merge($customer->preferences ?? [], [
            'communication_channel' => $data['communication_channel'],
            'marketing_opt_in'      => $request->boolean('marketing_opt_in'),
        ])]);

        return redirect()->route('customer.settings')
            ->with('status', __('customer_wallet.preferences_saved'));
    }

    public function updatePassword(Request $request)
    {
        $customer = $request->user('customer');

        $rules = ['password' => ['required', 'confirmed', Password::defaults()]];
        if ($customer->hasPassword()) {
            $rules['current_password'] = ['required', 'string'];
        }
        $request->validate($rules);

        if ($customer->hasPassword()
            && ! Hash::check($request->string('current_password'), $customer->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('customer.current_password_wrong'),
            ]);
        }

        $customer->forceFill(['password' => $request->string('password')->value()])->save();

        return redirect()->route('customer.settings')
            ->with('status', __('customer.password_changed'));
    }

    public function requestEmailChange(Request $request)
    {
        $request->merge(['new_email' => mb_strtolower(trim((string) $request->input('new_email')))]);

        $request->validate([
            'new_email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')],
        ]);

        return $this->startChange(
            $request,
            CustomerOtp::PURPOSE_CHANGE_EMAIL,
            $request->string('new_email'),
        );
    }

    public function requestPhoneChange(Request $request)
    {
        $customer = $request->user('customer');
        $normalized = $this->phones->normalize($request->input('new_phone'), $customer->country ?? null);

        if ($normalized === null) {
            throw ValidationException::withMessages(['new_phone' => __('customer.phone_invalid')]);
        }

        $request->merge(['new_phone' => $normalized]);
        $request->validate([
            'new_phone' => ['required', Rule::unique('customers', 'phone')],
        ]);

        return $this->startChange($request, CustomerOtp::PURPOSE_CHANGE_PHONE, $normalized);
    }

    public function showConfirmChange(Request $request)
    {
        $pending = $request->session()->get('customer_change');
        if ($pending === null) {
            return redirect()->route('customer.settings');
        }

        return view('customer.settings-confirm', [
            'destination' => $pending['destination'],
            'purpose'     => $pending['purpose'],
        ]);
    }

    public function confirmChange(Request $request)
    {
        $pending = $request->session()->get('customer_change');
        if ($pending === null) {
            return redirect()->route('customer.settings');
        }

        $request->validate(['code' => ['required', 'digits:' . config('customer_identity.otp.length')]]);

        $otp = $this->otp->verify($pending['destination'], $pending['purpose'], $request->string('code'));
        $customer = $request->user('customer');

        if ($otp === null || $otp->customer_id !== $customer->id) {
            throw ValidationException::withMessages(['code' => __('customer.otp_invalid')]);
        }

        // Verifying the code applies the change: the destination is the new
        // value and is verified by construction.
        if ($pending['purpose'] === CustomerOtp::PURPOSE_CHANGE_EMAIL) {
            $customer->forceFill([
                'email'             => $pending['destination'],
                'email_verified_at' => now(),
            ])->save();
        } else {
            $customer->forceFill([
                'phone'             => $pending['destination'],
                'phone_verified_at' => now(),
            ])->save();
        }

        $request->session()->forget('customer_change');

        return redirect()->route('customer.settings')
            ->with('status', __('customer.contact_changed'));
    }

    private function startChange(Request $request, string $purpose, string $destination)
    {
        $this->otp->send($destination, $purpose, $request->user('customer'));

        $request->session()->put('customer_change', [
            'destination' => $destination,
            'purpose'     => $purpose,
        ]);

        return redirect()->route('customer.change.confirm')
            ->with('status', __('customer.otp_sent_generic'));
    }
}
