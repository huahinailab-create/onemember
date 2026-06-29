# 19 — Backup and Disaster Recovery

> **Version:** OneMember 1.0  
> **Last updated:** 2026-06-29  
> **Cross-reference:** [docs/17-Production-Deployment-Guide.md](17-Production-Deployment-Guide.md), [docs/18-Operations-Runbook.md](18-Operations-Runbook.md)  
> **DECISION:** DECISION-051

---

## Executive Summary

This document defines the backup strategy, recovery objectives, and disaster recovery procedures for OneMember V1.0. All database state, uploaded files, and environment configuration must be protected and recoverable within defined time objectives.

**Recovery objectives for V1.0:**

| Objective | Target |
|-----------|--------|
| Recovery Time Objective (RTO) | < 4 hours |
| Recovery Point Objective (RPO) | < 24 hours |

These are achievable with daily backups and the procedures documented here.

---

## 1. Backup Strategy

OneMember uses a **layered backup strategy** with three categories of data:

| Category | What | How | Frequency |
|----------|------|-----|-----------|
| **Database** | All merchant, member, campaign, transaction, and redemption data | `mysqldump` → compressed `.sql.gz` | Daily |
| **File storage** | Merchant-uploaded files (logo images) | `rsync` or object storage sync | Daily |
| **Environment config** | `.env`, Nginx config, Supervisor config, crontab | Secure off-site copy | After every change |

### What is NOT backed up separately

| Item | Reason |
|------|--------|
| Application code | In git — recoverable by `git clone` |
| Composer packages | Recoverable by `composer install` |
| npm packages / built assets | Recoverable by `npm ci && npm run build` |
| Laravel framework | Part of Composer packages |
| Log files | Not backed up — logs are operational, not data |

---

## 2. Backup Frequency

| Backup Type | Schedule | Timing |
|-------------|---------|--------|
| Full database dump | Daily | 02:00 server time |
| Backup verification | Daily | 03:00 server time (automated via `php artisan backup:verify`) |
| File storage sync | Daily | 02:30 server time |
| Weekly snapshot | Weekly | Sunday 02:00 |
| Pre-deployment snapshot | Every deployment | Before `php artisan migrate --force` |

The `backup:verify` command runs automatically via the Laravel scheduler at 03:00 and logs pass/fail to `storage/logs/laravel.log`. See Section 13 for the verification checklist.

---

## 3. Retention Policy

| Backup Type | Local Retention | Off-site Retention |
|-------------|----------------|-------------------|
| Daily database backup | 30 days | 90 days |
| Weekly database snapshot | 12 weeks (3 months) | 1 year |
| Pre-deployment snapshot | 7 days | 30 days |
| File storage backups | 30 days | 90 days |

After local retention expires, delete local copies only after confirming off-site copy exists.

---

## 4. Database Backup

### Backup script

Create `/usr/local/bin/onemember-backup.sh`:

```bash
#!/bin/bash
set -euo pipefail

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="${BACKUP_PATH:-/var/backups/onemember}"
DB_NAME="${DB_DATABASE:-onemember_production}"
DB_USER="${DB_USERNAME:-onemember_user}"
DB_PASS="${DB_PASSWORD}"
LOG_FILE="/var/log/onemember/backup.log"

mkdir -p "$BACKUP_DIR"
mkdir -p "$(dirname "$LOG_FILE")"

echo "[$(date)] Starting backup: db_${DATE}.sql.gz" >> "$LOG_FILE"

mysqldump \
  --user="$DB_USER" \
  --password="$DB_PASS" \
  --host=127.0.0.1 \
  --single-transaction \
  --routines \
  --triggers \
  --add-drop-table \
  "$DB_NAME" | gzip -9 > "${BACKUP_DIR}/db_${DATE}.sql.gz"

SIZE=$(du -sh "${BACKUP_DIR}/db_${DATE}.sql.gz" | cut -f1)
echo "[$(date)] Backup complete: db_${DATE}.sql.gz (${SIZE})" >> "$LOG_FILE"

# Prune backups older than 30 days
find "$BACKUP_DIR" -name "db_*.sql.gz" -mtime +30 -delete
echo "[$(date)] Pruned backups older than 30 days." >> "$LOG_FILE"
```

