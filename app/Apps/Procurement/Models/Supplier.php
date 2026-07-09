<?php

namespace App\Apps\Procurement\Models;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** PLATFORM-002 Part 9 — merchant-owned supplier with vendor rating. */
class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'merchant_id', 'name', 'category', 'contact_person', 'phone',
        'email', 'address', 'rating_avg', 'rating_count', 'active',
    ];

    protected $casts = ['active' => 'boolean', 'rating_avg' => 'decimal:2'];

    protected static function booted(): void
    {
        static::created(fn (self $supplier) => event(new \App\Events\Domain\SupplierCreated($supplier)));
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /** Incremental vendor rating (1–5). */
    public function rate(int $stars): void
    {
        $stars = max(1, min(5, $stars));
        $total = ((float) $this->rating_avg * $this->rating_count) + $stars;
        $this->rating_count++;
        $this->rating_avg = round($total / $this->rating_count, 2);
        $this->save();
    }
}
