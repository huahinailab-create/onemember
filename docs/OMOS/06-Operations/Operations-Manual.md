# OneMember Operations Manual

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-05 |
| **Author** | Claude Fable 5 |
| **Audience** | Any engineer operating OneMember, with no prior knowledge assumed |
| **Deep-dive references** | [17-Production-Deployment-Guide](../../17-Production-Deployment-Guide.md) · [18-Operations-Runbook](../../18-Operations-Runbook.md) · [19-Backup-and-Disaster-Recovery](../../19-Backup-and-Disaster-Recovery.md) · [16-Production-Security-Review](../../16-Production-Security-Review.md) · [Scalability-Review-2026-07](../10-Architecture/Scalability-Review-2026-07.md) |

**How to use this manual:** each section is self-sufficient for the common case and links to the deep-dive doc for edge cases. Commands assume Ubuntu 24.04, PHP 8.3, app root `/var/www/onemember`, service user `www-data`. Replace paths to match your host.

---

## 0. System Map (read first)

```
onemember.co          → corporate marketing site (Thai-first, public)
app.onemember.co      → merchant application + /admin (platform staff)
wallet.onemember.co   → customer wallet (Phase 2 — behind FEATURE_WALLET)

One Laravel 13 monolith (PHP 8.3+) · One MySQL 8 database (multi-tenant by merchant_id)
Redis (prod): cache + queue + session (ADR-009) · Queue workers via Horizon
Scheduler: cron → php artisan schedule:run every minute on ONE node only
Email: event-driven, queued, never sent from controllers (CTO-003)
```

Golden rules (never break; see EXECUTE.md "never do" table):
1. Never disable email verification, expose devtools in production, or hardcode secrets.
2. Never run destructive artisan/SQL against production without a same-day backup you have verified.
3. Every schema change ships as a migration with a tested `down()`.
4. Deploys happen only with Product Owner approval (CEO-007).

---

## 1. Production Deployment

Full detail: [doc 17](../../17-Production-Deployment-Guide.md). Standard release to an existing server:

```bash
cd /var/www/onemember
php artisan down --retry=30                # maintenance mode
git fetch origin && git checkout <release-tag>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force               # additive-only; see §12 before any risky migration
php artisan config:cache route:cache view:cache event:cache
php artisan queue:restart                 # workers pick up new code
php artisan up
```

Post-deploy verification (2 minutes, always):
```bash
curl -fsS https://onemember.co/ > /dev/null && echo corp-ok
curl -fsS https://app.onemember.co/up && echo app-ok       # health endpoint
php artisan queue:monitor default,mail --max=100
tail -n 50 storage/logs/laravel.log | grep -i error || echo log-clean
```
Rollback: `git checkout <previous-tag>` + `composer install` + caches + `php artisan migrate:rollback --step=N` **only** for this release's migrations (check `php artisan migrate:status` first).

### 1a. "New route / admin page not visible after deploy" (deployment gotcha)

All routes live in domain groups (`Route::domain(config('domains.app'))…`) and production runs `route:cache` + `config:cache`. A new page (e.g. `/admin/control-room`) can appear missing after deploy for one of these reasons — check in order:

1. **Stale route cache.** The deploy must rebuild caches *after* the new code is the `current` release. Correct order (Forge deploy script, after `git pull`/symlink):
   ```bash
   php artisan optimize:clear      # drop old route/config/view caches FIRST
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```
   If `optimize:clear` is skipped, an old cache from the previous release can persist and hide new routes.
2. **Opcache holding old files.** Reload PHP-FPM so opcache picks up new code: `sudo systemctl reload php8.3-fpm` (Forge does this automatically on deploy; a manual `artisan` run outside deploy does not).
3. **Wrong `APP_DOMAIN`.** Domain-group routes only match the configured host. If `APP_DOMAIN` is unset/misspelt on the server, `/admin/*` 404s. Verify: `php artisan tinker --execute="echo config('domains.app');"` → must be `app.onemember.co`. After any `.env` change: `config:clear` then `config:cache`.
4. **Verify the route actually shipped:** `php artisan route:list | grep control-room` (should print the route). CI guards this via `DeploymentIntegrityTest` — a missing critical route fails the build before deploy.

