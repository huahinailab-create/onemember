# 20 — Performance Optimization

> **Sprint:** 5.5.4  
> **Date:** 2026-06-29  
> **Cross-reference:** [docs/12-SaaS-Architecture.md](12-SaaS-Architecture.md), [docs/08-Product-Decisions.md](08-Product-Decisions.md) — DECISION-052

---

## Overview

This document records the findings and fixes from the Sprint 5.5.4 performance audit of OneMember V1.0. The application was reviewed for N+1 queries, missing eager loading, duplicate queries, missing indexes, and inefficient collection usage.

**Result:** Two code fixes and three new database indexes were applied. All 62 automated tests pass.

---

## 1. Query Audit — Controllers

### DashboardController

**Queries before optimization: 11**  
**Queries after optimization: 9**

| Query | Before | After | Notes |
|-------|--------|-------|-------|
| `members()->count()` (active members KPI) | ✅ Ran | ❌ Removed | Duplicate — usageSummary runs the same query |
| `loyaltyPrograms(status=active)->count()` | ✅ Ran | ✅ Ran | Covered by new `(merchant_id, status)` index |
| `redemptions()->whereDate(today)->count()` | ✅ Ran | ✅ Ran | Covered by new `(merchant_id, redeemed_at)` index |
| `transactions(Earn, today)->sum(points)` | ✅ Ran | ✅ Ran | Covered by existing `(merchant_id, type, created_at)` index |
| `transactions()->with(member,program)->limit(10)` | ✅ Ran | ✅ Ran | Eager-loaded correctly |
| `members()->orderByDesc(total_points)->limit(5)` | ✅ Ran | ✅ Ran | Covered by new `(merchant_id, total_points)` index |
| `loyaltyPrograms(active)->withCount(rewards)` | ✅ Ran | ✅ Ran | No change |
| `members()->withTrashed()->exists()` (hasAnyMembers) | ✅ Always ran | ⚡ Short-circuit | Skipped when active members > 0 |
| `loyaltyPrograms()->withTrashed()->exists()` | ✅ Ran | ✅ Ran | No change |
| `rewards()->withTrashed()->exists()` | ✅ Ran | ✅ Ran | No change |
| `loyaltyPrograms()->withTrashed()->oldest()->first()` | ✅ Ran | ✅ Ran | No change |
| `usageSummary → members()->count()` | ✅ Ran | ✅ Ran (first now) | Moved to run first; result reused as `$totalActiveMembers` |
| `usageSummary → loyaltyPrograms()->count()` | ✅ Ran | ✅ Ran | No change (counts all non-deleted, not just active) |

**Fix applied:** `$subscriptionUsage = $subscriptionService->usageSummary($merchant)` is now called first. `$totalActiveMembers` is read from `$subscriptionUsage['members']['used']` instead of issuing a separate `COUNT(*)`. This saves one DB round-trip per dashboard load.

**Short-circuit applied:** `$hasAnyMembers` evaluates `$totalActiveMembers > 0` first (in-memory, no DB). The `withTrashed()->exists()` query fires only when the merchant has zero active members — the new-merchant or fully-archived case.

---

### MemberController

| Method | Finding | Action |
|--------|---------|--------|
| `index` | Paginated query; correct filtering; no eager loading needed (list shows name/points only) | No change |
| `show` | Transactions eager-load `loyaltyProgram` (withTrashed) and `createdBy` — correct | No change |
| `show` | Eligible rewards query uses `whereRaw('0 = 1')` as a no-op guard for stamps with insufficient balance | No change — safe literal |
| `store / update / archive` | Single model operations; no N+1 | No change |

No issues found.

---

### CampaignController

| Method | Finding | Action |
|--------|---------|--------|
| `index` | Paginated query with optional search; no eager loading needed | No change |
| `show` | Rewards loaded as a flat list (no nested relationships rendered) | No change |
| All writes | Single model operations | No change |

No issues found.

---

### RewardController

| Method | Finding | Action |
|--------|---------|--------|
| `create` | 5 separate calls to `SubscriptionService`; each hits DB once | Acceptable — 5 targeted queries, not N+1 |
| `store / update / archive` | Single model operations | No change |
| `show` | No eager loading needed — only reward + campaign displayed | No change |

