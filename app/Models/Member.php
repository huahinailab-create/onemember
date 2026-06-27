<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'name',
        'nickname',
        'notes',
        'email',
        'phone',
        'member_code',
        'birthday',
        'status',
        'total_points',
        'lifetime_points',
        'joined_at',
        'last_activity_at',
    ];

    protected $casts = [
        'status'           => MemberStatus::class,
        'birthday'         => 'date',
        'joined_at'        => 'datetime',
        'last_activity_at' => 'datetime',
        'total_points'     => 'integer',
        'lifetime_points'  => 'integer',
    ];

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->withTrashed()
                    ->where($field ?? $this->getRouteKeyName(), $value)
                    ->firstOrFail();
    }

    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            if (empty($member->member_code)) {
                $member->member_code = strtoupper(Str::random(10));
            }
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    public function isBirthdayMonth(): bool
    {
        return $this->birthday && $this->birthday->month === now()->month;
    }

    public function isBirthdayToday(): bool
    {
        return $this->birthday
            && $this->birthday->day === now()->day
            && $this->birthday->month === now()->month;
    }

    public function isActive(): bool
    {
        return $this->status === MemberStatus::Active;
    }
}
