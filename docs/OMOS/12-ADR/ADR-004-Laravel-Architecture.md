# ADR-004 — Laravel as Application Framework with Event-Driven Architecture

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-02 |
| **Author** | ChatGPT CTO |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [CTO-Decisions.md](../CTO-Decisions.md), [Known-Constraints.md](../Known-Constraints.md), [11-Standards/Coding-Standards.md](../11-Standards/Coding-Standards.md) |

---

## Context

OneMember requires an application framework for the Merchant SaaS backend. The choice of framework shapes every technical decision that follows: routing, ORM, authentication, queuing, testing, deployment.

Additionally, the architecture of cross-cutting concerns (email, audit logging, analytics event tracking) requires a pattern decision: synchronous (inline in controllers) or event-driven (events + listeners + queues).

## Decision

1. **Laravel 13.x with PHP 8.3+** is the application framework. Not to be replaced before Phase 3.
2. **Event-driven architecture** for all cross-cutting concerns. Controllers fire events. Listeners handle side effects. All side effects are queued. Controllers never send email directly.

## Options Considered

### Option A — Laravel (chosen)
Full-featured PHP framework. MVC pattern. Eloquent ORM. Built-in queues, events, notifications, auth scaffold (Breeze). Large community. Excellent documentation.

**Pros:** Complete ecosystem. Fast development. Well-understood by the AI developer team. Excellent documentation for troubleshooting.  
**Cons:** PHP is perceived as "not modern" in some circles. Framework upgrade effort every few years.

### Option B — Node.js (Express / Fastify)
JavaScript/TypeScript backend. Fast. Popular with modern teams.

**Pros:** Single language across frontend (if JS) and backend. High performance.  
**Cons:** No equivalent of Laravel's all-in-one ecosystem. Requires assembling packages for ORM, auth, queues, email, etc. More architectural decisions upfront. Not the AI developer team's primary strength.

### Option C — Serverless (AWS Lambda / Vercel)
Function-based architecture without persistent servers.

**Pros:** Auto-scaling. No server management.  
**Cons:** Cold starts create latency. Complex local development. Database connection pooling is non-trivial. Laravel integration (Vapor) adds complexity and cost.

## Rationale for Event-Driven Architecture

Direct email in controllers (synchronous) causes:
- HTTP response latency tied to mail server performance
- Requests failing if the mail server is temporarily unavailable
- Difficulty testing (must mock mailer in every test)

Event-driven approach:
- HTTP response completes immediately after the event is fired
- Email is sent in the background by a queued listener
- The listener is independently testable
- Failed jobs are automatically retried without user impact

Evidence: BUG-001 implemented the email verification polling fix. The queued email architecture ensured verification emails were sent reliably without blocking the registration response.

## Consequences

### Positive
- Laravel's batteries-included approach means less custom infrastructure
- Event-driven email is reliable, testable, and decoupled from HTTP
- `php artisan` CLI provides testing, migration, cache management, and queue management in one tool
- Eloquent ORM handles multi-tenant data scoping naturally via model scopes

### Negative
- Queue worker must be running in production (`php artisan queue:work` as a Forge daemon)
- Any failure in the queue worker silently delays background tasks until the worker restarts
- Framework upgrades (Laravel 13 → 14+) require periodic upgrade sprints

### Risks
- Queue worker going down silently stops email delivery. Mitigated by: Forge monitoring + `php artisan queue:monitor`.
- Eloquent N+1 queries if relationships are not eager-loaded. Mitigated by: Laravel Telescope in development, query log monitoring.

## Validation

Architecture is working when: (a) the application has been in production for 6+ months without a framework-related production incident, and (b) background email delivery is reliable with no reported cases of emails not arriving.
