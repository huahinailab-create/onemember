<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PLATFORM-002 Part 6 — one merchant automation rule.
 *
 * WHEN {trigger_event} IF {conditions all match} THEN {actions}.
 * conditions: [{field, operator, value}] evaluated against the domain
 * event payload; actions: [{type, params}] resolved via ActionRegistry.
 * The visual rule builder is future work — this is the engine only.
 */
class AutomationRule extends Model
{
    protected $fillable = [
        'merchant_id', 'name', 'trigger_event', 'conditions',
        'actions', 'enabled', 'last_run_at', 'run_count',
    ];

    protected $casts = [
        'conditions'  => 'array',
        'actions'     => 'array',
        'enabled'     => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