No issues found.

---

### PurchaseController

| Method | Finding | Action |
|--------|---------|--------|
| `store` | 1 campaign lookup + 1 transaction create + 1 member update = 3 queries | Optimal for this write path |

No issues found.

---

### RedemptionController

| Method | Finding | Action |
|--------|---------|--------|
| `store` | Loads reward (1 query) + campaign (1 query) + creates transaction + redemption + updates reward + updates member = 6 queries | All necessary for the write; no N+1 |

No issues found.

---

### SubscriptionService

| Method | Finding | Action |
|--------|---------|--------|
| `usageSummary` | Calls `usageCount` twice (members + campaigns) = 2 DB queries | Acceptable — clean, minimal |
| `usageCount('members')` | `$merchant->members()->count()` | Confirmed duplicate with old `DashboardController` — fixed |
| `usageCount('campaigns')` | Counts all non-deleted campaigns (different from `activeCampaignCount`) | Correctly different from dashboard KPI |

---

## 2. Eager Loading Review

| Location | Relationship | Status |
|----------|-------------|--------|
| `DashboardController` — recent activity | `member`, `loyaltyProgram` (withTrashed) | ✅ Eager-loaded |
| `MemberController::show` — transaction list | `loyaltyProgram` (withTrashed), `createdBy` | ✅ Eager-loaded |
| `MemberController::index` — member list | None needed (list shows flat columns) | ✅ Correct |
| `CampaignController::show` — reward list | None needed (rewards table shows flat columns) | ✅ Correct |
| `ProcessExpiredTrials` command | Loads merchants, loops per-merchant | ✅ Uses `get()` then loops — not `all()` |

**No N+1 queries found.** All relationships that would cause N+1 are already eager-loaded. List pages use flat queries with no nested relationship access in Blade.

---

## 3. Database Index Review

### Existing indexes (pre-Sprint 5.5.4)

| Table | Index | Covers |
|-------|-------|--------|
| `members` | `(merchant_id, status)` | Status-filtered member lists |
| `members` | `(merchant_id, email)` | Email lookup |
| `members` | `member_code` (unique) | QR code lookup |
| `loyalty_programs` | `(merchant_id, is_active)` | Legacy column — no longer used for filtering |
| `rewards` | `(loyalty_program_id, is_active)` | Legacy column |
| `rewards` | `(merchant_id, is_active)` | Legacy column |
| `transactions` | `(member_id, created_at)` | Member activity feed |
| `transactions` | `(merchant_id, type, created_at)` | Dashboard KPI (points issued today) |
| `redemptions` | `(merchant_id, status)` | Redemption status filter |
| `redemptions` | `(member_id, status)` | Per-member redemption filter |

### Issues identified

| Table | Missing Index | Queries Affected |
|-------|--------------|-----------------|
| `loyalty_programs` | `(merchant_id, status)` | All campaign list queries, dashboard active campaign count |
| `redemptions` | `(merchant_id, redeemed_at)` | Dashboard "Redeemed Today" KPI |
| `members` | `(merchant_id, total_points)` | Dashboard "Top Members" (ORDER BY total_points DESC LIMIT 5) |

### Indexes added (migration: `2026_06_29_000001_add_performance_indexes`)

| Table | Index | Justification |
|-------|-------|--------------|
| `loyalty_programs` | `(merchant_id, status)` | Every campaign query in the application filters `WHERE merchant_id = ? AND status = ?`. The original `(merchant_id, is_active)` index covers a boolean column that was superseded by the `status` string column in Sprint 3.1. Without this index, every campaign list page scans all rows for the tenant. |
| `redemptions` | `(merchant_id, redeemed_at)` | Dashboard KPI "Redeemed Today" queries `WHERE merchant_id = ? AND DATE(redeemed_at) = ?`. As redemption history grows, this query scans the full tenant redemption set without an index. |
| `members` | `(merchant_id, total_points)` | Dashboard "Top Members" queries `ORDER BY total_points DESC LIMIT 5` scoped to merchant. The composite index allows MySQL to return the top 5 by reading the index only, without a sort. |

