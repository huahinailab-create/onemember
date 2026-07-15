<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * CUSTOMER-001B — one entry in a customer's permanent address book.
 * Belongs ONLY to the customer. Merchants never receive a reference to this
 * row — orders carry a plain-text snapshot of the chosen address instead.
 */
class CustomerAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'label', 'recipient_name', 'phone',
        'country', 'admin_area_1', 'admin_area_2', 'admin_area_3', 'admin_area_4',
        'postal_code', 'line1', 'line2', 'building', 'floor', 'unit',
        'landmark', 'delivery_instructions',
        'latitude', 'longitude',
        'is_default', 'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (CustomerAddress $address) {
            $address->uuid ??= (string) Str::uuid();
        });
    }

    /** Public routes bind by uuid — internal ids never leave the system. */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Active (not archived) addresses. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * The address rendered as display lines in this country's conventional
     * order (config field order, smallest area → largest), for the address
     * book UI and for the plain-text order snapshot merchants receive.
     *
     * @return list<string>
     */
    public function displayLines(): array
    {
        $config = config("customer_address.countries.{$this->country}", []);
        $order  = $config['fields'] ?? ['line1', 'line2', 'admin_area_2', 'admin_area_1', 'postal_code'];

        $lines = [];

        $unitParts = array_filter([
            $this->building,
            $this->floor !== null ? __('customer_address.floor_short', ['floor' => $this->floor]) : null,
            $this->unit !== null ? __('customer_address.unit_short', ['unit' => $this->unit]) : null,
        ]);
        if ($unitParts !== []) {
            $lines[] = implode(' · ', $unitParts);
        }

        $lines[] = $this->line1;
        if ($this->line2) {
            $lines[] = $this->line2;
        }

        // Administrative areas smallest → largest, then postcode, per config order.
        $areaLine = [];
        foreach ($order as $field) {
            if (str_starts_with($field, 'admin_area_') && $this->{$field}) {
                $areaLine[] = $this->{$field};
            }
        }
        if ($this->postal_code) {
            $areaLine[] = $this->postal_code;
        }
        if ($areaLine !== []) {
            $lines[] = implode(', ', $areaLine);
        }

        if ($this->landmark) {
            $lines[] = __('customer_address.near', ['landmark' => $this->landmark]);
        }

        return array_values(array_filter($lines));
    }

    /** One-line rendering for compact lists. */
    public function oneLine(): string
    {
        return implode(', ', $this->displayLines());
    }

    /**
     * The plain-text snapshot a merchant receives when this address is used
     * on an order: recipient + phone + address lines + instructions. This is
     * the ONLY form in which an address ever leaves the customer's book.
     */
    public function orderSnapshot(): string
    {
        $lines = array_filter([
            trim($this->recipient_name.($this->phone ? ' · '.$this->phone : '')),
            ...$this->displayLines(),
            $this->delivery_instructions,
        ]);

        return implode("\n", $lines);
    }
}
