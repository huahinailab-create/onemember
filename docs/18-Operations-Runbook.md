# 18 — Operations Runbook

> **Version:** OneMember 1.0  
> **Last updated:** 2026-06-29  
> **Cross-reference:** [docs/17-Production-Deployment-Guide.md](17-Production-Deployment-Guide.md), [docs/16-Production-Security-Review.md](16-Production-Security-Review.md)  
> **Audience:** On-call engineers, operations team

---

## Overview

This runbook covers daily operational procedures, incident response, maintenance, troubleshooting, and recovery for OneMember running in production. All commands assume the application root is `/var/www/onemember` and the web server user is `www-data`. Adjust paths as needed for your deployment.

---

## 1. Daily Operational Checklist

Run every morning. Estimated time: 5–10 minutes.

```bash
# 1. Verify application is responding
curl -sf https://yourdomain.com/up | python3 -m json.tool

# 2. Check application log for errors since yesterday
grep -i "ERROR\|CRITICAL\|ALERT\|EMERGENCY" \
  /var/www/onemember/storage/logs/laravel.log | tail -50

# 3. Check security log for anomalies (failed login spikes)
grep "auth.login.failed" /var/www/onemember/storage/logs/security.log \
  | tail -20

# 4. Verify queue workers are running
supervisorctl status onemember-worker:*

# 5. Check for failed queue jobs
cd /var/www/onemember && php artisan queue:failed

# 6. Verify scheduler last ran (check cron log or syslog)
grep "schedule:run" /var/log/syslog | tail -5

# 7. Check disk usage
df -h /var/www/onemember/storage
```

**Expected outcomes:**
- `/up` returns `{"status":"ok",...}`
- No `CRITICAL` or `EMERGENCY` log entries
- Queue workers show `RUNNING`
- No new failed jobs overnight
- Cron fired within the last hour
- Disk usage below 80%

---

## 2. Weekly Maintenance Checklist

Run every Monday. Estimated time: 15–20 minutes.

```bash
# 1. Check SSL certificate expiry
echo | openssl s_client -servername yourdomain.com \
  -connect yourdomain.com:443 2>/dev/null \
  | openssl x509 -noout -dates

# 2. Review log file sizes
ls -lh /var/www/onemember/storage/logs/

# 3. Check database size and table row counts
mysql -u onemember_user -p onemember_production -e "
  SELECT table_name, table_rows,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
  FROM information_schema.tables
  WHERE table_schema = 'onemember_production'
  ORDER BY size_mb DESC;
"

# 4. Verify backup exists from yesterday
ls -lh /var/backups/onemember/ | grep "$(date -d yesterday +%Y%m%d)"

# 5. Check for available package security updates (review only — don't auto-apply)
cd /var/www/onemember && composer audit

# 6. Review failed queue jobs accumulated this week
php artisan queue:failed

# 7. Check server resources (CPU, memory)
top -bn1 | head -20
free -h
```

