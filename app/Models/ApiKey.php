<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * PLATFORM-002 Part 5 — merchant API key.
 *
 * The plaintext key ("om_live_…") exists only at generation time; the DB
 * stores its sha256. Abilities gate endpoint access ("members:read", or
 * ["*"] for full access within the merchant tenant).
 */
class ApiKey extends Model
{
    protected $fillable = [
        'merchant_id', 'name', 'key_prefix', 'key_hash',
        'abilities', 'last_used_at', 'revoked_at',
    ];

    protected $casts = [
        'abilities'    => 'array',
        'last_used_at' => 'datetime',
        'revoked_at'   => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Create a key and return [model, plaintext]. The plaintext is never
     * recoverable afterwards.
     *
     * @return array{0: self, 1: string}
     */
    public static function generate(Merchant $merchant, string $name, array $abilities = ['*']): array
    {
        $plaintext = 'om_live_' . Str::random(40);

        $key = self::create([
            'merchant_id' => $merchant->id,
            'name'        => $name,
            'key_prefix'  => substr($plaintext, 0, 12),
            'key_hash'    => hash('sha256', $plaintext),
            'abilities'   => $abilities,
        ]);

        return [$key, $plaintext];
    }

    public static function findByPlaintext(string $plaintext): ?self
    {
        return self::where('key_hash', hash('sha256', $plaintext))
            ->whereNull('revoked_at')
            ->first();
    }

    public function can(string $ability): bool
    {
        return in_array('*', $this->abilities, true) || in_array($ability, $this->abilities, true);
    }
}
