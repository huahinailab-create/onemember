<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMemberLink extends Model
{
    protected $fillable = [
        'customer_id',
        'member_id',
        'merchant_id',
        'linked_via',
        'linked_at',
        'unlinked_at',
    ];

    protected $casts = [
        'linked_at'   => 'datetime',
        'unlinked_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
