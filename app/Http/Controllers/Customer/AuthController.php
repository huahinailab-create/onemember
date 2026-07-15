<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Services\CustomerIdentity\OtpService;
use App\Services\CustomerIdentity\PhoneNumberService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * CUSTOMER-001A — customer sign-in on the `customer` guard. The customer
 * chooses phone or email as identifier, and OTP or password as method.
 * Every failure message is generic: this controller never confirms whether
 * an account exists.
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly OtpService $otp,
        private readonly PhoneNumberService $phones,
    ) {
    }

    public function showLogin()
    {
        return view('customer.auth.login');
    }

    /** Method B — password. */
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password'   => ['required', 'string'],
        ]);

        [$column, $value] = $this->resolveIdentifier($request->string('identifier'));

        $customer = $column === null
            ? null
            : Customer::where($column, $value)->first();

        if ($customer === null
            || ! $customer->isActive()
            || ! $customer->hasPassword()
            || ! auth('customer')->attempt(
                [$column => $value, 'password' => $request->string('password')->value()],
                $request->boolean('remember'),
            )) {
            throw ValidationException::withMessages([
                'identifier' => __('customer.login_failed'),
            ]);
        }

        return $this->finishLogin($request, $customer);
    }

    /** Method A — request a one-time password. */
    public function requestLoginOtp(Request $request)
    {
        $request->validate(['identifier' => ['required', 'string', 'max:255']]);

        [$column, $value] = $this->resolveIdentifier($request->string('identifier'));

        if ($column !== null) {
            $customer = Customer::where($column, $value)->first();

            // Only send when the account exists and is active — but the
            // response below is IDENTICAL either way (no existence leak).
            if ($customer !== null && $customer->isActive()) {
                $this->otp->send($value, CustomerOtp::PURPOSE_LOGIN, $customer);
            }
        }

        if ($column !== null) {
            $request->session()->put('customer_otp', [
                'destination' => $value,
                'purpose'     => CustomerOtp::PURPOSE_LOGIN,
            ]);
        }

        return redirect()->route('customer.otp.form')
            ->with('status', __('customer.otp_sent_generic'));
    }

    public function showOtpForm(Request $request)
    {
        $pending = $request->session()->get('customer_otp');

        if ($pending === null) {
            return redirect()->route('customer.login');
        }

        return view('customer.auth.verify', [
            'destination' => $pending['destination'],
            'resendIn'    => $this->otp->resendAvailableIn($pending['destination']),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:' . config('customer_identity.otp.length')]]);

        $pending = $request->session()->get('customer_otp');
        if ($pending === null) {
            return redirect()->route('customer.login');
        }

        $otp = $this->otp->verify($pending['destination'], $pending['purpose'], $request->string('code'));

        if ($otp === null || $otp->customer === null || ! $otp->customer->isActive()) {
            throw ValidationException::withMessages(['code' => __('customer.otp_invalid')]);
        }

        $customer = $otp->customer;

        // Completing an OTP proves control of the destination — mark it
        // verified (covers both login and post-registration verification).
        $this->markDestinationVerified($customer, $pending['destination']);

        $request->session()->forget('customer_otp');
        auth('customer')->login($customer, remember: true);

        return $this->finishLogin($request, $customer);
    }

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get('customer_otp');
        if ($pending === null) {
            return redirect()->route('customer.login');
        }

        $customer = $this->customerForDestination($pending['destination']);
        if ($customer !== null && $customer->isActive()) {
            $this->otp->send($pending['destination'], $pending['purpose'], $customer);
        }

        return redirect()->route('customer.otp.form')
            ->with('status', __('customer.otp_sent_generic'));
    }

    public function logout(Request $request)
    {
        auth('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }

    private function finishLogin(Request $request, Customer $customer)
    {
        $request->session()->regenerate();
        $customer->forceFill(['last_login_at' => now()])->save();
        $request->session()->put('locale', $customer->locale);

        return redirect()->intended(route('customer.wallet', absolute: false));
    }

    /**
     * Turn free-typed input into [column, normalized value] — phone → E.164,
     * email → lowercase. [null, null] when it is neither.
     */
    private function resolveIdentifier(string $identifier): array
    {
        $identifier = trim($identifier);

        if ($this->phones->looksLikePhone($identifier)) {
            $normalized = $this->phones->normalize($identifier);

            return $normalized === null ? [null, null] : ['phone', $normalized];
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return ['email', mb_strtolower($identifier)];
        }

        return [null, null];
    }

    private function customerForDestination(string $destination): ?Customer
    {
        $column = str_contains($destination, '@') ? 'email' : 'phone';

        return Customer::where($column, $destination)->first();
    }

    private function markDestinationVerified(Customer $customer, string $destination): void
    {
        if (str_contains($destination, '@') && $customer->email === $destination) {
            $customer->forceFill(['email_verified_at' => $customer->email_verified_at ?? now()])->save();
        } elseif ($customer->phone === $destination) {
            $customer->forceFill(['phone_verified_at' => $customer->phone_verified_at ?? now()])->save();
        }
    }
}
