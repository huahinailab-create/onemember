<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialExtension extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'admin_user_id',
        'days',
        'previous_trial_ends_at',
        'new_trial_ends_at',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'days'                   => 'integer',
        'previous_trial_ends_at' => 'datetime',
        'new_trial_ends_at'      => 'datetime',
        'created_at'             => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $e) => $e->created_at ??= now());
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