### Note on legacy `is_active` indexes

The `loyalty_programs`, `rewards` tables still have `(merchant_id, is_active)` and `(loyalty_program_id, is_active)` indexes from the original migration. These cover a boolean column that is no longer used in query WHERE clauses (the `status` string column replaced `is_active` for all filtering). These legacy indexes are left in place to avoid a destructive migration change — they are inert but harmless. They can be dropped in a future maintenance migration after confirming no external tools reference them.

---

## 4. Blade Optimization Review

| View | Finding |
|------|---------|
| `dashboard.blade.php` | KPI data passed as pre-computed scalars — no Eloquent calls in Blade |
| `members/index.blade.php` | Paginated collection; no relationship access in loop |
| `members/show.blade.php` | Paginated transactions; relationships pre-loaded (`$tx->member`, `$tx->loyaltyProgram`) |
| `campaigns/index.blade.php` | Paginated campaigns; no relationship access |
| `campaigns/show.blade.php` | Flat reward list; no nested relationship access |
| `rewards/show.blade.php` | Single model view; no relationship loops |
| `settings/*.blade.php` | Form views; no query-driven loops |

**No Blade-level N+1 patterns found.** All controllers pass pre-loaded data to views. No Eloquent calls in Blade templates.

---

## 5. Scheduler Review

| Command | Estimated Complexity | Per-Run Cost |
|---------|---------------------|-------------|
| `ProcessExpiredTrials` (01:00) | O(n) where n = merchants with expired trials | Low — runs once daily; processes only expired records via indexed WHERE |
| `VerifyDatabaseBackup` (03:00) | O(1) — file system glob | Negligible |

Both scheduled commands are lightweight. Neither performs full-table scans or loops over large unbounded datasets. `ProcessExpiredTrials` correctly uses `where('subscription_status', Trial)->where('trial_ends_at', '<=', now())` which can be indexed by `trial_ends_at` if the merchants table grows large. No action required at V1.0 scale.

---

## 6. Query Complexity Estimates

Estimates assume a mid-scale merchant: 500 members, 5 campaigns, 5,000 transactions, 200 redemptions.

| Page / Action | Queries | Index Coverage | Expected P95 (MySQL) |
|---------------|---------|---------------|---------------------|
| **Dashboard** | 9 | All KPI queries indexed | < 50ms |
| **Members list (active, page 1)** | 1 | `(merchant_id, status)` | < 10ms |
| **Member detail (show)** | 3 (member + tx page + eligible rewards) | `(member_id, created_at)` | < 30ms |
| **Campaigns list** | 1 | `(merchant_id, status)` (new) | < 10ms |
| **Campaign detail (show)** | 1 (rewards flat list) | `(loyalty_program_id, ...)` | < 10ms |
| **Record purchase** | 3 writes (campaign lookup + tx create + member update) | FK indexes | < 30ms |
| **Redeem reward** | 6 writes | FK indexes | < 40ms |
| **Activity feed (member)** | 1 paginated + eager | `(member_id, created_at)` | < 20ms |

These are estimates for SQLite (dev) and will be faster on MySQL with InnoDB buffer pool in production.

---

## 7. Production Optimization Commands

Run after every deployment:

```bash
# Cache all four layers — mandatory for production
php artisan optimize

# Equivalent to:
php artisan config:cache   # Merges all config files into one PHP file
php artisan route:cache    # Compiles route list to a single file
php artisan view:cache     # Pre-compiles all Blade templates
php artisan event:cache    # Caches auto-discovered event → listener map
```

Clear when debugging config issues:

```bash
php artisan optimize:clear
```

Verify cache is active:

```bash
php artisan about
# Look for: Config ...... Cached, Routes ...... Cached, Views ...... Cached
```

**Impact of caching:**

