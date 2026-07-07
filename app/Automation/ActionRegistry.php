<?php

namespace App\Automation;

use App\Automation\Actions\LogAction;
use App\Automation\Contracts\ActionHandler;

/**
 * PLATFORM-002 Part 6 — maps stored action `type` strings to handlers.
 *
 * Core ships the reference LogAction; product actions (send coupon, issue
 * points, notify staff, create purchase request) register here as they are
 * approved and built — Apps may register their own via register().
 */
class ActionRegistry
{
    /** @var array<string, class-string<ActionHandler>> */
    private array $handlers = [
        'log' => LogAction::class,
    ];

    public function register(string $type, string $handlerClass): void
    {
        $this->handlers[$type] = $handlerClass;
    }

    public function has(string $type): bool
    {
        return isset($this->handlers[$type]);
    }

    public function resolve(string $type): ?ActionHandler
    {
        return $this->has($type) ? app($this->handlers[$type]) : null;
    }

    /** @return list<string> */
    public function types(): array
    {
        return array_keys($this->handlers);
    }
}
