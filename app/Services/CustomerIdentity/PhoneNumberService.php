<?php

namespace App\Services\CustomerIdentity;

/**
 * CUSTOMER-001A — pragmatic E.164 normalization for the countries OneMember
 * operates in (config/customer_identity.php `phone_countries`). Not a full
 * libphonenumber; deliberately small: strip formatting, resolve the dial
 * code from the customer's country (or accept an explicit +country input),
 * validate national length, return +<dial><national>.
 */
class PhoneNumberService
{
    /**
     * Normalize free-typed input to E.164 (+66812345678) or return null if
     * the input can't be a valid number for the given country.
     */
    public function normalize(?string $input, string $country = null): ?string
    {
        $country ??= config('customer_identity.default_phone_country');
        $input = trim((string) $input);
        if ($input === '') {
            return null;
        }

        $hasPlus = str_starts_with($input, '+');
        $digits = preg_replace('/\D+/', '', $input);
        if ($digits === '' || strlen($digits) > 15) { // E.164 hard maximum
            return null;
        }

        $countries = config('customer_identity.phone_countries', []);

        // Explicit international input: +<dial><national> — accept if the
        // dial code matches any supported country and the length fits.
        if ($hasPlus) {
            foreach ($countries as $rules) {
                $dial = $rules['dial_code'];
                if (str_starts_with($digits, $dial)) {
                    $national = substr($digits, strlen($dial));
                    if (in_array(strlen($national), $rules['national_lengths'], true)) {
                        return '+' . $digits;
                    }
                }
            }

            return null;
        }

        // National input for the given country (e.g. 0812345678 for TH).
        $rules = $countries[$country] ?? null;
        if ($rules === null) {
            return null;
        }

        $national = $digits;
        if (($rules['trim_leading_zero'] ?? false) && str_starts_with($national, '0')) {
            $national = substr($national, 1);
        }

        // Tolerate input already including the dial code without '+'.
        if (str_starts_with($digits, $rules['dial_code'])) {
            $candidate = substr($digits, strlen($rules['dial_code']));
            if (in_array(strlen($candidate), $rules['national_lengths'], true)) {
                return '+' . $digits;
            }
        }

        if (! in_array(strlen($national), $rules['national_lengths'], true)) {
            return null;
        }

        return '+' . $rules['dial_code'] . $national;
    }

    /** True when the input looks like a phone attempt (vs an email). */
    public function looksLikePhone(string $identifier): bool
    {
        return ! str_contains($identifier, '@')
            && preg_match('/\d{6,}/', preg_replace('/\D+/', '', $identifier)) === 1;
    }
}