**Expected outcomes:**
- SSL certificate expiry > 30 days (alert if < 30 days)
- Log files rotating correctly (yesterday's log compressed)
- Database growing at expected rate
- At least one backup present from the past 24 hours
- `composer audit` returns 0 vulnerabilities
- Failed jobs reviewed and either retried or flushed

---

## 3. Monthly Maintenance Checklist

Run the first Monday of each month. Estimated time: 30–45 minutes.

```bash
# 1. Test database restore from backup (restore to test environment only)
gunzip < /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz \
  | mysql -u onemember_user -p onemember_test

# 2. Rotate application secrets review (not necessarily change — just verify)
# Confirm APP_KEY is not the same as in .env.example
grep APP_KEY /var/www/onemember/.env

# 3. Review security log for month — failed login patterns, unusual IPs
grep "auth.login.failed" /var/www/onemember/storage/logs/security.log \
  | awk '{print $NF}' | sort | uniq -c | sort -rn | head -20

# 4. Check PHP version and available updates
php --version

# 5. Review and prune failed queue jobs
php artisan queue:failed
php artisan queue:flush   # After reviewing — removes ALL failed jobs

# 6. Archive old log files off-server
# (copy logs older than 30 days to off-site storage, then remove locally)

# 7. Verify scheduler is still registered
php artisan schedule:list

# 8. Review disk usage trends
df -h
du -sh /var/www/onemember/storage/logs/
du -sh /var/www/onemember/storage/app/
du -sh /var/backups/onemember/
```

**Expected outcomes:**
- Database restore succeeded (test environment)
- `APP_KEY` is non-trivial (not empty, not example value)
- No sustained failed login campaign from a single IP
- PHP version is supported (within active support window)
- Disk usage trending flat or predictable
- Scheduler shows `ProcessExpiredTrials` at 01:00

---

## 4. Verifying Scheduler Health

The Laravel scheduler must run every minute via cron to execute registered commands.

### Check crontab entry

```bash
crontab -l -u www-data
# Expected:
# * * * * * cd /var/www/onemember && php artisan schedule:run >> /dev/null 2>&1
```

### Check what is scheduled

```bash
cd /var/www/onemember && php artisan schedule:list
```

Expected output:
```
0 1 * * *    php artisan subscriptions:process-expired-trials
```

### Verify the command last ran

```bash
# Check security log for trial processing events
grep "trial.expired\|subscription.status" \
  /var/www/onemember/storage/logs/security.log | tail -20

# Or check application log
grep "ProcessExpiredTrials" /var/www/onemember/storage/logs/laravel.log | tail -5
```

### Run manually (dry run)

```bash
cd /var/www/onemember && php artisan schedule:run --verbose
```

### Run the command directly (for debugging)

```bash
cd /var/www/onemember && php artisan subscriptions:process-expired-trials
```

---

## 5. Verifying Queue Worker Health

### Check worker process status

```bash
supervisorctl status onemember-worker:*
```

Expected:
```
onemember-worker:onemember-worker_00   RUNNING   pid 12345, uptime 0:12:34
onemember-worker:onemember-worker_01   RUNNING   pid 12346, uptime 0:12:34
```

### Check Supervisor logs

```bash
tail -50 /var/log/onemember/worker.log
```

### Check queue depth

```bash
cd /var/www/onemember

# Database queue — count pending jobs
php artisan db:table jobs

# Or run direct SQL
mysql -u onemember_user -p onemember_production \
  -e "SELECT queue, COUNT(*) as pending FROM jobs GROUP BY queue;"
```

### Check failed jobs

```bash
php artisan queue:failed
```

### Retry failed jobs

```bash
# Retry a specific failed job
php artisan queue:retry <job-id>

# Retry all failed jobs
php artisan queue:retry all
```

### Restart workers (after code deployment)

```bash
php artisan queue:restart
# Workers finish current job, then stop
# Supervisor automatically restarts them with new code
```

---

## 6. Inspecting Logs

### Application log

```bash
# Live tail
tail -f /var/www/onemember/storage/logs/laravel.log

# Show only errors
grep -E "ERROR|CRITICAL|ALERT|EMERGENCY" \
  /var/www/onemember/storage/logs/laravel.log

# Show last 100 lines
tail -100 /var/www/onemember/storage/logs/laravel.log
```

### Security log

```bash
# Live tail
tail -f /var/www/onemember/storage/logs/security.log

# Login failures in the last 24h
grep "auth.login.failed" /var/www/onemember/storage/logs/security.log \
  | grep "$(date +%Y-%m-%d)"

# All events for a specific user (by email)
grep "user@example.com" /var/www/onemember/storage/logs/security.log

# All events for a specific IP
grep "\"ip_address\":\"1.2.3.4\"" /var/www/onemember/storage/logs/security.log
```

### Log file locations

| Log | Path | Rotation | Retention |
|-----|------|---------|-----------|
| Application | `storage/logs/laravel.log` | Daily (Laravel driver) | 14 days (default) |
| Security | `storage/logs/security.log` | Daily (Laravel driver) | 90 days |
| Queue worker | `/var/log/onemember/worker.log` | Supervisor | Until manual rotation |
| Nginx access | `/var/log/nginx/access.log` | System logrotate | 14 days |
| Nginx error | `/var/log/nginx/error.log` | System logrotate | 14 days |

### Log permissions

```bash
# Laravel writes logs as www-data; readable by root and www-data group
ls -la /var/www/onemember/storage/logs/

# If log files are unreadable:
chown -R www-data:www-data /var/www/onemember/storage/logs/
chmod -R 664 /var/www/onemember/storage/logs/
chmod 775 /var/www/onemember/storage/logs/
```

---

## 7. Restarting Services Safely

### Restart queue workers (graceful — preferred)

```bash
# Signal workers to stop after current job, then Supervisor restarts them
php artisan queue:restart
# Wait 30–60 seconds for workers to cycle
supervisorctl status onemember-worker:*
```

### Restart Supervisor workers (forced)

```bash
supervisorctl restart onemember-worker:*
```

### Restart PHP-FPM (zero-downtime)

```bash
# Graceful reload — finishes in-flight requests first
sudo systemctl reload php8.3-fpm

# Verify
sudo systemctl status php8.3-fpm
```

### Restart Nginx (zero-downtime)

```bash
# Test config first — never reload a broken config
sudo nginx -t

# Graceful reload
sudo systemctl reload nginx

# Hard restart (only if reload fails)
sudo systemctl restart nginx
```

### Restart Redis

```bash
sudo systemctl restart redis
# Verify
redis-cli ping
# Expected: PONG
```

### Service restart order (when full restart is required)

1. Enable maintenance mode: `php artisan down`
2. Restart MySQL
3. Restart Redis
4. Restart PHP-FPM
5. Restart Nginx
6. Restart queue workers: `supervisorctl restart onemember-worker:*`
7. Verify health: `curl https://yourdomain.com/up`
8. Disable maintenance mode: `php artisan up`

---

## 8. Deployment Verification Steps

After every deployment, run in order:

```bash
cd /var/www/onemember

# 1. Confirm new code is present
git log --oneline -3

# 2. Health endpoint
curl -sf https://yourdomain.com/up | python3 -m json.tool

# 3. No pending migrations
php artisan migrate:status | grep -v "Ran"

# 4. Scheduler still registered
php artisan schedule:list

# 5. Queue workers running
supervisorctl status onemember-worker:*

# 6. No new errors in application log
tail -20 /var/www/onemember/storage/logs/laravel.log

# 7. Cache populated (config, routes, views)
php artisan about | grep -E "Cache|Environment|Version"
```

**Smoke test (manual, 2 minutes):**
1. Log in with a test merchant account
2. View members list — loads without error
3. View campaigns — loads without error
4. Check `/up` returns correct version

---

## 9. Incident Response Procedure

### Severity Levels

| Level | Name | Definition | Response Time | Examples |
|-------|------|-----------|--------------|---------|
| **P1** | Critical | Application completely unavailable; data loss risk | Immediate (< 15 min) | Server down, database inaccessible, login broken for all users |
| **P2** | High | Major feature broken; significant user impact | < 1 hour | Payments not processing, registration broken, emails not sending |
| **P3** | Medium | Feature degraded; workaround exists | < 4 hours | Scheduler missed a run, queue backed up, slow page loads |
| **P4** | Low | Minor issue; no immediate user impact | < 24 hours | Log errors with no visible symptom, a single failed job, cosmetic bug |

---

### P1 Response Workflow

```
1. ASSESS (2 minutes)
   - Is /up returning 200?
   - Can you SSH to the server?
   - Is the database responding?

2. COMMUNICATE
   - Notify Product Owner immediately
   - If > 5 minutes to fix: enable maintenance mode
     php artisan down --message="We'll be back shortly."

3. DIAGNOSE
   - tail -50 storage/logs/laravel.log
   - systemctl status nginx php8.3-fpm mysql redis
   - df -h (disk full?)
   - free -h (OOM?)

4. MITIGATE
   - Fix root cause OR rollback (see Section 12)

5. VERIFY
   - curl https://yourdomain.com/up
   - Run smoke test

6. RESTORE
   - php artisan up

7. DOCUMENT
   - Record: what happened, when, cause, fix, duration
   - Update runbook if a procedure was missing or wrong
```

---

### P2 Response Workflow

```
1. ASSESS
   - Identify affected feature
   - Check application log for errors

2. COMMUNICATE
   - Notify Product Owner within 30 minutes

3. DIAGNOSE
   - Is it code, config, or infrastructure?
   - Check: queue workers, scheduler, mail config

4. FIX OR DEFER
   - Minor config fix: apply immediately
   - Code change required: deploy hotfix after testing
   - Cannot fix quickly: document workaround for merchants

5. VERIFY
   - Test the affected feature manually

6. DOCUMENT
   - Record incident in team notes
```

---

### P3/P4 Response Workflow

```
1. Log the issue with details
2. Investigate during business hours
3. Fix in next available maintenance window
4. Verify fix
5. Add checklist item if this was a recurring issue
```

---

## 10. Backup Verification

### Verify backup exists

```bash
# List today's and yesterday's backups
ls -lh /var/backups/onemember/ | grep -E "$(date +%Y%m%d)|$(date -d yesterday +%Y%m%d)"

# Check file is not empty or corrupt
gunzip -t /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz
echo "Exit code: $?"   # 0 = OK
```

### Spot-check backup content

```bash
# Preview first 20 lines of a backup without fully extracting
gunzip -c /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz | head -20

# Count tables in backup
gunzip -c /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz \
  | grep "^CREATE TABLE" | wc -l
```

### Backup health checklist (weekly)

- [ ] Backup file exists for each of the last 7 days
- [ ] File sizes are consistent (large drop = data was deleted or script failed)
- [ ] `gunzip -t` exits 0 on the latest backup
- [ ] Off-site copy is current (see Section 10.1)

### 10.1 Off-site Backup

Store a copy of database backups outside the production server. Choose any approach:

```bash
# Option A: rsync to a separate server
rsync -az /var/backups/onemember/ backup-server:/backups/onemember/

# Option B: Copy to object storage (provider-agnostic — use rclone)
rclone copy /var/backups/onemember/ remote:onemember-backups/

# Option C: Manual download via SFTP once per week
sftp user@server:/var/backups/onemember/ .
```

---

## 11. Database Restore Procedure

> **Always test restores on a non-production environment first.**

### Restore to test environment (routine monthly test)

```bash
# 1. Identify the backup file
ls -lh /var/backups/onemember/

# 2. Restore to test database
gunzip -c /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz \
  | mysql -u onemember_user -p onemember_test

# 3. Verify row counts
mysql -u onemember_user -p onemember_test \
  -e "SELECT COUNT(*) FROM merchants; SELECT COUNT(*) FROM members;"
```

### Restore to production (emergency only)

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Create a safety snapshot of current production DB
mysqldump \
  --user=onemember_user \
  --password=<password> \
  --single-transaction \
  onemember_production | gzip > /var/backups/onemember/pre_restore_$(date +%Y%m%d_%H%M%S).sql.gz

# 3. Restore from backup
gunzip -c /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz \
  | mysql -u onemember_user -p onemember_production

# 4. Verify
mysql -u onemember_user -p onemember_production \
  -e "SELECT COUNT(*) FROM merchants; SELECT COUNT(*) FROM members;"

# 5. Run any migrations that post-date the backup
php artisan migrate --force

# 6. Clear cache
php artisan cache:clear

# 7. Verify health
curl https://yourdomain.com/up

# 8. Disable maintenance mode
php artisan up
```

---

## 12. Emergency Rollback Procedure

Use when a deployment causes a production error and must be reverted quickly.

```bash
cd /var/www/onemember

# 1. Enable maintenance mode immediately
php artisan down

# 2. Identify the last known-good commit
git log --oneline -10

# 3. Check out the previous release
git checkout <previous-commit-hash>

# 4. Reinstall dependencies for that version
composer install --no-dev --optimize-autoloader

# 5. Rebuild frontend assets (if they changed)
npm ci && npm run build

# 6. IMPORTANT: If the new deployment ran migrations, assess carefully
#    Option A: Rollback migrations (only if safe — additive migrations are usually reversible)
php artisan migrate:rollback
#    Option B: Restore database from pre-deployment backup (if migrations were destructive)
#    (see Section 11)

# 7. Re-cache
php artisan optimize

# 8. Restart queue workers
php artisan queue:restart

# 9. Verify health
curl https://yourdomain.com/up

# 10. Smoke test (login, view dashboard)

# 11. Disable maintenance mode
php artisan up

# 12. Document what caused the rollback
#     Fix the issue in a branch before re-deploying
```

**Rule:** Never re-deploy the broken code. Fix it in a branch, test locally, then deploy the fixed version.

---

## 13. Maintenance Mode Procedure

### Enable maintenance mode

```bash
# Basic maintenance mode (Laravel default page)
php artisan down

# With custom message
php artisan down --message="Scheduled maintenance — back in 30 minutes."

# With retry header (tells browsers to retry after N seconds)
php artisan down --retry=60

# With a secret bypass URL (allows you to access the app while it's down)
php artisan down --secret="your-secret-token"
# Access: https://yourdomain.com/your-secret-token
```

### Verify maintenance mode is active

```bash
# Should return HTTP 503
curl -I https://yourdomain.com/

# Health endpoint bypasses maintenance mode (intended for uptime monitors)
curl https://yourdomain.com/up
```

### Disable maintenance mode

```bash
php artisan up
# Verify
curl https://yourdomain.com/
```

### Deployment workflow with maintenance mode

```bash
php artisan down --retry=60
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
curl https://yourdomain.com/up   # verify before bringing up
php artisan up
```

---

## 14. Production Troubleshooting Guide

### Application returns 500 Internal Server Error

```bash
# 1. Check application log
tail -50 /var/www/onemember/storage/logs/laravel.log

# 2. Check Nginx error log
tail -50 /var/log/nginx/error.log

# 3. Check PHP-FPM log
tail -50 /var/log/php8.3-fpm.log

# 4. Ensure APP_DEBUG=false (never enable in production — check log instead)
grep APP_DEBUG /var/www/onemember/.env

# 5. Clear and rebuild caches
php artisan optimize:clear && php artisan optimize
```

### Application returns 419 (CSRF Token Mismatch)

```bash
# Session driver issue — check session config
php artisan config:show session

# Verify session table exists
php artisan db:table sessions

# Clear expired sessions
php artisan db:table sessions --prune

# Check SESSION_DOMAIN in .env (must match the domain in APP_URL)
grep -E "SESSION_DOMAIN|APP_URL" /var/www/onemember/.env
```

### Login redirects to /login immediately after logging in

```bash
# Session not persisting — likely a cookie domain mismatch or HTTPS issue
grep -E "APP_URL|SESSION_SECURE_COOKIE|SESSION_DOMAIN" /var/www/onemember/.env

# Ensure APP_URL uses https:// in production
# Ensure SESSION_SECURE_COOKIE=true only when HTTPS is active
```

### Emails not being sent

```bash
# Check queue workers are running
supervisorctl status onemember-worker:*

# Check failed jobs (failed mail sends land here)
php artisan queue:failed

# Test mail config directly
php artisan tinker
>>> Mail::raw('test', function($m) { $m->to('test@example.com')->subject('test'); });

# Check mail config
php artisan config:show mail
```

### Disk space running out

```bash
# Find largest directories
du -sh /var/www/onemember/storage/*
du -sh /var/backups/onemember/

# Purge old backups (keeping 30 days)
find /var/backups/onemember/ -name "db_*.sql.gz" -mtime +30 -delete

# Compress old log files manually if logrotate hasn't run
gzip /var/www/onemember/storage/logs/laravel-*.log

# Remove old cache files
php artisan cache:clear
```

### Slow page loads

```bash
# Check if cache is populated
php artisan about | grep Cache

# Re-cache if missing
php artisan optimize

# Check MySQL slow query log (if enabled)
tail -50 /var/log/mysql/slow.log

# Check if Redis is running (cache misses = slower)
redis-cli ping
redis-cli info stats | grep "keyspace_hits\|keyspace_misses"
```

### Queue jobs backed up (large backlog)

```bash
# Check queue depth
mysql -u onemember_user -p onemember_production \
  -e "SELECT queue, COUNT(*) FROM jobs GROUP BY queue;"

# Add more workers temporarily
supervisorctl -c /etc/supervisor/conf.d/onemember-worker.conf stop all
# Edit numprocs in /etc/supervisor/conf.d/onemember-worker.conf (e.g., 4)
supervisorctl reread && supervisorctl update
supervisorctl start onemember-worker:*
```

---

## 15. Merchant Support Workflow

When a merchant reports an issue:

### Step 1 — Gather context

Collect from the merchant:
- Their email address / account name
- Exact steps taken
- Time the issue occurred
- Error message (if any)
- Browser and device

### Step 2 — Identify the merchant

```bash
cd /var/www/onemember && php artisan tinker

# Find merchant by email
$user = \App\Models\User::where('email', 'merchant@example.com')->first();
$merchant = $user->merchant;

echo "Merchant ID: " . $merchant->id;
echo "Status: " . $merchant->status->value;
echo "Plan: " . $merchant->plan;
echo "Trial ends: " . $merchant->trial_ends_at;
echo "Onboarding: " . $merchant->onboarding_completed_at;
```

### Step 3 — Check security log for their activity

```bash
grep "merchant@example.com" /var/www/onemember/storage/logs/security.log \
  | tail -30
```

### Step 4 — Check application log for errors at that time

```bash
# Filter by approximate timestamp
grep "2026-06-29 14:" /var/www/onemember/storage/logs/laravel.log \
  | grep -i "error\|exception" | tail -20
```

### Step 5 — Reproduce in test environment

- Never debug on production data unless unavoidable
- Use `php artisan tinker` to inspect state, not to modify it
- If data correction is needed, document the change and confirm with Product Owner first

### Step 6 — Escalate if needed

See Section 16.

---

## 16. Escalation Procedure

| Situation | Action |
|-----------|--------|
| P1 incident | Notify Product Owner immediately by phone/message |
| P2 incident affecting billing | Notify Product Owner within 30 minutes |
| Data integrity concern | STOP all writes if possible; notify Product Owner before taking action |
| Security breach suspected | Enable maintenance mode; notify Product Owner; preserve logs (do not delete anything) |
| Cannot reproduce reported merchant issue | Escalate to CTO (Solution Architect) for architectural guidance |
| Requires code change in production | Get Product Owner approval; test in staging; deploy with standard procedure |

### Escalation contacts

| Role | Contact | Available |
|------|---------|-----------|
| Product Owner | Huahin — huahin.ailab@gmail.com | Business hours |
| CTO / Solution Architect | ChatGPT | On-demand |
| Lead Developer | Claude Code | On-demand |

**Incident log format:**

```
Date/Time:
Severity:
Summary:
Impact:
Timeline:
  [HH:MM] — event
  [HH:MM] — action taken
Root Cause:
Fix Applied:
Prevention:
```

---

## Monitoring Recommendations

> These are documentation recommendations. No third-party service is integrated into the application code.

### Recommended monitoring stack (vendor-neutral)

| Category | What to Monitor | Recommended Tools |
|----------|----------------|-------------------|
| Uptime | `GET /up` every 60s, expect `{"status":"ok"}` + HTTP 200 | UptimeRobot, BetterStack, StatusCake |
| Error tracking | Unhandled PHP exceptions with stack trace | Sentry (Laravel package available), Flare |
| Application logs | Forward `storage/logs/laravel.log` to centralised service | Papertrail, Logtail, Grafana Loki |
| Security logs | Forward `storage/logs/security.log`; alert on `auth.login.failed` spikes | Same log service with alerting rules |
| Server metrics | CPU, RAM, disk, network | Netdata, Grafana + Prometheus, Datadog |
| Database | MySQL connections, slow queries, replication lag | Percona Monitoring, built-in MySQL metrics |
| Queue health | Jobs pending, jobs failed, worker process count | Custom cron check or Supervisor alerts |
| Scheduler | Verify `ProcessExpiredTrials` fired daily | Cronitor, Healthchecks.io (free tier) |
| SSL expiry | Alert when certificate < 30 days from expiry | Certbot auto-renew + uptime monitor SSL check |
| Disk usage | Alert at 75% and 90% | Server metrics tool |

### Uptime monitor configuration

```
URL:              https://yourdomain.com/up
Method:           GET
Interval:         60 seconds
Expected status:  200
Expected body:    "status":"ok"
Alert after:      2 consecutive failures
Alert channels:   Email, Slack (or SMS for P1)
```

### Health endpoint response reference

```json
{
  "status":      "ok",
  "app":         "OneMember",
  "environment": "production",
  "timestamp":   "2026-06-29T01:10:49+00:00",
  "version":     "1.0"
}
```

The endpoint is unauthenticated, returns no sensitive data, and is excluded from maintenance mode detection (it responds 200 even when `php artisan down` is active because it's served before middleware).

---

## Log Retention & Archiving

### Current configuration

| Log | Driver | Rotation | Default Retention |
|-----|--------|---------|-------------------|
| `laravel.log` | `daily` | Midnight | 14 days (env: `LOG_DAILY_DAYS`) |
| `security.log` | `daily` | Midnight | 90 days (hardcoded in `config/logging.php`) |

### Recommended production logrotate config

Create `/etc/logrotate.d/onemember`:

```
/var/www/onemember/storage/logs/*.log {
    daily
    missingok
    rotate 90
    compress
    delaycompress
    notifempty
    create 0664 www-data www-data
    sharedscripts
    postrotate
        # If using PHP-FPM, it does not need signalling for log files
        true
    endscript
}
```

### Archiving security logs

Security logs must be retained for 90 days minimum. Archive logs older than 90 days to off-site storage before deletion:

```bash
# Compress and move logs older than 90 days
find /var/www/onemember/storage/logs/ -name "security-*.log" -mtime +90 \
  | while read f; do
      gzip "$f"
      mv "${f}.gz" /var/backups/onemember/logs/
  done
```

### Log file permissions

```bash
# Logs must be readable by www-data and root only
chmod 664 /var/www/onemember/storage/logs/*.log
chown www-data:www-data /var/www/onemember/storage/logs/*.log
```

---

## Production Commands Reference

| Command | Purpose |
|---------|---------|
| `php artisan optimize` | Cache config, routes, views, events (run after every deploy) |
| `php artisan optimize:clear` | Clear all caches (run when debugging config issues) |
| `php artisan down` | Enable maintenance mode |
| `php artisan up` | Disable maintenance mode |
| `php artisan migrate --force` | Run pending migrations (production — skips confirmation) |
| `php artisan migrate:status` | Show migration run status |
| `php artisan migrate:rollback` | Roll back last migration batch |
| `php artisan queue:work --tries=3` | Start queue worker |
| `php artisan queue:restart` | Signal workers to restart after deployment |
| `php artisan queue:failed` | List failed jobs |
| `php artisan queue:retry all` | Retry all failed jobs |
| `php artisan queue:flush` | Delete ALL failed jobs |
| `php artisan schedule:run` | Run scheduled tasks (called by cron every minute) |
| `php artisan schedule:list` | List registered scheduled commands |
| `php artisan cache:clear` | Clear application cache |
| `php artisan config:clear` | Clear config cache |
| `php artisan config:cache` | Rebuild config cache |
| `php artisan route:cache` | Rebuild route cache |
| `php artisan view:cache` | Rebuild view cache |
| `php artisan event:cache` | Rebuild event cache |
| `php artisan key:generate` | Generate new APP_KEY |
| `php artisan about` | Show app overview: env, cache status, version |
| `php artisan route:list` | Show all registered routes with middleware |
| `php artisan tinker` | Interactive REPL for querying models |
| `tail -f storage/logs/laravel.log` | Live tail application log |
| `tail -f storage/logs/security.log` | Live tail security log |
| `supervisorctl status onemember-worker:*` | Check queue worker status |
| `supervisorctl restart onemember-worker:*` | Force restart queue workers |
| `php artisan queue:restart` | Graceful worker restart (preferred) |

---

*This runbook is a living document. Update it whenever a procedure changes, a new issue is encountered, or an escalation path changes. Last reviewed: 2026-06-29.*
