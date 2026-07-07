<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PLATFORM-002 Part 1 — per-merchant App state.
 *
 * The legacy install list (merchant settings `installed_apps`) remains the
 * backward-compatible source of "is installed"; this row adds version,
 * enable/disable, and per-merchant configuration. A missing row for an
 * installed app means "enabled with defaults" (pre-marketplace merchants).
 */
class MerchantApp extends Model
{
    protected $fillable = [
        'merchant_id',
        'app_key',
        'version',
        'enabled',
        'config',
        'installed_at',
    ];

    protected $casts = [
        'enabled'      => 'boolean',
        'config'       => 'array',
        'installed_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