```bash
chmod +x /usr/local/bin/onemember-backup.sh
```

### Crontab entry (add to www-data or root crontab)

```
# OneMember — database backup daily at 02:00
0 2 * * * /usr/local/bin/onemember-backup.sh

# OneMember — Laravel scheduler (every minute, runs backup:verify at 03:00)
* * * * * cd /var/www/onemember && php artisan schedule:run >> /dev/null 2>&1
```

### Verify backup script manually

```bash
sudo -u www-data /usr/local/bin/onemember-backup.sh
ls -lh /var/backups/onemember/
```

---

## 5. File Storage Backup

OneMember stores merchant uploads in `storage/app/public/` (logo images and any future uploads).

### Daily sync script

Add to `/usr/local/bin/onemember-backup.sh`:

```bash
# File storage backup
STORAGE_BACKUP="${BACKUP_DIR}/storage"
mkdir -p "$STORAGE_BACKUP"

rsync -az --delete \
  /var/www/onemember/storage/app/public/ \
  "$STORAGE_BACKUP/"

echo "[$(date)] File storage synced to ${STORAGE_BACKUP}" >> "$LOG_FILE"
```

### Object storage alternative (provider-agnostic via rclone)

```bash
# Install rclone and configure a remote named "backup"
rclone copy /var/www/onemember/storage/app/public/ \
  remote:onemember-backups/storage/ --log-level INFO
```

---

## 6. Environment Configuration Backup

The `.env` file contains all secrets and production configuration. It must be backed up securely.

### What to back up

```
/var/www/onemember/.env
/etc/nginx/sites-available/onemember
/etc/supervisor/conf.d/onemember-worker.conf
/usr/local/bin/onemember-backup.sh
/etc/logrotate.d/onemember
```

### How to back up

**Never commit `.env` to git.** Store a secure encrypted copy:

```bash
# Encrypt and store (using GPG — replace with your key ID)
gpg --recipient admin@yourdomain.com --encrypt /var/www/onemember/.env
# Store the encrypted .env.gpg in your password manager or secure vault

# Or use age (simpler, modern alternative)
age -r <recipient-public-key> -o /var/backups/onemember/env.age /var/www/onemember/.env
```

Alternatively, store the `.env` contents in a secrets manager (HashiCorp Vault, AWS Secrets Manager, 1Password Secrets Automation — any provider of your choice). Update this copy every time the `.env` changes.

---

## 7. Off-site Backup Recommendations

Local backups protect against application failure but not against server loss. Always maintain at least one copy of backups on a separate physical location.

### Recommended approaches (vendor-neutral)

| Approach | Complexity | Cost |
|----------|-----------|------|
| Rsync to a second server | Low | Low (VPS cost) |
| Object storage (S3-compatible) via rclone | Low | Very low |
| SFTP to a backup server | Low | Low |
| Dedicated backup service (BorgBase, Backblaze B2) | Medium | Low |

### Example: rclone to any S3-compatible storage

```bash
# Configure rclone once:
rclone config

# Daily off-site sync (add to backup script):
rclone copy /var/backups/onemember/ remote:onemember-backups/ \
  --include "db_*.sql.gz" \
  --log-level INFO \
  --log-file /var/log/onemember/rclone.log
```

### Off-site retention

Keep daily backups for 90 days off-site. Keep the most recent weekly snapshot for 1 year. This provides a 1-year audit trail for any merchant data dispute.

---

## 8. Recovery Time Objective (RTO)

**Target: < 4 hours** from incident declaration to full service restoration.

| Scenario | Estimated Recovery Time |
|----------|------------------------|
| Application code only (git restore) | 30 minutes |
| Database from backup (restore only) | 45–90 minutes |
| Full server rebuild from scratch | 3–4 hours |
| File storage restore from backup | 15–30 minutes |
| `.env` restore from secure vault | 15 minutes |

