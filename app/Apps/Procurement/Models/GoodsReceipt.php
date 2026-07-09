<?php

namespace App\Apps\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** PLATFORM-002 Part 9 — goods received against a purchase order. */
class GoodsReceipt extends Model
{
    protected $fillable = ['merchant_id', 'purchase_order_id', 'items', 'notes', 'received_by'];

    protected $casts = ['items' => 'array'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
