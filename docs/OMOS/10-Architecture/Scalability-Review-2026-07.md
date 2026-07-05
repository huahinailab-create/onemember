# Scalability Review — National Scale Readiness (SCALE-000)

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Review — awaiting CTO/PO ratification |
| **Last Updated** | 2026-07-05 |
| **Author** | Claude Fable 5 |
| **Scale Target** | 100,000 merchants · 1,000,000 members · 100,000,000 transactions |
| **Related Documents** | [ADR-004](../12-ADR/ADR-004-Laravel-Architecture.md), [ADR-008](../12-ADR/ADR-008-Phase-2-Customer-Wallet-Architecture.md), [ADR-009 (Proposed)](../12-ADR/ADR-009-Scale-Infrastructure.md), [Engineering-Backlog.md](../Engineering-Backlog.md), [17-Production-Deployment-Guide](../../17-Production-Deployment-Guide.md), [19-Backup-and-Disaster-Recovery](../../19-Backup-and-Disaster-Recovery.md), [20-Performance-Optimization](../../20-Performance-Optimization.md) |

---

## 0. Verdict

**Yes — the current architecture supports the target scale without major redesign**, on three conditions:

1. Move cache, queue, and session from the `database` driver to **Redis** before serious load (single biggest lever; pure config + infra, zero code redesign).
2. Close the **index gaps** listed in §2 before the transactions table grows (cheap now, expensive at 100M rows).
3. Keep the **read/write discipline** we already have (merchant_id scoping, event-driven side effects, queued email) — the model is sound; the risks are operational, not structural.

Scale math: 100M transaction rows at ~120 bytes/row ≈ 12–15 GB data + similar index volume — comfortable for a single managed MySQL 8 primary (16–32 GB RAM class) with a read replica. 1M members and 100k merchants are small tables by comparison. The multi-tenant single-DB design (CTO-005) holds; sharding is not needed at this target.

---

## 1. Area-by-Area Review

### 1.1 Database Indexes — 🟠 gaps found (verified against live schema)

Present and good: `members(merchant_id,status)`, `members(merchant_id,total_points)`, `transactions(merchant_id,type,created_at)`, `transactions(member_id,created_at)`, `redemptions(merchant_id,status)`.

**Missing (must add before launch — SCALE-001):**

| Index | Why |
|---|---|
| `members(merchant_id, phone)` | Counter Mode search, claim flow, CSV dedup — currently full scans per merchant |
| `transactions(loyalty_program_id, created_at)` | Campaign Analytics (RELEASE-4A) queries group on this |
| `transactions(merchant_id, created_at)` | Admin dashboards, exports (type-less range scans) |
| `members(merchant_id, last_activity_at)` | Win-back (MVP-008) + point-expiry scans |
| `members(merchant_id, postal_code)` | Postal analytics widget |
| Unique `members(merchant_id, phone)` partial (where deleted_at null) | Uniqueness is validation-only today — race condition under concurrent joins (wallet makes this real) |

### 1.2 Multi-Tenancy — 🟢 sound
Single DB + `merchant_id` on every row + scoped queries (CTO-005) is the right model to 100k tenants. Guards: `TenantIsolationTest` exists; wallet adds the customer dimension (`WalletTenantIsolationTest`, PH2-001B). **Do not** introduce per-tenant schemas/databases — operational cost explodes, zero benefit at this profile.

### 1.3 Queue Architecture — 🟠 driver swap needed
`database` queue is correct today (CTO-004) but at national scale (email bursts: 1M birthday-month members) the jobs table becomes a hot spot competing with OLTP. **Redis queue + Laravel Horizon** (supervision, metrics, per-queue balancing). Define queues: `default`, `mail`, `passes` (Phase 2), `exports`. Code change: none (driver config). Update CTO-004 via ADR-009.

### 1.4 Redis Usage — 🟠 introduce (ADR-009)
One managed Redis instance serves cache + queue + session. Sizing: trivial (<2 GB) at target scale. Sessions on Redis remove DB session writes on every request. This is the single "new infrastructure" item this review introduces — everything else is configuration of what exists.

### 1.5 Cache Strategy — 🟡 pattern exists, expand deliberately
`MerchantIntelligenceService` already uses `Cache::remember`. Standardise: merchant dashboard counters (5 min TTL — PERF-001), wallet card list (invalidate on `WalletBalanceChanged`), admin platform metrics (15 min), corporate pages (full-page, 1 h). Rule: cache derived aggregates only, never authorization or consent state.

