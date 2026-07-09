<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** PLATFORM-002 Part 4 — one delivery attempt record (the webhook audit log). */
class WebhookDelivery extends Model
{
    protected $fillable = [
        'webhook_endpoint_id', 'event', 'payload', 'status',
        'attempts', 'response_code', 'last_error', 'delivered_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'delivered_at' => 'datetime',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }
}
