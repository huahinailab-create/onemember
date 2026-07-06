<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** APP-001 — Commerce App catalogue item. Merchant-owned entirely. */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'product_category_id',
        'name',
        'description',
        'price',
        'stock_qty',
        'status',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'stock_qty' => 'integer',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'active'
            && ($this->stock_qty === null || $this->stock_qty > 0);
    }
}
