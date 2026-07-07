<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PLATFORM-002 Part 5 — v1 Member representation (reference resource).
 *
 * Deliberately minimal: the merchant's own business record only. Fields are
 * additive-only once shipped; breaking shape changes require /api/v2.
 */
class MemberResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'member_code'  => $this->member_code,
            'name'         => $this->name,
            'phone'        => $this->phone,
            'status'       => $this->status->value,
            'total_points' => (int) $this->total_points,
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