**One-liner post-deploy check:** `php artisan route:list | grep -E "control-room|go-live" && curl -fsS https://app.onemember.co/up`.

**Ready-to-paste Forge deploy script:** [forge-deploy-script.sh](./forge-deploy-script.sh) — implements the correct order (clear → rebuild → FPM reload) and fails the deploy loudly if the critical routes are missing.


## 2. New Server Setup

Order matters. Full package lists in doc 17 §1–2.

1. **Base:** Ubuntu 24.04 LTS, `ufw` allow 22/80/443 only, unattended-upgrades on, NTP on (Asia/Bangkok is display-only; servers run UTC).
2. **Stack:** Nginx, PHP 8.3-FPM + extensions (doc 17 §2), MySQL 8 (or managed DB — preferred, BD-13), Redis 7 (§5), Node 20 (build only), certbot.
3. **App:**
   ```bash
   sudo mkdir -p /var/www/onemember && sudo chown $USER /var/www/onemember
   git clone <repo> /var/www/onemember && cd /var/www/onemember
   cp .env.example .env   # fill per doc 17 §3 — then: php artisan key:generate
   composer install --no-dev --optimize-autoloader && npm ci && npm run build
   sudo chown -R www-data:www-data storage bootstrap/cache
   php artisan migrate --force && php artisan storage:link
   ```
4. **Nginx:** three server blocks (corporate, app, wallet) → same app root; TLS via certbot for all three names; HSTS comes from app middleware, don't duplicate.
5. **Services:** queue workers (§6), scheduler cron (§7), log shipping + monitoring agents (§4, §18).
6. **First admin:** `php artisan tinker` → `User::where('email','ops@onemember.co')->update(['is_admin'=>true]);` (DECISION-068 — no UI for this, by design).
7. Run the Production Checklist (§20) before pointing DNS.

## 3. Disaster Recovery

Targets (proposed, pending BD-12): **RPO ≤ 15 min, RTO ≤ 4 h.** Full detail: [doc 19](../../19-Backup-and-Disaster-Recovery.md).

**Scenario A — app server lost:** build new server (§2, ~60–90 min), restore `.env` from secret store, point DNS/LB. No data loss (DB/Redis/files are external at scale; if files were local, restore from object-storage sync or last backup).

**Scenario B — database lost/corrupted:**
1. Freeze writes: `php artisan down`.
2. Managed DB: point-in-time-restore to a new instance at the last known-good minute. Self-hosted: restore latest dump + binlogs (doc 19 §4).
3. Update `DB_HOST` in `.env`, `php artisan config:cache`, verify (§12 checks), `php artisan up`.
4. Write an incident report (§14 template) — always.

**Scenario C — bad deploy/migration:** §1 rollback; if data was mutated, prefer PITR to a side instance and reconcile — never hand-edit production rows without a captured before-state.

**Scenario D — Redis lost:** sessions drop (users re-login), queued jobs in flight may be lost — reconcile via `failed_jobs` + email log; cache rebuilds itself. Redis is not the system of record; nothing in Redis is authoritative.

## 4. Monitoring

Baseline (must exist before go-live; Scalability §1.17):

| Check | Tool | Alert threshold |
|---|---|---|
| Uptime, all 3 domains + `/up` | external pinger | 2 consecutive failures |
| Error tracking | Sentry/Bugsnag (BD-13) | new exception class in prod |
| Queue depth / failed jobs | Horizon + `queue:monitor` | depth > 500 or any failed > 10/h |
| Scheduler heartbeat | `schedule:run` log line age | > 5 min |
| DB: slow queries, connections, disk | provider dashboards | slow > 1 s sustained; disk > 75 % (matches admin health widget thresholds) |
| Redis memory/evictions | provider/`INFO` | evictions > 0 |
| SSL expiry | weekly job (§9) | < 21 days |
| SMS OTP spend (Phase 2) | provider budget alarm | daily budget ×1.5 |