### 1.6 Search Strategy — 🟢 SQL is enough at target
All search is per-merchant (≤ thousands of members per tenant) → indexed `LIKE 'prefix%'` / equality is fine. National-scale *cross-tenant* search exists only in `/admin` (100k merchants — still fine with indexes). Full-text (Meilisearch/Scout) is **only-after-very-large-scale**, and only if product adds fuzzy consumer-facing search (wallet directory with geo, Phase 2.1+ — flagged as BD-11 in the gap review).

### 1.7 File Storage — 🟠 move to S3-compatible object storage
Today: `FILESYSTEM_DISK=local` (logos via MerchantBrandingService, CSV import temp files, future export files, pass assets). Local disk breaks horizontal scaling (§1.12) — first second web server fails. Move `public` + new `exports`/`passes` disks to S3-compatible storage (AWS S3 ap-southeast-1/7 or Cloudflare R2). Laravel flysystem = config change + one-time asset copy. Temp import files can stay local (request-scoped) or move for statelessness.

### 1.8 Image Storage — 🟡 same move + processing rules
Merchant logos: enforce size/format on upload (exists), generate the 2–3 needed variants at upload time (pass strip images, dashboard thumb) via queued job, store variants in object storage, serve through CDN (§1.14). No image server needed at this scale.

### 1.9 Reporting — 🟠 protect OLTP
Exports (`ExportService`) and admin reports currently query the primary synchronously. At 100M rows: (a) run heavy reads on a **read replica** (Laravel `read` connection — config), (b) queue exports > 10k rows and email a signed link (pattern already designed for PDPA export), (c) cursor/chunked queries (`lazyById`) in all export code paths — verify in SCALE-001.

### 1.10 Analytics — 🟡 pre-aggregate at Year-1
Live queries (RELEASE-4A, admin funnel) are fine to ~10M transactions. Year-1: nightly rollup tables `daily_merchant_stats(merchant_id, day, points_issued, points_redeemed, tx_count, purchase_total)` and `daily_campaign_stats` — populated by a scheduled job, additive schema, dashboards read rollups + today-live delta. This defers any OLAP store far beyond 100M rows. Design principle: rollups are derived data — rebuildable from transactions, never authoritative.

### 1.11 Background Jobs — 🟢 pattern right, add batching
All commands are chunk-safe or small; two need batching before 1M members: `ProcessBirthdayRewards` and `ProcessPointExpiry` currently `->get()` full member sets per campaign → switch to `lazyById(1000)` (SCALE-001, small code change, no behaviour change). Horizon gives retry/backoff visibility.

### 1.12 Horizontal Scaling — 🟢 ready once state is external
App is stateless except: local sessions (→ Redis), local files (→ S3), scheduler (run on exactly one node — `schedule:run` on a designated worker or `onOneServer()`). Then N web nodes + M queue workers behind LB scale linearly. Laravel Octane is an option later, not required.

### 1.13 Load Balancing — 🟢 standard
Managed LB (ALB/Cloudflare) → 2+ app nodes. Health endpoint exists (`HealthController`). Sticky sessions unnecessary with Redis sessions. TLS at the edge; three domains (corporate/app/wallet) on one cert via SAN or per-domain certs.

### 1.14 CDN — 🟡 easy win
Cloudflare in front of all three domains: static assets (Vite-hashed — safe to cache forever), merchant logos from object storage, corporate site full-page cache. Thai PoPs make TTFB matter for the 15-second join promise. Rule: never cache authenticated HTML.

### 1.15 Backup & Disaster Recovery — 🟠 verify at scale
Doc 19 + `VerifyDatabaseBackup` command exist. Gaps at 100M rows: (a) `mysqldump` window becomes hours — move to snapshot-based backups (managed DB point-in-time recovery) + logical dump weekly; (b) define and **test** RTO/RPO (proposal: RPO ≤ 15 min via PITR, RTO ≤ 4 h — needs PO sign-off, BD-12); (c) object storage versioning for logos/exports; (d) quarterly restore drill added to Operations Runbook.

### 1.16 Security — 🟢 strong baseline, scale-specific adds
Existing: headers middleware, scoped tenancy, event-driven audit, verified webhooks, admin flag isolation. Add for scale: per-route rate limiting beyond OTP (login, join, export endpoints), WAF at the edge (Cloudflare), secrets to a managed store at multi-node (SSM/Vault — .env-per-node is error-prone), dependency scanning in CI. SEC-002 (CSP unsafe-inline) remains scheduled tech debt; wallet views are born clean.

### 1.17 Monitoring — 🟠 gap
Nothing beyond `/admin` health widget + `HealthController`. Needed before launch: uptime checks (all 3 domains), error tracking (Sentry/Bugsnag — Laravel integration is config), queue depth + failed-jobs alerting (Horizon), DB slow-query log shipping, disk/CPU alarms, SMS-spend alarm (wallet OTP, R-02). Dashboards: p95 request time per domain, jobs/s, OTP success rate.

