<?php

namespace Tests\Unit;

use App\Services\CustomerIdentity\PhoneNumberService;
use Tests\TestCase;

/** CUSTOMER-001A — E.164 normalization rules for TH/MM. */
class PhoneNumberServiceTest extends TestCase
{
    private PhoneNumberService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PhoneNumberService();
    }

    public function test_thai_national_format_normalizes_to_e164(): void
    {
        $this->assertSame('+66812345678', $this->service->normalize('0812345678', 'TH'));
        $this->assertSame('+66812345678', $this->service->normalize('081-234-5678', 'TH'));
        $this->assertSame('+66812345678', $this->service->normalize('081 234 5678', 'TH'));
    }

    public function test_explicit_international_input_passes_through(): void
    {
        $this->assertSame('+66812345678', $this->service->normalize('+66812345678', 'TH'));
        // Country hint is irrelevant when the dial code is explicit
        $this->assertSame('+66812345678', $this->service->normalize('+66 81 234 5678', 'MM'));
    }

    public function test_dial_code_without_plus_is_tolerated(): void
    {
        $this->assertSame('+66812345678', $this->service->normalize('66812345678', 'TH'));
    }

    public function test_myanmar_numbers_normalize(): void
    {
        $this->assertSame('+959123456789', $this->service->normalize('09123456789', 'MM'));
        $this->assertSame('+959123456789', $this->service->normalize('+959123456789', 'TH'));
    }

    public function test_invalid_input_returns_null(): void
    {
        $this->assertNull($this->service->normalize('12345', 'TH'));          // too short
        $this->assertNull($this->service->normalize('081234567890123', 'TH')); // wrong length
        $this->assertNull($this->service->normalize('not a phone', 'TH'));
        $this->assertNull($this->service->normalize('', 'TH'));
        $this->assertNull($this->service->normalize('+4915112345678', 'TH')); // unsupported country
    }

    public function test_looks_like_phone_distinguishes_from_email(): void
    {
        $this->assertTrue($this->service->looksLikePhone('0812345678'));
        $this->assertTrue($this->service->looksLikePhone('+66 81 234 5678'));
        $this->assertFalse($this->service->looksLikePhone('user@example.com'));
        $this->assertFalse($this->service->looksLikePhone('hello'));
    }
}