The in-app `/admin` dashboard health widget (database/email/queue/storage/scheduler/backup) is a human snapshot, not an alerting system — never rely on it alone.

## 5. Redis Setup (ADR-009)

Managed Redis preferred (BD-13). Self-hosted:
```bash
sudo apt install -y redis-server
# /etc/redis/redis.conf:
#   requirepass <strong-pass>   bind 127.0.0.1   maxmemory 1gb
#   maxmemory-policy allkeys-lru        # cache-safe; queues use no-eviction DB
sudo systemctl enable --now redis-server
```
`.env` (production only — dev/test stay on database/array drivers):
```
CACHE_STORE=redis  QUEUE_CONNECTION=redis  SESSION_DRIVER=redis
REDIS_HOST=... REDIS_PASSWORD=... REDIS_PORT=6379
```
⚠️ If one Redis serves cache **and** queues, use separate logical DBs (`REDIS_CACHE_DB=1`) and never `allkeys-lru` on the queue DB — evicting a queued job loses it. Verify after cutover: `php artisan queue:work --once` processes a test job; login session survives a page reload.

## 6. Queue Workers

With Horizon (SCALE-001 onward): `php artisan horizon` under systemd; queues `default`, `mail`, `passes`, `exports`. Pre-Horizon minimal unit:

```ini
# /etc/systemd/system/onemember-worker@.service
[Service]
User=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/onemember/artisan queue:work --queue=default,mail --sleep=3 --tries=3 --max-time=3600
```
`sudo systemctl enable --now onemember-worker@{1,2}`

Operations: after every deploy `php artisan queue:restart`; inspect failures `php artisan queue:failed`, retry `queue:retry <id|all>`, investigate root cause before retrying mail jobs (double-send risk is low — mailables are queued once — but check the email log table first).

## 7. Scheduler

One node only: `* * * * * cd /var/www/onemember && php artisan schedule:run >> /var/log/onemember-schedule.log 2>&1` (crontab of `www-data`).

Registered jobs (routes/console.php — verify with `php artisan schedule:list`):

| Time (server) | Command | What breaks if it stops |
|---|---|---|
| 01:00 | subscriptions:process-expired-trials | expired trials keep pro features |
| 02:00 | loyalty:process-point-expiry | points never expire |
| 03:00 | backup:verify | silent backup failures |
| 08:00 | loyalty:process-birthday-rewards | no birthday bonuses/emails |
| 09:00 | subscriptions trial reminders | no trial-ending emails |
| 09:30 | loyalty:send-winback-alerts | no win-back digests |

All jobs are idempotent (safe to re-run after downtime); after an outage spanning a run time, run the missed command manually once.

## 8. Daily Operations (10 min)

Doc 18 §1 script, summarised: health endpoints green → error log since yesterday → failed jobs = 0 → scheduler heartbeat fresh → disk < 75 % → security log anomalies (failed-login spikes) → admin dashboard glance. Anything red → §14 incident triage.

## 9. Weekly Operations (30 min)

Doc 18 §2: SSL expiry ≥ 21 d · log sizes/rotation · DB size + top table growth (watch `transactions`) · backup existed every day (§11) · security updates review (apply in a window, not auto) · failed-jobs weekly review · CPU/RAM trend vs last week · **restore-drill quarter check** (§12 — book it if a quarter is ending).

## 10. Monthly Maintenance (2 h window)

Doc 18 §3: test restore to staging (§12) · secrets review (APP_KEY unchanged, no `.env` drift between nodes) · month security-log review · PHP/OS patch level · prune failed jobs + old exports · dependency audit `composer audit` / `npm audit` · review monitoring thresholds vs actual traffic · capacity check against Scalability bottleneck register (any B-item trigger approaching?).

## 11. Backup Verification

