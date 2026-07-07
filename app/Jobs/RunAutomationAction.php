<?php

namespace App\Jobs;

use App\Automation\ActionRegistry;
use App\Models\AutomationRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * PLATFORM-002 Part 6 — executes one matched automation action off-request.
 * Rules never slow down the triggering transaction; failures are logged
 * and retried by the queue, never bubbled to the user.
 */
class RunAutomationAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly AutomationRule $rule,
        public readonly array $action,
        public readonly array $eventPayload,
    ) {
    }

    public function handle(ActionRegistry $registry): void
    {
        $type    = $this->action['type'] ?? '';
        $handler = $registry->resolve($type);

        if (! $handler) {
            Log::warning('automation.unknown_action', ['rule_id' => $this->rule->id, 'type' => $type]);

            return;
        }

        $handler->execute($this->rule, $this->action['params'] ?? [], $this->eventPayload);

        $this->rule->forceFill([
            'last_run_at' => now(),
            'run_count'   => $this->rule->run_count + 1,
        ])->save();
    }
}