The 4-hour RTO assumes:
- Backup files are accessible (off-site or surviving disk)
- A replacement server can be provisioned within 60 minutes
- The operations engineer is familiar with the deployment guide

---

## 9. Recovery Point Objective (RPO)

**Target: < 24 hours** — maximum acceptable data loss is one day of merchant activity.

| Data type | RPO |
|-----------|-----|
| Database (transactions, members, redemptions) | < 24 hours (daily backup at 02:00) |
| Merchant uploads (logos) | < 24 hours (daily sync) |
| Application code | 0 (git — no data loss possible) |
| Environment config | Changes since last manual backup |

**Data created between the last backup (02:00) and an incident will be lost.** This is the accepted trade-off for V1.0. If a lower RPO is required in future (e.g., RPO < 1 hour), implement MySQL binary log shipping or a managed database service with point-in-time recovery.

---

## 10. Disaster Scenarios

### Scenario A — Application crashes (code or config error)

**Symptoms:** `/up` returns 500; application log shows exception.  
**Data at risk:** None — no data loss.  
**Recovery:** Rollback code (see Section 12.3) or fix the error and redeploy.  
**Estimated time:** 15–60 minutes.

---

### Scenario B — Database corruption or accidental data deletion

**Symptoms:** Merchant reports missing data; database queries fail or return unexpected results.  
**Data at risk:** Data created since the last backup (up to 24 hours).  
**Recovery:** Restore from the most recent clean backup (see Section 11.2).  
**Estimated time:** 45–90 minutes.

---

### Scenario C — Server disk failure

**Symptoms:** Server becomes inaccessible; filesystem errors in system logs.  
**Data at risk:** Data created since the last backup + off-site sync (up to 24 hours).  
**Recovery:** Provision new server → restore from off-site backup → redeploy application.  
**Estimated time:** 3–4 hours.

---

### Scenario D — Server completely destroyed (hardware failure, cloud provider outage)

**Symptoms:** Server unreachable; provider reports hardware loss.  
**Data at risk:** Same as Scenario C.  
**Recovery:** Full server rebuild procedure (see Section 12.4).  
**Estimated time:** 3–4 hours.

---

### Scenario E — Accidental `php artisan migrate:rollback` or dropped table

**Symptoms:** Application errors referencing missing columns or tables; merchant data missing.  
**Data at risk:** Schema and data created since last backup.  
**Recovery:** Restore from pre-deployment snapshot or most recent daily backup.  
**Estimated time:** 30–60 minutes.

---

### Scenario F — Security breach / ransomware

**Symptoms:** Unexpected file encryption; suspicious process activity; security log anomalies.  
**Data at risk:** Unknown — treat all production data as compromised.  
**Recovery:**  
1. Immediately isolate the server (disable network, not just the app).  
2. Do NOT pay ransom or run unknown binaries.  
3. Preserve all logs and disk images for forensic analysis.  
4. Provision a clean replacement server.  
5. Restore from off-site backup (verify backup integrity before restoring).  
6. Notify affected merchants per applicable data breach regulations.  
**Estimated time:** 4–8 hours (plus legal/notification obligations).

---

## 11. Database Restore Procedure

### 11.1 Restore to test environment (routine monthly test)

```bash
# 1. Identify a backup file
ls -lh /var/backups/onemember/

# 2. Verify the file is not corrupt
gunzip -t /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz
echo "Exit: $?"  # Must be 0

# 3. Restore to test database (NEVER to production without Step 4)
gunzip -c /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz \
  | mysql -u onemember_user -p onemember_test

# 4. Spot-check row counts
mysql -u onemember_user -p onemember_test \
  -e "SELECT COUNT(*) AS merchants FROM merchants;
      SELECT COUNT(*) AS members FROM members;
      SELECT COUNT(*) AS transactions FROM transactions;"
```

### 11.2 Restore to production (emergency)

