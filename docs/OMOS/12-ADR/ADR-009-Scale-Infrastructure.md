# ADR-009 — Scale Infrastructure: Redis, Object Storage, Observability

| Field | Value |
|---|---|
| **Status** | **Proposed** — awaiting CTO/PO approval (updates CTO-004) |
| **Date** | 2026-07-05 |
| **Author** | Claude Fable 5 (SCALE-000) |
| **Supersedes** | Partially updates CTO-004 (queue/session driver choice) |
| **Related Documents** | [Scalability-Review-2026-07.md](../10-Architecture/Scalability-Review-2026-07.md), [ADR-004](./ADR-004-Laravel-Architecture.md) |

---

## Context

CTO-004 set `database` for queue and session — correct for pilot scale, minimal moving parts. The Scalability Review (SCALE-000) targets 100k merchants / 1M members / 100M transactions and identifies the database-backed cache/queue/session as the first systemic bottleneck (B-01), local file storage as the horizontal-scaling blocker (B-06), and missing observability as the top operational risk (B-07).

## Decision (proposed)

1. **Redis** (one managed instance) becomes the driver for cache, queue, and session in production. Laravel Horizon supervises queues (`default`, `mail`, `passes`, `exports`). Development/test may keep `database`/`array` drivers — tests do not change.
2. **S3-compatible object storage** for all publicly served and multi-node-shared files (merchant logos, future exports and pass assets). `local` remains for request-scoped temp files only.
3. **Observability baseline** is mandatory for production: error tracking service, uptime checks on all three domains, queue-depth and failed-job alerts, slow-query logging, JSON logs with request-ID correlation shipped to a central sink.
4. These are configuration/infrastructure changes plus the small SCALE-001 code items (indexes, command batching, rate limiters). No application redesign is authorised by this ADR.

## Options Considered

**Redis vs keep database drivers:** database drivers couple background load to OLTP and collapse under mail bursts; Redis is the industry-standard Laravel path, zero code change. — chosen.
**Object storage vs NFS/local:** NFS adds ops fragility; object storage is cheaper and CDN-native. — chosen.
**Full APM suite vs baseline:** baseline (errors, uptime, queues, slow queries) covers launch; APM deferred to Year 1 if p95 targets are missed.

## Consequences

- New infrastructure dependencies: managed Redis, object storage bucket, error-tracking SaaS (vendor choice = BD-13 hosting decision).
- CTO-004 must be annotated: "database driver superseded in production by ADR-009; remains the default for dev/test."
- `.env.example` and deployment guide (doc 17) updated in SCALE-001.
- Cost impact requires PO budget sign-off (BD-13).
