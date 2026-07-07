# Automation Engine (PLATFORM-002 P6)

WHEN {domain event} IF {conditions} THEN {actions} — engine only; the
merchant-facing visual rule builder is future work.

- **Rule** (`automation_rules`): merchant_id, `trigger_event` (event-bus
  name), `conditions` `[{field, operator, value}]` (ANDed, evaluated against
  the event payload), `actions` `[{type, params}]`, enabled, run stats.
- **Trigger side**: `AutomationEngine` wildcard-listens on the bus,
  tenant-scoped; matching rules queue one `RunAutomationAction` job per
  action (3 tries) — rules never slow the triggering request.
- **Conditions** (`ConditionEvaluator`): equals, not_equals, gt, gte, lt,
  lte, contains, exists. Unknown fields/operators fail closed.
- **Actions** (`ActionRegistry`): stable `type` string → `ActionHandler`
  implementation. Core ships `log` (reference). Product actions (send
  coupon, issue points, notify staff, create purchase request) register as
  they are approved; Apps may `register()` their own.
- **Time-based triggers** ("inactive 30 days", "birthday", "queue length
  over 25"): future scheduled commands emit synthetic domain events through
  this same engine — no second rule path.
