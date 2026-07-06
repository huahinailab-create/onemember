<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * APP-003 — a customer's order FROM a merchant (merchant of record,
 * Domain Model §1.10). Status transitions are merchant actions and audited.
 */
class Order extends Model
{
    public const STATUSES = ['placed', 'accepted', 'ready', 'completed', 'cancelled'];

    // Allowed transitions (merchant-driven)
    public const TRANSITIONS = [
        'placed'   => ['accepted', 'cancelled'],
        'accepted' => ['ready', 'completed', 'cancelled'],
        'ready'    => ['completed', 'cancelled'],
    ];

    protected $fillable = [
        'merchant_id',
        'member_id',
        'customer_name',
        'customer_phone',
        'fulfillment_type',
        'address',
        'notes',
        'status',
        'payment_status',
        'subtotal',
        'fulfillment_fee',
        'total',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'fulfillment_fee' => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->public_uuid ??= (string) Str::uuid();
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }
}
