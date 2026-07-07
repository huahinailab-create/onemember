# OneMember Developer Documentation (PLATFORM-002)

Platform foundation guides. Everything here runs inside the single Laravel 13
monolith (ADR-004/009/012) — no microservices; Apps are first-party modules
with clean namespaces, extractable later only on load evidence.

| Guide | Covers |
|---|---|
| [marketplace.md](marketplace.md) | App Registry, Manifests, install/enable lifecycle, health |
| [plugin-sdk.md](plugin-sdk.md) | Building an App: AppProvider, contracts, routes, nav, migrations |
| [event-bus.md](event-bus.md) | Domain events: registry, payload rules, subscribing |
| [webhooks.md](webhooks.md) | Outbound webhooks: subscriptions, signing, retry, failure handling |
| [public-api.md](public-api.md) | REST API conventions + [api/openapi.yaml](api/openapi.yaml) |
| [automation.md](automation.md) | WHEN/IF/THEN rule engine: triggers, conditions, actions |
| [localization.md](localization.md) | Internal vs customer languages; adding a language |
| [knowledge-center.md](knowledge-center.md) | Articles, versioning, context help, ? buttons |
| [apps/queue.md](apps/queue.md) | Queue App |
| [apps/procurement.md](apps/procurement.md) | Procurement App |
| [apps/commerce.md](apps/commerce.md) | Commerce App |

Golden rules for every subsystem: tenant scoping by merchant_id everywhere;
custodian principle (ADR-010) — never leak cross-merchant data; no money
handling (ADR-011); stable public identifiers (event names, API fields,
action types) are append-only once shipped.
