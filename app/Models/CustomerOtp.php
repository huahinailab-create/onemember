<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CUSTOMER-001A — a single one-time password. The code itself is never
 * stored: only its hash. `destination` is the normalized phone (E.164) or
 * lowercased email the code went to; for change_email/change_phone it is
 * the pending NEW value, applied when the code verifies.
 */
class CustomerOtp extends Model
{
    public const PURPOSE_LOGIN          = 'login';
    public const PURPOSE_REGISTER       = 'register';
    public const PURPOSE_CHANGE_EMAIL   = 'change_email';
    public const PURPOSE_CHANGE_PHONE   = 'change_phone';
    public const PURPOSE_PASSWORD_RESET = 'password_reset';

    protected $fillable = [
        'customer_id', 'channel', 'destination', 'purpose',
        'code_hash', 'attempts', 'expires_at', 'consumed_at',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeUsable(Builder $query): Builder
    {
        return $query->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->where('attempts', '<', config('customer_identity.otp.max_attempts'));
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function attemptsExhausted(): bool
    {
        return $this->attempts >= config('customer_identity.otp.max_attempts');
    }
}
