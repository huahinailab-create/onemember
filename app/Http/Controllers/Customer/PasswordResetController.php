<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Services\CustomerIdentity\OtpService;
use App\Services\CustomerIdentity\PhoneNumberService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * CUSTOMER-001A — OTP-based password reset (no token emails; the same OTP
 * engine used everywhere else). Response to the request step is identical
 * whether or not the account exists.
 */
class PasswordResetController extends Controller
{
    public function __construct(
        private readonly OtpService $otp,
        private readonly PhoneNumberService $phones,
    ) {
    }

    public function request()
    {
        return view('customer.auth.forgot');
    }

    public function send(Request $request)
    {
        $request->validate(['identifier' => ['required', 'string', 'max:255']]);

        $identifier = trim($request->string('identifier'));
        $destination = $this->phones->looksLikePhone($identifier)
            ? $this->phones->normalize($identifier)
            : (filter_var($identifier, FILTER_VALIDATE_EMAIL) ? mb_strtolower($identifier) : null);

        if ($destination !== null) {
            $column = str_contains($destination, '@') ? 'email' : 'phone';
            $customer = Customer::where($column, $destination)->first();

            if ($customer !== null && $customer->isActive()) {
                $this->otp->send($destination, CustomerOtp::PURPOSE_PASSWORD_RESET, $customer);
            }

            $request->session()->put('customer_password_reset', ['destination' => $destination]);
        }

        return redirect()->route('customer.password.reset')
            ->with('status', __('customer.otp_sent_generic'));
    }

    public function showReset(Request $request)
    {
        if ($request->session()->missing('customer_password_reset')) {
            return redirect()->route('customer.password.request');
        }

        return view('customer.auth.reset');
    }

    public function update(Request $request)
    {
        $pending = $request->session()->get('customer_password_reset');
        if ($pending === null) {
            return redirect()->route('customer.password.request');
        }

        $request->validate([
            'code'     => ['required', 'digits:' . config('customer_identity.otp.length')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $otp = $this->otp->verify(
            $pending['destination'],
            CustomerOtp::PURPOSE_PASSWORD_RESET,
            $request->string('code'),
        );

        if ($otp === null || $otp->customer === null || ! $otp->customer->isActive()) {
            throw ValidationException::withMessages(['code' => __('customer.otp_invalid')]);
        }

        $otp->customer->forceFill([
            'password'      => $request->string('password')->value(),
            'last_login_at' => now(),
        ])->save();

        $request->session()->forget('customer_password_reset');
        auth('customer')->login($otp->customer);
        $request->session()->regenerate();

        return redirect()->route('customer.profile')
            ->with('status', __('customer.password_reset_done'));
    }
}
