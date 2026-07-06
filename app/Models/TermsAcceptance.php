<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Append-only record of terms acceptance (CORE-001). */
class TermsAcceptance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'merchant_id',
        'document',
        'version',
        'ip_address',
        'accepted_at',
        'created_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $t) => $t->created_at ??= now());
        static::updating(fn () => throw new \LogicException('Terms acceptances are append-only.'));
        static::deleting(fn () => throw new \LogicException('Terms acceptances are append-only.'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
