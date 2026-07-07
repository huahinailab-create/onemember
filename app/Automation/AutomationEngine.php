<?php

namespace App\Automation;

use App\Events\Domain\DomainEvent;
use App\Jobs\RunAutomationAction;
use App\Models\AutomationRule;

/**
 * PLATFORM-002 Part 6 — the trigger side of the workflow engine.
 *
 * Wildcard-listens on the domain event bus; for each enabled rule of the
 * event's merchant whose trigger matches, evaluates the stored conditions
 * against the event payload and queues every action. Time-based triggers
 * ("inactive 30 days", "birthday") arrive later as scheduled commands that
 * dispatch synthetic domain events through this same engine.
 */
class AutomationEngine
{
    public function __construct(private readonly ConditionEvaluator $evaluator)
    {
    }

    public function handle(string $eventName, array $data): void
    {
        $event = $data[0] ?? null;

        if (! $event instanceof DomainEvent || $event->merchantId() === null) {
            return;
        }

        AutomationRule::where('merchant_id', $event->merchantId())
            ->where('trigger_event', $event->name())
            ->where('enabled', true)
            ->get()
            ->each(function (AutomationRule $rule) use ($event) {
                if (! $this->evaluator->matches($rule->conditions ?? [], $event->payload())) {
                    return;
                }

                foreach ($rule->actions as $action) {
                    RunAutomationAction::dispatch($rule, $action, $event->payload());
                }
            });
    }
}
