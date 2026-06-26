<?php

namespace App\Models;

use App\Enums\BirthdayRewardType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'loyalty_program_id',
        'reward_id',
        'name',
        'type',
        'value',
        'valid_days_before',
        'valid_days_after',
        'is_active',
    ];

    protected $casts = [
        'type'               => BirthdayRewardType::class,
        'value'              => 'integer',
        'valid_days_before'  => 'integer',
        'valid_days_after'   => 'integer',
        'is_active'          => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function isEligible(Member $member): bool
    {
        if (! $this->is_active || ! $member->birthday) {
            return false;
        }

        $birthday = $member->birthday->setYear(now()->year);
        $windowStart = $birthday->copy()->subDays($this->valid_days_before);
        $windowEnd   = $birthday->copy()->addDays($this->valid_days_after);

        return now()->between($windowStart, $windowEnd);
    }
}
