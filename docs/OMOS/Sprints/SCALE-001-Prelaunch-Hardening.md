# SCALE-001 — Pre-Launch Scale Hardening

| Field | Value |
|---|---|
| **Status** | ✅ Ready pending ADR-009 approval + BD-13 (hosting/vendor) |
| **Classification** | Type B (infrastructure config + schema indexes) |
| **Complexity** | Medium (≈ 1 sprint) |
| **Dependencies** | ADR-009 approved; production hosting chosen (BD-13) |
| **Source** | [Scalability-Review §3 tier 🔴](../10-Architecture/Scalability-Review-2026-07.md) |

## Objective
Remove every "must do before launch" bottleneck (B-01…B-07) with zero behaviour change.

## Files Expected to Change
- `config/cache.php`, `config/queue.php`, `config/session.php`, `.env.example` — Redis production defaults; `config/horizon.php` (new, package)
- `config/filesystems.php` — s3 disks (`public-assets`, `exports`)
- `app/Services/MerchantBrandingService.php` — disk name only (no logic)
- `app/Console/Commands/ProcessBirthdayRewards.php`, `ProcessPointExpiry.php` — `->get()` → `->lazyById(1000)`
- `bootstrap/app.php` / route groups — named rate limiters (login, join, export)
- `app/Http/Middleware/AssignRequestId.php` (new) + `config/logging.php` — JSON + request-ID
- One migration file: six indexes (see DB impact)
- `docs/17-Production-Deployment-Guide.md`, `docs/18-Operations-Runbook.md` — Redis/Horizon/monitoring/restore-drill sections

## Database Impact
Single additive migration (tested `down()` per DX-001):
`members(merchant_id,phone)`, partial-unique `members(merchant_id,phone) where deleted_at is null`, `members(merchant_id,last_activity_at)`, `members(merchant_id,postal_code)`, `transactions(loyalty_program_id,created_at)`, `transactions(merchant_id,created_at)`.
⚠️ Pre-check for existing (merchant_id,phone) duplicates among soft-deleted-null rows before unique index; report list to PO if found (no silent data change).

## Test Plan
- Full suite green with `QUEUE_CONNECTION=database` (test env unchanged)
- New: command batching tests (birthday/expiry with 2,500-member fixture, chunked, same results)
- Rate-limiter tests (429 on abuse thresholds)
- Migration up/down cycle test in CI
- Manual: Horizon dashboard on staging; S3 logo upload/serve round-trip

## Acceptance Criteria
1. Production config uses Redis for cache/queue/session; Horizon supervises 4 named queues.
2. All six indexes exist; duplicate-phone pre-check report delivered.
3. Birthday/expiry commands never materialise > 1,000 members in memory.
4. Logos/exports served from object storage via CDN; app boots on a clean node with no local state.
5. Error tracking + uptime + queue alerts firing in staging test.
6. 521+ existing tests pass unmodified.
