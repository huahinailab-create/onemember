<?php

namespace App\Apps\Procurement\Models;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PLATFORM-002 Part 9 — purchase request with approval workflow.
 * draft → submitted → approved|rejected; approved → ordered (PO created).
 */
class PurchaseRequest extends Model
{
    public const TRANSITIONS = [
        'draft'     => ['submitted'],
        'submitted' => ['approved', 'rejected'],
        'approved'  => ['ordered'],
    ];

    protected $attributes = ['status' => 'draft'];

    protected $fillable = [
        'merchant_id', 'supplier_id', 'title', 'items', 'estimated_cost',
        'status', 'requested_by', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'items'          => 'array',
        'estimated_cost' => 'decimal:2',
        'approved_at'    => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }
}
