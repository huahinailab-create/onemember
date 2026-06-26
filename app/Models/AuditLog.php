<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'merchant_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(
        string $event,
        Model $auditable,
        array $oldValues = [],
        array $newValues = [],
        ?int $merchantId = null,
    ): self {
        return self::create([
            'user_id'     => auth()->id(),
            'merchant_id' => $merchantId,
            'event'       => $event,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id'   => $auditable->getKey(),
            'old_values'  => $oldValues ?: null,
            'new_values'  => $newValues ?: null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);
    }
}
