<?php

namespace App\Models;

use App\Enums\MerchantStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
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
        'subscription_plan',
        'subscription_status',
        'trial_ends_at',
    ];

    protected $casts = [
        'status'                  => MerchantStatus::class,
        'settings'                => 'array',
        'onboarding_completed_at' => 'datetime',
        'subscription_plan'       => SubscriptionPlan::class,
        'subscription_status'     => SubscriptionStatus::class,
        'trial_ends_at'           => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Merchant $merchant) {
            if (empty($merchant->slug)) {
                $merchant->slug = Str::slug($merchant->name);
            }

            $trialDays  = config('subscriptions.trial.days', 30);
            $trialPlan  = config('subscriptions.trial.plan', 'professional');

            $merchant->subscription_plan   ??= SubscriptionPlan::from($trialPlan);
            $merchant->subscription_status ??= SubscriptionStatus::Trial;
            $merchant->trial_ends_at       ??= now()->addDays($trialDays);
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

    // ── Subscription helpers ──────────────────────────────────────────────

    public function isOnTrial(): bool
    {
        return $this->subscription_status === SubscriptionStatus::Trial
            && $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture();
    }

    public function trialDaysRemaining(): int
    {
        if (! $this->isOnTrial()) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
    }

    public function currentPlan(): SubscriptionPlan
    {
        return $this->subscription_plan ?? SubscriptionPlan::Free;
    }

    public function subscriptionStatus(): SubscriptionStatus
    {
        return $this->subscription_status ?? SubscriptionStatus::Trial;
    }

    public function canUseFeature(string $feature): bool
    {
        // During an active trial the merchant has Professional-tier access.
        $planKey = $this->isOnTrial()
            ? config('subscriptions.trial.plan', 'professional')
            : ($this->subscription_plan?->value ?? 'free');

        return (bool) config("subscriptions.plans.{$planKey}.features.{$feature}", false);
    }

    public function isEnterprise(): bool
    {
        return $this->subscription_plan === SubscriptionPlan::Enterprise;
    }
}
