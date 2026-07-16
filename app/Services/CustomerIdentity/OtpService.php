<?php

namespace App\Services\CustomerIdentity;

use App\Mail\CustomerOtpMail;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Services\CustomerIdentity\Contracts\SmsProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * CUSTOMER-001A — the OTP engine. Codes are numeric, single-use, stored
 * hashed (bcrypt via Hash), expire after a few minutes, and die after N
 * wrong guesses. Sending is throttled per destination (resend cooldown +
 * hourly cap). Delivery is delegated: SMS through the SmsProvider seam,
 * email through a mailable — the service never knows which gateway exists.
 */
class OtpService
{
    public function __construct(private readonly SmsProvider $sms)
    {
    }

    /**
     * Generate, store (hashed), and deliver a code. Returns false when the
     * destination is being rate limited — callers surface the SAME generic
     * message either way, so the send path never leaks account existence.
     */
    public function send(string $destination, string $purpose, ?Customer $customer = null): bool
    {
        $channel = str_contains($destination, '@') ? 'email' : 'sms';

        if (! $this->allowSend($destination)) {
            return false;
        }

        // A fresh code supersedes any previous usable one for the same
        // destination+purpose — exactly one valid code at a time.
        CustomerOtp::where('destination', $destination)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = $this->generateCode();

        CustomerOtp::create([
            'customer_id' => $customer?->id,
            'channel'     => $channel,
            'destination' => $destination,
            'purpose'     => $purpose,
            'code_hash'   => Hash::make($code),
            'expires_at'  => now()->addMinutes(config('customer_identity.otp.expires_minutes')),
        ]);

        $this->deliver($channel, $destination, $code);

        RateLimiter::hit($this->hourlyKey($destination), 3600);
        RateLimiter::hit($this->cooldownKey($destination), config('customer_identity.otp.resend_seconds'));

        return true;
    }

    /**
     * Verify a code for a destination+purpose. Consumes the OTP on success;
     * counts an attempt on failure. Returns the consumed CustomerOtp on
     * success (its customer_id / destination drive the caller's action).
     */
    public function verify(string $destination, string $purpose, string $code): ?CustomerOtp
    {
        $otp = CustomerOtp::query()
            ->where('destination', $destination)
            ->where('purpose', $purpose)
            ->usable()
            ->latest('id')
            ->first();

        if ($otp === null) {
            return null;
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            return null;
        }

        $otp->update(['consumed_at' => now()]);

        return $otp;
    }

    /** Seconds the caller must wait before another send is allowed (0 = now). */
    public function resendAvailableIn(string $destination): int
    {
        if (RateLimiter::attempts($this->cooldownKey($destination)) === 0) {
            return 0;
        }

        return RateLimiter::availableIn($this->cooldownKey($destination));
    }

    private function allowSend(string $destination): bool
    {
        if (RateLimiter::attempts($this->cooldownKey($destination)) > 0) {
            return false;
        }

        return ! RateLimiter::tooManyAttempts(
            $this->hourlyKey($destination),
            config('customer_identity.otp.max_per_hour'),
        );
    }

    private function deliver(string $channel, string $destination, string $code): void
    {
        if ($channel === 'email') {
            Mail::to($destination)->send(new CustomerOtpMail($code));

            return;
        }

        $this->sms->send($destination, __('customer.otp_sms_message', [
            'code'    => $code,
            'minutes' => config('customer_identity.otp.expires_minutes'),
        ]));
    }

    private function generateCode(): string
    {
        $length = (int) config('customer_identity.otp.length');

        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    private function cooldownKey(string $destination): string
    {
        return 'customer-otp-cooldown:' . sha1($destination);
    }

    private function hourlyKey(string $destination): string
    {
        return 'customer-otp-hourly:' . sha1($destination);
    }
}
