<?php

namespace App\Events\Domain;

use App\Models\Member;

/**
 * PLATFORM-002 Part 3 — A member joined a merchant programme (any path: manual, import, scan-to-join).
 */
class MemberCreated extends DomainEvent
{
    public function __construct(public readonly Member $member)
    {
    }

    public function name(): string
    {
        return 'member.created';
    }

    public function payload(): array
    {
        return ["member_id" => $this->member->id, "member_code" => $this->member->member_code, "name" => $this->member->name, "created_via" => "platform"];
    }

    public function merchantId(): ?int
    {
        return $this->member->merchant_id;
    }
}
