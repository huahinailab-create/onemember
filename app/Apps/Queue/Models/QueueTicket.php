<?php

namespace App\Apps\Queue\Models;

use App\Models\Member;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PLATFORM-002 Part 8 — one queue ticket (walk-in or reservation).
 * Status machine: waiting → called → serving → done, with no_show and
 * cancelled as terminal side exits. Priority tickets are called first.
 */
class QueueTicket extends Model
{
    public const TRANSITIONS = [
        'waiting' => ['called', 'cancelled'],
        'called'  => ['serving', 'no_show', 'waiting'],
        'serving' => ['done'],
    ];

    protected $fillable = [
        'merchant_id', 'queue_counter_id', 'member_id', 'number', 'type',
        'priority', 'status', 'customer_name', 'customer_phone',
        'reserved_for', 'called_at', 'served_at', 'notes',
    ];

    protected $casts = [
        'priority'     => 'boolean',
        'reserved_for' => 'datetime',
        'called_at'    => 'datetime',
        'served_at'    => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(QueueCounter::class, 'queue_counter_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    /** Waiting line in calling order: priority first, then ticket number. */
    public function scopeWaitingLine(Builder $query): Builder
    {
        return $query->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('number');
    }
}
