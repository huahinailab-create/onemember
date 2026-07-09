<?php

namespace App\Automation\Contracts;

use App\Models\AutomationRule;

/**
 * PLATFORM-002 Part 6 — one executable automation action type.
 *
 * Implementations are registered in ActionRegistry under a stable string
 * key (the `type` stored in AutomationRule.actions). Handlers must be
 * idempotent-friendly and tenant-safe: only act on the rule's merchant.
 */
interface ActionHandler
{
    /**
     * @param array $params        the action's stored parameters
     * @param array $eventPayload  the triggering domain event payload
     */
    public function execute(AutomationRule $rule, array $params, array $eventPayload): void;
}