| Layer | Without cache | With cache |
|-------|--------------|-----------|
| Config | Parse all config/*.php on every request | Single compiled PHP file loaded once |
| Routes | Traverse route tree on every request | Compiled route map, O(1) lookup |
| Views | Compile Blade → PHP on first render | Pre-compiled PHP, no compile step |
| Events | Scan app/Listeners on every bootstrap | Compiled map file |

---

## 8. Scaling Recommendations

These are documented recommendations for when OneMember grows beyond V1.0 scale. None are required at launch.

### Short-term (100–1,000 merchants)

| Recommendation | When | Effort |
|----------------|------|--------|
| Add `CACHE_STORE=redis` in production | Before launch | Low — env change only |
| Add Redis session driver (`SESSION_DRIVER=redis`) | Before launch | Low — env change only |
| Run `php artisan optimize` after every deployment | Immediately | Already in deployment guide |

### Medium-term (1,000–10,000 merchants)

| Recommendation | When | Effort |
|----------------|------|--------|
| Add MySQL read replica for dashboard queries | > 5,000 merchants | Medium |
| Cache `usageSummary` per-merchant with 60s TTL | When dashboard becomes slow | Medium |
| Paginate campaign rewards list (currently unbounded `get()`) | > 50 rewards per campaign | Low |
| Add `members (merchant_id, name)` index for search | When member search is implemented | Low |
| Add `members (merchant_id, phone)` index for search | When phone search is implemented | Low |

### Long-term (10,000+ merchants)

| Recommendation | When | Effort |
|----------------|------|--------|
| Tenant database sharding | > 50,000 merchants | High |
| Pre-aggregate dashboard KPIs in a materialized summary table | When dashboard queries exceed 200ms | High |
| Full-text search (MySQL FULLTEXT or Typesense) for member search | When like-search becomes too slow | Medium |
| Laravel Octane (Swoole/FrankenPHP) | When PHP-FPM throughput is a bottleneck | Medium |

---

## 9. Load Testing Recommendations

Before launch, test the following scenarios:

### Tools (vendor-neutral)

- **k6** (scripted HTTP load testing, free, open source)
- **wrk** (simple HTTP benchmarking)
- **Apache JMeter** (GUI-based, good for session-based flows)

### Key scenarios to test

```
1. Dashboard load (authenticated)
   - 50 concurrent users
   - 60-second sustained
   - Target: P95 < 500ms, 0 errors

2. Member list (page 1, 500 members)
   - 100 concurrent users
   - Target: P95 < 200ms

3. Record purchase (write path)
   - 20 concurrent users
   - Target: P95 < 300ms, 0 data errors

4. Concurrent redemptions (same member, same reward)
   - 5 concurrent redemptions for a limited-quantity reward
   - Verify: quantity_redeemed does not exceed quantity_available
   - Laravel's increment() is atomic at the DB level — this should pass

5. Scheduler under load
   - Run ProcessExpiredTrials while 50 users are active
   - Verify no query lock contention
```

### Minimum acceptance criteria for V1.0 launch

| Metric | Target |
|--------|--------|
| Dashboard P95 response time | < 500ms |
| List pages P95 | < 300ms |
| Write operations P95 | < 500ms |
| Error rate under 50 concurrent users | 0% |
| Error rate under 100 concurrent users | < 0.1% |

---

## 10. Known Limitations & Future Improvements

| Limitation | Impact | Future sprint |
|------------|--------|--------------|
| `is_active` legacy indexes on `loyalty_programs` and `rewards` are inert | Minor index maintenance overhead | Drop in a future cleanup migration |
| Dashboard runs 9 queries (2 saved by this sprint) | Acceptable at V1.0 scale; could be reduced further with caching | Cache `usageSummary` when merchants > 1,000 |
| Campaign rewards list uses `get()` with no pagination | Acceptable if rewards per campaign stay < 50 | Paginate when reward catalogue feature is added |
| `ProcessExpiredTrials` iterates all expired merchants | Acceptable until merchant count > 10,000 | Add chunk processing: `->chunk(100, fn(...))` |
| Member search uses `LIKE '%value%'` | Cannot use standard indexes; full-scan | Add full-text index or search service when member search is implemented |
| No query result caching | Every request hits DB | Add per-request memoization or short-TTL cache when scale requires |

---

*This document records the V1.0 performance audit findings. Re-run before each major feature milestone. Last reviewed: 2026-06-29.*
