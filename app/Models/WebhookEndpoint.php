<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PLATFORM-002 Part 4 — a merchant-owned outbound webhook subscription.
 * events = list of domain event names ('member.created', …) or ['*'].
 */
class WebhookEndpoint extends Model
{
    protected $fillable = ['merchant_id', 'name', 'url', 'secret', 'events', 'active', 'disabled_at'];

    protected $casts = [
        'events'      => 'array',
        'active'      => 'boolean',
        'disabled_at' => 'datetime',
    ];

    /** Endpoints auto-disable after this many consecutive delivery failures. */
    public const MAX_CONSECUTIVE_FAILURES = 10;

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    public function subscribesTo(string $eventName): bool
    {
        return in_array('*', $this->events, true) || in_array($eventName, $this->events, true);
    }
}
