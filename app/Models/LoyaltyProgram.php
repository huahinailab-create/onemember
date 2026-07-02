<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'status',
        'settings',
    ];

    protected $casts = [
        'type'   => LoyaltyProgramType::class,
        'status' => CampaignStatus::class,
    ];

    protected function settings(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? [] : (is_array($value) ? $value : (json_decode($value, true) ?? [])),
            set: fn ($value) => $value ? json_encode($value) : null,
        );
    }

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

}
