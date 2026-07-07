# Queue App (PLATFORM-002 P8)

First reference App on the Plugin SDK. Key: `queue`.

- **Entities**: `queue_counters` (stations, staff assignable);
  `queue_tickets` — daily number per merchant, `type` walk_in|reservation
  (`reserved_for` required for reservations), `priority` flag, status
  machine `waiting → called → serving → done` (+ `no_show`, `cancelled`;
  `called → waiting` re-queues).
- **Service**: `QueueService` — `issueTicket` (emits `queue.ticket_created`),
  daily numbering, `estimatedWaitMinutes` = people ahead × configured
  `avg_service_minutes` (app config), `todayStats` analytics.
- **UI**: board (`/queue`) with stat cards + call/serve actions; read-only
  auto-refreshing display (`/queue/display`) for a counter screen.
- **Notifications**: `notifyBySms` / `notifyByLine` are logging placeholders
  behind manifest feature flags — provider selection is a future decision.
