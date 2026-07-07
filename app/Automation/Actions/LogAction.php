<?php

namespace App\Automation\Actions;

use App\Automation\Contracts\ActionHandler;
use App\Models\AutomationRule;
use Illuminate\Support\Facades\Log;

/**
 * PLATFORM-002 Part 6 — reference action: writes a structured log line.
 * Used by tests and as the template for real product actions.
 */
class LogAction implements ActionHandler
{
    public function execute(AutomationRule $rule, array $params, array $eventPayload): void
    {
        Log::info('automation.rule_fired', [
            'rule_id'     => $rule->id,
            'merchant_id' => $rule->merchant_id,
            'trigger'     => $rule->trigger_event,
            'message'     => $params['message'] ?? null,
        ]);
    }
}
