<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'member_id',
        'loyalty_program_id',
        'created_by',
        'type',
        'points',
        'balance_before',
        'balance_after',
        'purchase_amount',
        'invoice_number',
        'reference_type',
        'reference_id',
        'note',
        'created_at',
    ];

    protected $casts = [
        'type'            => TransactionType::class,
        'points'          => 'integer',
        'balance_before'  => 'integer',
        'balance_after'   => 'integer',
        'purchase_amount' => 'decimal:2',
        'created_at'      => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCredit(): bool
    {
        return $this->points > 0;
    }

    public function isDebit(): bool
    {
        return $this->points < 0;
    }
}
