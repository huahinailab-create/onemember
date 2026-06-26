<?php

namespace App\Models;

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
        'value',
        'image_path',
        'quantity_available',
        'quantity_redeemed',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'type'               => RewardType::class,
        'points_required'    => 'integer',
        'value'              => 'decimal:2',
        'quantity_available' => 'integer',
        'quantity_redeemed'  => 'integer',
        'is_active'          => 'boolean',
        'valid_from'         => 'date',
        'valid_until'        => 'date',
    ];

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

    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->quantity_available !== null && $this->quantity_redeemed >= $this->quantity_available) {
            return false;
        }

        $today = today();

        if ($this->valid_from && $today->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $today->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function remainingQuantity(): ?int
    {
        if ($this->quantity_available === null) {
            return null;
        }

        return max(0, $this->quantity_available - $this->quantity_redeemed);
    }
}
