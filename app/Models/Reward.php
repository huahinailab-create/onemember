<?php

namespace App\Models;

use App\Enums\RewardStatus;
use App\Enums\RewardType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'loyalty_program_id',
        'name',
        'description',
        'type',
        'points_required',
        'quantity_available',
        'status',
        'internal_notes',
    ];

    protected $casts = [
        'type'               => RewardType::class,
        'status'             => RewardStatus::class,
        'points_required'    => 'integer',
        'quantity_available' => 'integer',
        'quantity_redeemed'  => 'integer',
    ];

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->withTrashed()
                    ->where($field ?? $this->getRouteKeyName(), $value)
                    ->firstOrFail();
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    public function remainingQuantity(): ?int
    {
        if ($this->quantity_available === null) {
            return null;
        }

        return max(0, $this->quantity_available - $this->quantity_redeemed);
    }
}