```bash
# 1. Enable maintenance mode
cd /var/www/onemember && php artisan down

# 2. Create a safety snapshot of the current database state
mysqldump \
  --user=onemember_user --password=<password> \
  --single-transaction onemember_production \
  | gzip > /var/backups/onemember/pre_restore_$(date +%Y%m%d_%H%M%S).sql.gz

# 3. Verify the target backup is not corrupt
gunzip -t /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz

# 4. Drop and recreate the database (optional — if schema is corrupt)
# mysql -u root -p -e "DROP DATABASE onemember_production; CREATE DATABASE onemember_production;"

# 5. Restore
gunzip -c /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz \
  | mysql -u onemember_user -p onemember_production

# 6. Run any migrations that post-date the backup
php artisan migrate --force

# 7. Clear caches
php artisan cache:clear

# 8. Verify health
curl https://yourdomain.com/up

# 9. Run smoke test (login → dashboard → member list)

# 10. Disable maintenance mode
php artisan up
```

---

## 12. Recovery Checklists

### 12.1 File Restore

```bash
# Restore uploaded files from backup
rsync -az /var/backups/onemember/storage/ \
  /var/www/onemember/storage/app/public/

# Fix permissions
chown -R www-data:www-data /var/www/onemember/storage/
chmod -R 775 /var/www/onemember/storage/
```

### 12.2 Environment Restore

```bash
# Retrieve .env from secure vault / password manager
# Place at:
/var/www/onemember/.env

# Verify key is correct
php artisan config:show app.key

# Re-cache
php artisan optimize
```

### 12.3 Code Rollback

```bash
cd /var/www/onemember
php artisan down
git log --oneline -10
git checkout <previous-commit-hash>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate:rollback   # if the bad deployment ran new migrations
php artisan optimize
php artisan queue:restart
curl https://yourdomain.com/up
php artisan up
```

### 12.4 Full Server Rebuild

In the event of total server loss, rebuild in this order:

```
Step 1 — Provision new server
  - Ubuntu 22.04/24.04 LTS
  - Install PHP 8.3, Nginx, MySQL 8, Redis, Supervisor
  - Install PHP extensions (see docs/17, Section 2)

Step 2 — Restore application code
  git clone <repository-url> /var/www/onemember

Step 3 — Restore environment
  - Retrieve .env from secure vault
  - Place at /var/www/onemember/.env

Step 4 — Install dependencies
  cd /var/www/onemember
  composer install --no-dev --optimize-autoloader
  npm ci && npm run build

Step 5 — Restore database
  - Create MySQL database and user
  - Restore from off-site backup (see Section 11.2)
  - Run: php artisan migrate --force

Step 6 — Restore files
  - Restore storage/app/public/ from off-site backup (see Section 12.1)
  - Run: php artisan storage:link

Step 7 — Configure web server
  - Restore Nginx config from backup or recreate from docs/17 Section 10
  - Install SSL certificate (Let's Encrypt: certbot --nginx)

Step 8 — Configure queue workers
  - Restore /etc/supervisor/conf.d/onemember-worker.conf
  - sudo supervisorctl reread && sudo supervisorctl update
  - sudo supervisorctl start onemember-worker:*

Step 9 — Configure scheduler
  - Add crontab entry (see Section 4 of this document)

Step 10 — Configure backups
  - Restore /usr/local/bin/onemember-backup.sh
  - Add backup crontab entry

Step 11 — Final verification
  php artisan optimize
  curl https://yourdomain.com/up
  php artisan schedule:list
  supervisorctl status onemember-worker:*
  # Manual smoke test: login → dashboard → add member

Step 12 — Update DNS if server IP changed
  - Update A record
  - Allow 5–30 minutes for propagation
```

---

## 13. Backup Verification Checklist

Run weekly (automates at 03:00 daily via `php artisan backup:verify`):

### Automated check

```bash
# Check the application log for the most recent backup:verify result
grep "Backup verification" /var/www/onemember/storage/logs/laravel.log | tail -5
```

Expected log entry (pass):
```
[2026-06-29 03:00:01] production.INFO: Backup verification passed. {"file":"db_20260629_020001.sql.gz","size_mb":2.4,"age_hours":1.0}
```

