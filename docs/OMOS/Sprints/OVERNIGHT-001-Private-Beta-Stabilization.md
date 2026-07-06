# OVERNIGHT-001 — Private Beta Stabilization & Bug Hunt

| Field | Value |
|---|---|
| **Status** | ✅ Complete — 2026-07-07 |
| **Type** | Stabilization / QA (no new features) |
| **Developer** | Claude Fable 5 |

## Result
Codebase entered the hunt healthy. **One real bug found and fixed** (commerce products table overflowed the viewport at 375px). Everything else audited clean; the work hardened it with regression tests.

## Sub-sprints (one commit each)
| Priority | Commit | Outcome |
|---|---|---|
| P1 Deploy verification | `3e432f4` | Route-integrity test + deploy-troubleshooting docs. Root cause of "route invisible after deploy" is operational (cache/opcache/APP_DOMAIN), not code. |
| P2 Smoke tests | `387d21c` | 11 end-to-end smoke tests; beta walkable end to end. |
| P3 Broken links | `2dd9b49` | No broken links in 188 route() refs; added CI guard. |
| P4 Mobile | `cd9886e` | Fixed products-table overflow at 375px + wrapped 2 admin tables. |
| P5 Error handling | `e0d5cfa` | Edge cases all degrade safely; 13-case regression test. |
| P6 Docs | this commit | CHANGELOG, CurrentSprint, sprint record. |

## Tests
697 passing / 1,584 assertions (+13 from 684).

## What to check tomorrow (operator)
1. On the deploy target: `php artisan route:list | grep -E "control-room|go-live"` after deploy, and confirm the deploy script runs `optimize:clear` → `config:cache` → `route:cache` **after** the release symlink flips, then reloads PHP-FPM.
2. Verify `APP_DOMAIN=app.onemember.co` in the server `.env` (domain-group routes 404 otherwise).
3. Run `php artisan onemember:go-live-check` on the target and clear any criticals (storage link, mail, queue).
4. Manual pass on a real phone (iOS Safari) of: dashboard, members, counter, launch-kit print, commerce products, storefront order.
