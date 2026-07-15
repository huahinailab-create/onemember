<?php

namespace App\Services\CustomerIdentity\Contracts;

/**
 * CUSTOMER-001A — the SMS delivery seam. OtpService composes the message;
 * a provider only delivers it. Binding is config-driven
 * (customer_identity.sms_provider), so integrating a real Thai/Myanmar SMS
 * gateway later is one new class + one config value — no changes to any
 * caller. The only implementation today is LogSmsProvider (development):
 * per the sprint charter there is NO production SMS integration yet and no
 * fake production sending.
 */
interface SmsProvider
{
    /**
     * @param string $to  E.164 phone number, e.g. +66812345678
     */
    public function send(string $to, string $message): void;
}
