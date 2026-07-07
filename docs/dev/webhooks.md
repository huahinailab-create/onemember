# Outbound Webhooks (PLATFORM-002 P4)

- **Subscription**: `webhook_endpoints` row (merchant-owned): url, secret,
  `events` (list of event-bus names or `["*"]`), active flag. Managed via a
  future API/admin surface — no merchant UI yet.
- **Fan-out**: `WebhookDispatcher` wildcard-listens on the event bus and
  creates one `webhook_deliveries` row + queues `SendWebhook` per matching
  endpoint. Tenant-scoped by construction.
- **Delivery**: POST JSON `{event, data, created_at}` with headers
  `X-OneMember-Event`, `X-OneMember-Timestamp`,
  `X-OneMember-Signature` = HMAC-SHA256(`timestamp.body`, endpoint secret).
  Receivers must verify the signature and reject stale timestamps.
- **Retry**: 5 attempts, backoff 30s/2m/10m/1h; each attempt logged
  (status, attempts, response_code, last_error).
- **Failure handling**: after the final attempt the delivery is `failed`;
  an endpoint whose last 10 deliveries all failed is auto-disabled
  (`disabled_at`) and logged.