Automated: `backup:verify` runs 03:00 daily (checks yesterday's dump exists at `BACKUP_PATH`, logs pass/fail; admin health widget shows state). Manual spot-check weekly:
```bash
ls -lh $BACKUP_PATH | tail -3            # exists, plausible size (compare last week)
gunzip -t <latest>.sql.gz && echo intact
zcat <latest>.sql.gz | head -20          # header sanity: correct DB name, recent date
```
A backup that has never been restored is a hope, not a backup — §12 quarterly drill is mandatory.

## 12. Restore Procedures

**To staging (drill, quarterly):**
```bash
mysql -e "CREATE DATABASE onemember_restore"
zcat <backup>.sql.gz | mysql onemember_restore
mysql onemember_restore -e "SELECT COUNT(*) FROM merchants; SELECT COUNT(*) FROM transactions;
  SELECT MAX(created_at) FROM transactions;"    # counts sane, max age ≈ backup time
```
Point a staging `.env` at it, log in, open a merchant dashboard + one member. Record duration → this is your evidence for the RTO target. **To production:** only during Scenario B (§3), always to a *new* instance/schema, never over the top of the corrupted one.

## 13. Security Checklist

Recurring (monthly + before every release; deep dive doc 16):
- `APP_DEBUG=false`, `APP_ENV=production`; devtools routes absent in prod (`php artisan route:list | grep -i dev` → empty)
- No secrets in repo (`git grep -iE "AKIA|BEGIN.*PRIVATE|password.*="` sanity), `.env` perms 600
- Email verification enforced; admin list reviewed (`User::where('is_admin',true)->get()` — every entry justified)
- Security headers live: `curl -sI https://app.onemember.co | grep -iE "strict-transport|content-security"`
- Stripe webhook signature test still green; rate limiters answering 429 on abuse paths
- Dependency CVEs: `composer audit`, `npm audit --production`
- Backups encrypted at rest; object storage buckets not public-listable
- PDPA: consent/audit tables append-only, PII masking in logs (Phase 2 §wallet)

## 14. Incident Response

**Severities:** SEV1 total outage/data loss · SEV2 core flow broken (login, purchase recording, joins) · SEV3 degraded (slow, one feature) · SEV4 cosmetic.

**Process (all SEVs):**
1. **Stabilise** — prefer reversible moves: `php artisan down`, rollback (§1), disable a feature flag. Never debug live under SEV1 pressure.
2. **Communicate** — SEV1/2: notify Product Owner immediately; status note to affected merchants if > 30 min.
3. **Diagnose** — error tracker first, then `storage/logs/laravel.log`, Horizon failed jobs, DB slow log, `journalctl -u nginx -u php8.3-fpm`.
4. **Resolve & verify** — §1 post-deploy checks + the specific broken flow.
5. **Post-mortem within 48 h** (blameless, file in `docs/OMOS/06-Operations/incidents/YYYY-MM-DD-title.md`): timeline · impact (merchants/members/minutes) · root cause · what limited/worsened it · action items with owners. Recurring causes feed the Engineering Backlog.

Common quick diagnoses: 500s after deploy → forgot `config:cache` or bad env; emails not arriving → workers dead (`queue:restart`) or mail provider; scheduler silence → crontab user wrong; "CSRF token mismatch" storm → session driver/Redis down.

## 15. Scaling Checklist (when load grows)

Work the [bottleneck register](../10-Architecture/Scalability-Review-2026-07.md) in order — triggers are listed per item. Sequence: Redis (done at SCALE-001) → add 2nd app node (needs §5 sessions external + object storage; add LB health checks) → dedicated worker node → DB read replica (`read` connection for admin/exports/analytics) → rollup tables → then and only then consider partitioning/extraction (§1.19 seams). Before each step: capture a week of baseline metrics so you can prove the step helped.

## 16. Go-Live Checklist (first production launch)

Cross-check [11-Launch-Checklist](../../11-Launch-Checklist.md); operational gates:
- [ ] SCALE-001 complete (Redis, indexes, batching, object storage, rate limits)
- [ ] Monitoring §4 all firing on staging tests (kill a worker → alert arrives)
- [ ] Backups running 7 straight days **and** one restore drill done (§12)
- [ ] Security checklist §13 clean; PDPA pages live; BD-07 legal sign-off if wallet enabled
- [ ] DNS TTL lowered 24 h ahead; rollback plan written for the launch deploy
- [ ] First admin created; on-call owner + escalation path named for week 1
- [ ] Product Owner go/no-go recorded (CEO-007 — deployment requires explicit PO approval)

## 17. New Developer Onboarding (day 1 → productive)

1. **Read, in order:** `docs/OMOS/EXECUTE.md` (operating protocol) → `CurrentSprint.md` → `02-Product/Product-Bible.md` + `Glossary.md` → `08-Product-Decisions.md` (skim decision titles) → `12-ADR/` (all, short) → this manual §0.
2. **Local setup:** PHP 8.3 + Composer + Node 20 → `cp .env.example .env` (SQLite default works) → `composer install && npm install` → `php artisan key:generate && php artisan migrate` → `npm run build` → `php artisan test` (**must be green before any work — currently 521 tests**) → `php artisan serve` with `/etc/hosts` entries for the three domains if testing routing.
3. **Know the invariants:** merchant_id scoping on every query (CTO-005) · no email from controllers (CTO-003) · Bootstrap 5 only (ADR-005) · Campaign≡LoyaltyProgram (ADR-007) · decisions live in docs before code (Decision Log rule).
4. **First contribution:** pick a 🟢 item from `Engineering-Backlog.md`, follow §19 release flow. Sprint work only via OMOS ("Continue OMOS" protocol).

## 18. Coding Standards (operational summary)

Authoritative: [06-Coding-Standards](../../06-Coding-Standards.md) + `11-Standards/`. The rules reviewers actually reject on:
- PSR-12; typed signatures; FormRequests for all validation; Eloquent (no raw SQL without justification + binding)
- Controllers thin — domain behaviour in Services; cross-cutting via Events/Listeners
- Every feature lands with Feature tests; `php artisan test` zero failures is non-negotiable (CTO-006); never skip/delete a failing test
- Translations: every user-facing string via `__()`, en+th (completeness is test-enforced)
- Migrations additive with working `down()`; no `--no-verify` commits; commit style `type(scope): summary`
- New Blade: no inline styles/scripts (SEC-002 direction; mandatory in wallet views)

## 19. Release Checklist (every release)

- [ ] All sprint acceptance criteria met; scope matches the sprint spec (no creep)
- [ ] `php artisan test` — zero failures, locally and in CI
- [ ] `npm run build` clean if frontend touched
- [ ] New migrations: `migrate` + `migrate:rollback` cycled on staging (DX-001)
- [ ] Decision log / ADR updated if any decision was made; CurrentSprint.md updated
- [ ] CHANGELOG entry; release tagged `vX.Y.Z`
- [ ] PO approval to deploy recorded (CEO-007) → §1 procedure → post-deploy verification
- [ ] Monitor error tracker for 30 min post-deploy; announce done

## 20. Production Checklist (steady-state audit — run monthly)

> **Automated pre-check (OPS-001):** run `php artisan onemember:go-live-check` or open `/admin/go-live` for an instant pass/fail on app key, debug, mail, queue, storage link, backup path, terms version, admin user, plans, scheduler, and feature flags. Critical failures block go-live; warnings are advisory.


- [ ] §4 monitors all green and actually alerting (test one)
- [ ] §7 all scheduled jobs ran in the last 24 h (`schedule:list` vs logs)
- [ ] §11 yesterday's backup verified; §12 drill within last quarter
- [ ] §13 security checklist passed this month
- [ ] Failed jobs = 0; queue p95 wait < 30 s
- [ ] Disk < 75 %, DB growth vs capacity plan reviewed (Scalability triggers)
- [ ] Open incidents have owners; post-mortems filed within 48 h
- [ ] OMOS current: CurrentSprint, decision log, Engineering Backlog reflect reality