### 1.18 Logging — 🟠 centralise
Single-file `stack` logging today. Multi-node needs: JSON-formatted logs → centralised sink (CloudWatch/Loki/ELK — pick with hosting, BD-13), request-ID correlation middleware (tiny), PII masking rule (phone masking already specified in wallet design — apply platform-wide), retention 30–90 days ops / security events 1 y (align with PDPA, BD-10).

### 1.19 Future Microservice Boundaries — 🟢 document, don't build
The monolith is correct for this team and scale (ADR-004, ADR-008 Option A). Clean seams to preserve, in extraction order if ever needed:
1. **Wallet** (`wallet.onemember.co` domain group + wallet services + customer tables) — already designed as a seam (ADR-008).
2. **Pass services** (stateless, queue-driven — trivially extractable).
3. **Analytics/rollups** (read-only, own store).
4. **Enterprise Bridge** (PH2-003 — design as an internal module with an explicit interface from day one).
Never extract: loyalty core (members/transactions/campaigns) — it IS the product; splitting it creates distributed-transaction pain for zero gain.

---

## 2. Bottleneck Register (every identified bottleneck)

| # | Bottleneck | Trigger point | Fix | Tier |
|---|---|---|---|---|
| B-01 | `database` cache/queue/session contending with OLTP | ~50–100 req/s or big mail bursts | Redis (ADR-009) | **Launch** |
| B-02 | Missing `members(merchant_id,phone)` index | any large merchant | add index | **Launch** |
| B-03 | Phone uniqueness by validation only (race) | concurrent wallet joins | partial unique index | **Launch** |
| B-04 | `transactions` lacking campaign/date-only indexes | ~5M rows | add 2 indexes | **Launch** |
| B-05 | Birthday/expiry commands load full member sets | ~100k members | `lazyById` batching | **Launch** |
| B-06 | Local file storage blocks 2nd app node | first horizontal step | S3-compatible disks | **Launch** |
| B-07 | No error tracking/queue alerting | first production incident | monitoring stack | **Launch** |
| B-08 | Synchronous large exports on primary | ~1M members / 10M tx | queued exports + replica | Year 1 |
| B-09 | Live analytics queries on raw transactions | ~10M tx | rollup tables | Year 1 |
| B-10 | Dashboard N-queries per load | ~1k concurrent merchants | PERF-001 caching | Year 1 |
| B-11 | mysqldump backup window | ~20 GB data | snapshot/PITR backups | Year 1 |
| B-12 | Single-region, single-primary DB | ~500k merchants / regional expansion | read replicas per region; evaluate partitioning `transactions` by created_at | Very large |
| B-13 | Consent audit table growth | ~50M consent rows | yearly partitioning | Very large |
| B-14 | Cross-tenant admin search | ~1M merchants | search engine for /admin only | Very large |
| B-15 | Blade SSR CPU at extreme concurrency | ~5k req/s | Octane / edge cache expansion | Very large |

## 3. Tiered Action Plan

### 🔴 Must do before launch (→ implementation spec SCALE-001)
1. Redis for cache/queue/session + Horizon (ADR-009).
2. Six index additions incl. phone partial-unique (one migration sprint; DX-001 down() tested).
3. `lazyById` batching in birthday/expiry commands.
4. Object storage for public assets + exports.
5. Monitoring baseline: error tracking, uptime, queue alerts, slow-query log; JSON logs + request IDs.
6. Rate limiting on auth/join/export routes; CDN + WAF in front of all domains.
7. Backup: enable PITR, document + test restore once.

### 🟡 Recommended during Year 1
8. Read replica + `read` connection for admin/exports/analytics.
9. Rollup tables for merchant + campaign analytics (B-09).
10. PERF-001 dashboard caching; PERF-002 member pagination verification.
11. Queued large exports with signed download links.
12. Quarterly restore drills; SEC-003 webhook idempotency table; secrets manager.
13. SEC-002 CSP cleanup (bundle with a UI-refactor sprint).

### 🟢 Only after very large scale (do not build now)
14. Transactions partitioning / archival tier (per-year).
15. Regional read replicas; multi-region active/passive.
16. Search engine; OLAP store; service extraction along §1.19 seams.
17. Octane/worker autoscaling.

## 4. What This Review Explicitly Rejects

- **Sharding or per-tenant databases** — unnecessary at 100k tenants, permanent complexity.
- **Microservice split now** — team size and product stage make the monolith strictly better (consistent with ADR-004/008).
- **Kafka/event buses** — Laravel events + Redis queues cover every current and Phase-2 flow.
- **NoSQL stores** — the data is relational; the glossary's transaction-ledger invariant depends on it.
