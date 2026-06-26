<?php

namespace App\Models;

use App\Enums\LoyaltyProgramType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'name',
        'type',
        'description',
        'points_per_unit',
        'is_active',
        'starts_at',
        'ends_at',
        'settings',
    ];

    protected $casts = [
        'type'            => LoyaltyProgramType::class,
        'points_per_unit' => 'decimal:2',
        'is_active'       => 'boolean',
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
        'settings'        => 'array',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function birthdayRewards(): HasMany
    {
        return $this->hasMany(BirthdayReward::class);
    }

    public function calculatePoints(float $amount): int
    {
        return (int) floor($amount * $this->points_per_unit);
    }

    public function isRunning(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }
}
