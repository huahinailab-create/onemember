<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';
    case Blocked  = 'blocked';

    public function label(): string
    {
        return __("enums.member_status.{$this->value}");
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
