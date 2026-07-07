<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case Draft  = 'draft';
    case Active = 'active';
    case Paused = 'paused';

    public function label(): string
    {
        return __("enums.campaign_status.{$this->value}");
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
