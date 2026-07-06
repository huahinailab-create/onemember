<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * The global OneMember Identity (PH2-001A, ADR-010).
 *
 * One verified mobile phone number = one Customer, forever. OneMember is the
 * custodian of this record — the customer controls it; merchants connect to
 * it only through consented CustomerMemberLinks. Never expose the numeric id;
 * use public_uuid (URLs) and onemember_id (human-readable, on the card).
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'birthday',
        'postal_code',
        'locale',
    ];

    protected $casts = [
        'birthday' => 'date',
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

    /** Masked phone for consent screens: 081-xxx-5678 style. */
    public function maskedPhone(): string
    {
        $digits = preg_replace('/\D/', '', (string) $this->phone);
        if (strlen($digits) < 7) {
            return str_repeat('x', strlen($digits));
        }

        return substr($digits, 0, 3) . '-xxx-' . substr($digits, -4);
    }
}
