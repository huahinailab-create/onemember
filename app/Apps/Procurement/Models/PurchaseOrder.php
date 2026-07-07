<?php

namespace App\Apps\Procurement\Models;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** PLATFORM-002 Part 9 — purchase order (created from an approved request). */
class PurchaseOrder extends Model
{
    protected $fillable = [
        'merchant_id', 'purchase_request_id', 'supplier_id',
        'items', 'total_cost', 'status', 'expected_at',
    ];

    protected $casts = [
        'items'       => 'array',
        'total_cost'  => 'decimal:2',
        'expected_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }
}
