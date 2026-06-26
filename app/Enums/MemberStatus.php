<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';
    case Blocked  = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::Active   => 'Active',
            self::Inactive => 'Inactive',
            self::Blocked  => 'Blocked',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Active   => 'badge bg-success',
            self::Inactive => 'badge bg-secondary',
            self::Blocked  => 'badge bg-danger',
        };
    }
}
