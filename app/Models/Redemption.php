<?php

namespace App\Models;

use App\Enums\RedemptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Redemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'member_id',
        'reward_id',
        'transaction_id',
        'used_by',
        'code',
        'status',
        'points_used',
        'redeemed_at',
        'expires_at',
    ];

    protected $casts = [
        'status'      => RedemptionStatus::class,
        'points_used' => 'integer',
        'redeemed_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Redemption $redemption) {
            if (empty($redemption->code)) {
                $redemption->code = strtoupper(Str::random(8));
            }
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function isPending(): bool
    {
        return $this->status === RedemptionStatus::Pending;
    }

    public function isExpired(): bool
    {
        if ($this->status === RedemptionStatus::Expired) {
            return true;
        }

        return $this->expires_at && $this->expires_at->isPast();
    }
}
