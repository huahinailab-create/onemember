<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case Draft  = 'draft';
    case Active = 'active';
    case Paused = 'paused';

    public function label(): string
    {
        return match($this) {
            self::Draft  => 'Draft',
            self::Active => 'Active',
            self::Paused => 'Paused',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft  => 'badge bg-secondary',
            self::Active => 'badge bg-success',
            self::Paused => 'badge bg-warning text-dark',
        };
    }
}
