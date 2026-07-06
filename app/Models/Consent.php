<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only consent ledger (ADR-010). Rows are never updated or deleted —
 * current state is the latest row per (customer, merchant, data_type).
 */
class Consent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'merchant_id',
        'data_type',
        'granted',
        'consent_version',
        'source',
        'acted_at',
        'created_at',
    ];

    protected $casts = [
        'granted'    => 'boolean',
        'acted_at'   => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Consent $consent) {
            $consent->created_at ??= now();
        });

        // Append-only guard: any update attempt is a programming error.
        static::updating(function () {
            throw new \LogicException('Consent rows are append-only (ADR-010). Create a new row instead.');
        });

        static::deleting(function () {
            throw new \LogicException('Consent rows are append-only (ADR-010) and must never be deleted.');
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
