<?php

namespace App\Models;

use App\Enums\MerchantStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'brand_color',
        'secondary_color',
        'business_tagline',
        'receipt_footer',
        'facebook_url',
        'instagram_url',
        'line_url',
        'status',
        'currency',
        'timezone',
        'settings',
        'onboarding_completed_at',
        'subscription_plan',
        'subscription_status',
        'trial_ends_at',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'billing_email',
        'subscription_renews_at',
        'cancel_at_period_end',
    ];

    protected $casts = [
        'status'                  => MerchantStatus::class,
        'onboarding_completed_at' => 'datetime',
        'subscription_plan'       => SubscriptionPlan::class,
        'subscription_status'     => SubscriptionStatus::class,
        'trial_ends_at'           => 'datetime',
        'subscription_renews_at'  => 'datetime',
        'cancel_at_period_end'    => 'boolean',
    ];

    protected function settings(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? [] : (is_array($value) ? $value : (json_decode($value, true) ?? [])),
            set: fn ($value) => $value ? json_encode($value) : null,
        );
    }

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

    /**
     * True when the trial period has ended.
     * Covers both the database-updated state (status = Expired) and the
     * window between trial expiry and the next command run (status still Trial,
     * but trial_ends_at is in the past).
     */
    /** CORE-002: keys of OneMember Apps this merchant has installed. */
    public function installedApps(): array
    {
        $apps = $this->settings['installed_apps'] ?? [];

        return is_array($apps) ? array_values($apps) : [];
    }

    /** CORE-002: gate for App features. */
    public function hasApp(string $key): bool
    {
        return in_array($key, $this->installedApps(), true);
    }

    /**
     * BETA-008B: every currency this merchant accepts — primary first, then
     * the additional accepted currencies from settings. Display only; no
     * conversion (ADR-011 — money never touches OneMember).
     */
    public function acceptedCurrencies(): array
    {
        $primary = $this->currency ?: config('app.default_currency', 'THB');
        $extra   = $this->settings['accepted_currencies'] ?? [];

        return array_values(array_unique(array_merge([$primary], is_array($extra) ? $extra : [])));
    }

    /**
     * BETA-008B: languages offered on customer-facing surfaces (storefront,
     * portal, join, order pages), first entry = default. Falls back to the
     * merchant's internal language so existing merchants behave unchanged.
     */
    public function customerLanguages(): array
    {
        $configured = $this->settings['customer_languages'] ?? [];
        $allowed    = array_keys(config('localization.customer_languages', []));
        $configured = array_values(array_intersect(is_array($configured) ? $configured : [], $allowed));

        if ($configured !== []) {
            return $configured;
        }

        // Unconfigured: offer the shipped app languages, merchant's internal
        // language first — existing merchants render exactly as before while
        // visitors can still switch within the shipped set.
        $internal = $this->settings['locale'] ?? 'th';
        $internal = in_array($internal, $allowed, true) ? $internal : 'th';

        return array_values(array_unique(array_merge(
            [$internal],
            array_keys(config('localization.internal_languages', ['en' => '', 'th' => ''])),
        )));
    }

    /**
     * BETA-008B: resolve the locale for a customer-facing page. An explicit
     * ?lang= request wins when the merchant offers that language; otherwise
     * the merchant's default customer language. Never browser-derived
     * (GLOBAL-001 §8).
     */
    public function resolveCustomerLocale(?string $requested = null): string
    {
        $offered = $this->customerLanguages();

        if ($requested !== null && in_array($requested, $offered, true)) {
            return $requested;
        }

        // Visitor's explicit site-wide language switch, when the merchant
        // offers that language.
        $session = session('locale');
        if (is_string($session) && in_array($session, $offered, true)) {
            return $session;
        }

        return $offered[0];
    }

    public function trialExtensions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrialExtension::class)->latest('created_at');
    }

    public function wantsEmail(string $category): bool
    {
        if (in_array($category, ['billing', 'security_alerts'])) {
            return true;
        }
        $prefs = $this->settings['email_notifications'] ?? [];
        return (bool) ($prefs[$category] ?? true);
    }

    public function isTrialExpired(): bool
    {
        if ($this->subscription_status === SubscriptionStatus::Expired) {
            return true;
        }

        return $this->subscription_status === SubscriptionStatus::Trial
            && $this->trial_ends_at !== null
            && $this->trial_ends_at->isPast();
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->withTrashed()
                    ->where($field ?? $this->getRouteKeyName(), $value)
                    ->firstOrFail();
    }
}
