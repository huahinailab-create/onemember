<?php

namespace App\Models;

use App\Enums\MerchantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'contact_person',
        'business_type',
        'slug',
        'email',
        'phone',
        'website',
        'address',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'notes',
        'logo_path',
        'status',
        'currency',
        'timezone',
        'settings',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'status'                  => MerchantStatus::class,
        'settings'                => 'array',
        'onboarding_completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Merchant $merchant) {
            if (empty($merchant->slug)) {
                $merchant->slug = Str::slug($merchant->name);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function loyaltyPrograms(): HasMany
    {
        return $this->hasMany(LoyaltyProgram::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    public function birthdayRewards(): HasMany
    {
        return $this->hasMany(BirthdayReward::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isActive(): bool
    {
        return $this->status === MerchantStatus::Active;
    }
}
