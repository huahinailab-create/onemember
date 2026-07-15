<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * The global OneMember Identity (PH2-001A, ADR-010; authenticatable since
 * CUSTOMER-001A, ADR-016).
 *
 * One person = one Customer, able to join many merchants while merchant
 * data stays isolated behind consented CustomerMemberLinks. This is NOT
 * merchant authentication (App\Models\User) — customers sign in on their
 * own `customer` guard with a phone number OR email, via OTP or password.
 * Never expose the numeric id; use public_uuid (URLs) and onemember_id
 * (human-readable, on the card).
 */
class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'nickname',
        'display_name',
        'phone',
        'email',
        'password',
        'birthday',
        'postal_code',
        'locale',
        'country',
        'timezone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthday'          => 'date',
        'password'          => 'hashed',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->public_uuid)) {
                $customer->public_uuid = (string) Str::uuid();
            }
            if (empty($customer->onemember_id)) {
                $customer->onemember_id = self::generateOneMemberId();
            }
        });

        // Keep the canonical `name` (PH2-001A card name) populated when the
        // structured name is set and no explicit full name was given.
        static::saving(function (Customer $customer) {
            if (empty($customer->name) && ($customer->first_name || $customer->last_name)) {
                $customer->name = trim($customer->first_name . ' ' . $customer->last_name);
            }
        });
    }

    /**
     * Permanent, human-readable OneMember ID, e.g. OM-7K3F-92XD.
     * Crockford-style alphabet (no 0/O/1/I/L/U) to survive being read aloud
     * at a counter. Uniqueness is guaranteed by the DB constraint + retry.
     */
    public static function generateOneMemberId(): string
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTVWXYZ';

        do {
            $body = '';
            for ($i = 0; $i < 8; $i++) {
                $body .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $id = 'OM-' . substr($body, 0, 4) . '-' . substr($body, 4);
        } while (self::withTrashed()->where('onemember_id', $id)->exists());

        return $id;
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->where($field ?? 'public_uuid', $value)->firstOrFail();
    }

    public function links(): HasMany
    {
        return $this->hasMany(CustomerMemberLink::class);
    }

    public function liveLinks(): HasMany
    {
        return $this->links()->whereNull('unlinked_at');
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function otps(): HasMany
    {
        return $this->hasMany(CustomerOtp::class);
    }

    /** What the customer is called across OneMember surfaces. */
    public function displayName(): string
    {
        return $this->display_name
            ?: ($this->nickname
            ?: ($this->first_name
            ?: (string) $this->name));
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /** Masked phone for consent screens: 081-xxx-5678 style. */
    public function maskedPhone(): string
    {
        $digits = preg_replace('/\D/', '', (string) $this->phone);
        if (strlen($digits) < 7) {
            return str_repeat('x', strlen($digits));
        }

        return substr($digits, 0, 3) . '-xxx-' . substr($digits, -4);
    }

    /** Masked email for OTP screens: c•••@example.com style. */
    public function maskedEmail(): string
    {
        if (! $this->email || ! str_contains($this->email, '@')) {
            return '•••';
        }

        [$local, $domain] = explode('@', $this->email, 2);

        return mb_substr($local, 0, 1) . '•••@' . $domain;
    }
}