Expected log entry (fail):
```
[2026-06-29 03:00:01] production.ERROR: Backup verification failed: no recent backup found. {"path":"/var/backups/onemember","cutoff":"2026-06-28 02:00:01"}
```

### Manual weekly checklist

- [ ] `php artisan backup:verify --path=/var/backups/onemember` exits 0
- [ ] Backup file exists and is dated yesterday or today
- [ ] `gunzip -t db_YYYYMMDD_HHMMSS.sql.gz` exits 0 (file is not corrupt)
- [ ] File size is consistent with prior weeks (large drop = data missing)
- [ ] Off-site copy exists and is current
- [ ] File storage backup exists in `/var/backups/onemember/storage/`

---

## 14. Testing Schedule

| Test | Frequency | Procedure |
|------|-----------|-----------|
| Automated backup verification (`backup:verify`) | Daily (03:00) | Runs automatically; check log weekly |
| Manual backup file integrity check (`gunzip -t`) | Weekly | Part of weekly maintenance checklist |
| Database restore to test environment | Monthly | See Section 11.1 |
| File restore to test environment | Quarterly | Rsync backup to test path; verify file access |
| Full disaster recovery drill | Annually | See Section 15 |

---

## 15. Annual Disaster Recovery Drill

Once per year, perform a full end-to-end recovery drill in a staging environment. This verifies that the procedures work and that the team can execute them under time pressure.

### Drill procedure

**Preparation (1 week before)**
- [ ] Identify a date with low merchant activity
- [ ] Notify stakeholders
- [ ] Confirm off-site backup is current
- [ ] Set up a clean staging server (separate from production)

**Drill execution**
- [ ] Start timer
- [ ] Simulate total server loss: treat staging server as if production is gone
- [ ] Execute full rebuild procedure (Section 12.4) using only:
  - Off-site database backup
  - Off-site file storage backup
  - Secure `.env` from vault
  - Application code from git
- [ ] Stop timer when health endpoint returns 200 and smoke test passes

**Post-drill review**
- [ ] Record total time taken
- [ ] Identify steps that were unclear, slow, or failed
- [ ] Update this document with corrections
- [ ] Update runbook (docs/18) if any procedure was wrong
- [ ] Record drill result in team notes

**Target:** Full rebuild in under 4 hours (RTO).  
**If target is missed:** Identify bottleneck, fix the procedure or tooling, and retest within 30 days.

---

## 16. Scheduled Commands Reference

All production scheduled commands as of Sprint 5.5.3:

| Command | Schedule | Purpose |
|---------|---------|---------|
| `subscriptions:process-expired-trials` | Daily 01:00 | Expire merchant trials; log to security channel |
| `backup:verify` | Daily 03:00 | Verify yesterday's database backup exists; log pass/fail |

Full crontab required on the production server:

```
# OneMember — database backup
0 2 * * * /usr/local/bin/onemember-backup.sh

# OneMember — file storage backup
30 2 * * * rsync -az /var/www/onemember/storage/app/public/ /var/backups/onemember/storage/

# OneMember — Laravel scheduler (runs all php artisan schedule:run tasks)
* * * * * cd /var/www/onemember && php artisan schedule:run >> /dev/null 2>&1
```

Verify scheduler registration at any time:

```bash
php artisan schedule:list
```

---

## 17. Known Limitations (V1.0)

| Limitation | Impact | Future improvement |
|------------|--------|-------------------|
| RPO is 24 hours | Up to 24 hours of data could be lost in a catastrophic failure | Enable MySQL binary logging + point-in-time recovery |
| No automated off-site transfer | Manual or operator-configured rclone | Add rclone step to backup script after confirming target |
| No backup size alerting | A failed or truncated backup might not be noticed | Add size check to `backup:verify` command |
| Secrets backup is manual | `.env` must be manually saved to secure vault | Integrate with a secrets manager |
| No streaming replication | Single database instance | Add MySQL replica for read scaling + failover |

---

*This document is the authoritative disaster recovery reference for OneMember V1.0. Review and update after every major infrastructure change or following a recovery incident. Last reviewed: 2026-06-29.*
