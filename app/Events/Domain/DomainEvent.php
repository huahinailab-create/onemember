<?php

namespace App\Events\Domain;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * PLATFORM-002 Part 3 — base class of the OneMember domain event bus.
 *
 * Modules communicate by dispatching these events instead of calling each
 * other: webhooks (Part 4), the automation engine (Part 6), and future Apps
 * subscribe via a single wildcard listener on `App\Events\Domain\*`.
 *
 * Rules:
 * - name() is the stable public identifier (dot.notation) used by webhook
 *   subscriptions and automation triggers. Never rename one once shipped.
 * - payload() must stay custodian-safe (ADR-010): ids and merchant-owned
 *   business fields only — never cross-merchant data, never secrets.
 * - Events are dispatched synchronously; slow consumers must queue
 *   themselves (see SendWebhook).
 *
 * Registry of shipped events: docs/dev/event-bus.md
 */
abstract class DomainEvent
{
    use Dispatchable;

    /** Stable dot-notation event name, e.g. 'member.created'. */
    abstract public function name(): string;

    /** Custodian-safe payload for webhooks/automation. */
    abstract public function payload(): array;

    /** Tenant the event belongs to (null only for platform-level events). */
    abstract public function merchantId(): ?int;
}
