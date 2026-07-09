<?php

namespace App\Apps\Queue\Models;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** PLATFORM-002 Part 8 — a serving counter/station (staff assignable). */
class QueueCounter extends Model
{
    protected $fillable = ['merchant_id', 'name', 'staff_name', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class);
    }
}
