# 17 — Production Deployment Guide

> **Version:** OneMember 1.0  
> **Last updated:** 2026-06-29  
> **Cross-reference:** [docs/16-Production-Security-Review.md](16-Production-Security-Review.md), [docs/08-Product-Decisions.md](08-Product-Decisions.md) — DECISION-049

---

## Overview

This guide covers deploying OneMember to a Linux production server. All instructions are provider-agnostic. The application runs on any server with PHP 8.3+, MySQL 8.x, and a web server (Nginx recommended).

---

## 1. Server Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| PHP | 8.3 | 8.3 or 8.4 |
| MySQL | 8.0 | 8.0+ |
| Redis | 6.x | 7.x |
| RAM | 1 GB | 2 GB |
| Disk | 20 GB | 50 GB |
| OS | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |

---

## 2. Required PHP Extensions

```bash
php -m | grep -E "bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml|curl|zip|intl|redis|pdo_mysql"
```

Required extensions:

```
bcmath
ctype
curl
fileinfo
gd          (for future image processing)
intl
json
mbstring
openssl
pdo
pdo_mysql
redis       (if using Redis for cache/queue/session)
tokenizer
xml
zip
```

Install on Ubuntu:

```bash
sudo apt install -y php8.3-bcmath php8.3-ctype php8.3-curl php8.3-fileinfo \
  php8.3-gd php8.3-intl php8.3-mbstring php8.3-mysql php8.3-redis \
  php8.3-tokenizer php8.3-xml php8.3-zip
```

---

## 3. Production Environment Variables

Create `/path/to/onemember/.env` from `.env.example`. Required values:

```env
# Application
APP_NAME=OneMember
APP_ENV=production
APP_KEY=                    # php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_VERSION=1.0

# Database (MySQL in production — see DECISION-003)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=onemember_production
DB_USERNAME=onemember_user
DB_PASSWORD=<strong-random-password>

# Session (DECISION-046 — secure cookies required)
SESSION_DRIVER=database
SESSION_LIFETIME=60
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_ENCRYPT=true

# Cache (Redis recommended in production)
CACHE_STORE=redis

# Queue (Redis recommended in production)
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=<redis-password>
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp           # or mailgun, ses, postmark
MAIL_HOST=smtp.yourmailprovider.com
MAIL_PORT=587
MAIL_USERNAME=<mail-username>
MAIL_PASSWORD=<mail-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=OneMember

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error

# Security
BCRYPT_ROUNDS=12
TRUSTED_PROXIES=*           # Set if behind load balancer or proxy

# Vite (build artifacts served statically — no change needed)
VITE_APP_NAME=OneMember
```

---

## 4. Deployment Steps

### 4.1 First-Time Deployment

```bash
# 1. Clone repository
git clone <repository-url> /var/www/onemember
cd /var/www/onemember

# 2. Install PHP dependencies (production only — no dev packages)
composer install --no-dev --optimize-autoloader

# 3. Install and build frontend assets
npm ci
npm run build

# 4. Copy and configure environment
cp .env.example .env
# Edit .env with production values
nano .env

# 5. Generate application key
php artisan key:generate

# 6. Set file permissions
chmod -R 755 /var/www/onemember
chmod -R 775 /var/www/onemember/storage
chmod -R 775 /var/www/onemember/bootstrap/cache
chown -R www-data:www-data /var/www/onemember/storage
chown -R www-data:www-data /var/www/onemember/bootstrap/cache

# 7. Create storage symlink (for public file access)
php artisan storage:link

# 8. Run database migrations
php artisan migrate --force

# 9. Cache configuration for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 10. Verify health endpoint
curl https://yourdomain.com/up
```

### 4.2 Subsequent Deployments (Updates)

```bash
cd /var/www/onemember

# 1. Enable maintenance mode
php artisan down --retry=60

# 2. Pull latest code
git pull origin main

# 3. Update dependencies
composer install --no-dev --optimize-autoloader

# 4. Rebuild frontend (if assets changed)
npm ci
npm run build

# 5. Run any new migrations
php artisan migrate --force

# 6. Re-cache for performance
php artisan optimize

# 7. Restart queue workers (picks up new code)
php artisan queue:restart

# 8. Disable maintenance mode
php artisan up

# 9. Verify health
curl https://yourdomain.com/up
```

---

## 5. Database Migration

```bash
# Check migration status
php artisan migrate:status

# Run all pending migrations (production flag skips confirmation)
php artisan migrate --force

# Rollback one step (use with caution)
php artisan migrate:rollback
```

**Never drop or modify existing migrations.** Create new migrations for schema changes.

---

## 6. Storage Link

OneMember uses `public` disk for merchant logo uploads (when implemented).

```bash
php artisan storage:link
```

This creates `public/storage → storage/app/public`. Run once per deployment environment. Verify with:

```bash
ls -la public/storage
```

---

## 7. Cache Commands

| Command | Purpose | When to Run |
|---------|---------|-------------|
| `php artisan config:cache` | Cache all config files | Every deployment |
| `php artisan route:cache` | Cache compiled route list | Every deployment |
| `php artisan view:cache` | Pre-compile Blade views | Every deployment |
| `php artisan event:cache` | Cache event → listener map | Every deployment |
| `php artisan optimize` | Run all four above | Every deployment (shortcut) |
| `php artisan optimize:clear` | Clear all caches | After config changes / debugging |
| `php artisan config:clear` | Clear config cache only | After `.env` changes |

> **Important:** After changing any `.env` value, run `php artisan config:clear` then `php artisan config:cache`.

---

## 8. Queue Setup

OneMember does not currently dispatch queued jobs directly, but email notifications (password reset, email verification) are sent via the mail system which benefits from a queue worker in production.

### Start Queue Worker

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Restart Queue Workers (after deployment)

```bash
php artisan queue:restart
```

Workers check for a restart signal every loop cycle and gracefully stop, then Supervisor restarts them with the new code.

### Supervisor Configuration

Create `/etc/supervisor/conf.d/onemember-worker.conf`:

```ini
[program:onemember-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/onemember/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/onemember
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/onemember/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start onemember-worker:*
```

### Future Queue Requirements

When email marketing, webhooks, or async report generation are added (V2.0), the queue worker is already in place. No infrastructure changes required — just set `QUEUE_CONNECTION=redis` and add jobs.

---

## 9. Scheduler Setup

`ProcessExpiredTrials` runs daily at 01:00 server time. Add the Laravel scheduler to crontab:

```bash
crontab -e -u www-data
```

Add:

```
* * * * * cd /var/www/onemember && php artisan schedule:run >> /dev/null 2>&1
```

Verify the scheduler is registered:

```bash
php artisan schedule:list
```

Expected output:
```
0 1 * * *    php artisan subscriptions:process-expired-trials
```

---

## 10. HTTPS

HTTPS is mandatory in production. The `SESSION_SECURE_COOKIE=true` and HSTS header both require HTTPS.

### Nginx Configuration (recommended)

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/onemember/public;
    index index.php;

    ssl_certificate     /etc/ssl/certs/yourdomain.com.crt;
    ssl_certificate_key /etc/ssl/private/yourdomain.com.key;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Trusted Proxies

If behind a load balancer or reverse proxy, add to `.env`:

```env
TRUSTED_PROXIES=*
```

This ensures `request()->ip()` and `request()->isSecure()` return correct values (security logging, HSTS header).

---

## 11. SSL Certificate

### Using Certbot (Let's Encrypt — free)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Certbot installs the certificate and auto-renews via a cron job. Verify auto-renewal:

```bash
sudo certbot renew --dry-run
```

---

## 12. File Permissions

```bash
# Application files — readable by web server, writable by deploy user
chmod -R 755 /var/www/onemember

# Storage and cache — writable by web server (PHP-FPM user)
chmod -R 775 /var/www/onemember/storage
chmod -R 775 /var/www/onemember/bootstrap/cache

# Ownership
chown -R deploy-user:www-data /var/www/onemember
chown -R www-data:www-data /var/www/onemember/storage
chown -R www-data:www-data /var/www/onemember/bootstrap/cache

# Protect .env
chmod 600 /var/www/onemember/.env
```

---

## 13. Backups

### Database Backup

```bash
# Daily backup script: /usr/local/bin/onemember-backup.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/var/backups/onemember
mkdir -p $BACKUP_DIR

mysqldump \
  --user=onemember_user \
  --password=<password> \
  --single-transaction \
  --routines \
  --triggers \
  onemember_production | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Delete backups older than 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete
```

Add to crontab (daily at 02:00):

```
0 2 * * * /usr/local/bin/onemember-backup.sh >> /var/log/onemember/backup.log 2>&1
```

### Storage Backup

If using local disk for file uploads:

```bash
# Sync storage to backup location
rsync -az /var/www/onemember/storage/app/ /var/backups/onemember/storage/
```

### Retention Policy

| Backup Type | Frequency | Retention |
|-------------|-----------|-----------|
| Database | Daily | 30 days |
| Database | Weekly snapshot | 90 days |
| File storage | Daily sync | 30 days |
| Pre-deployment DB snapshot | Per deploy | 7 days |

### Restore Testing

Test restores monthly:

```bash
# Restore to test environment
gunzip < /var/backups/onemember/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u onemember_user -p onemember_test
```

---

## 14. Monitoring Recommendations

| Tool Category | Recommendation | Free Option |
|---------------|----------------|-------------|
| Uptime monitoring | Check `/up` every 60s | UptimeRobot, BetterStack |
| Error tracking | Capture exceptions with stack traces | Sentry (free tier) |
| Log aggregation | Forward `storage/logs/` | Papertrail, Logtail |
| Server metrics | CPU, memory, disk, MySQL | Netdata, Grafana |
| Security log alerts | Alert on `auth.login.failed` spikes | Log-based alerting |

### Health Check URL

Configure uptime monitors to poll:

```
GET https://yourdomain.com/up
Expected: HTTP 200, body contains "status":"ok"
```

### Log Paths

| Log | Path | Rotation |
|-----|------|---------|
| Application | `storage/logs/laravel.log` | Daily (Laravel `daily` driver) |
| Security events | `storage/logs/security.log` | Daily, 90-day retention |
| Queue worker | `/var/log/onemember/worker.log` | Supervisor |

---

## 15. Rollback Procedure

### Code Rollback

```bash
cd /var/www/onemember

# Enable maintenance mode
php artisan down

# Roll back to previous commit
git log --oneline -10   # identify the target commit
git checkout <commit-hash>

# Reinstall dependencies for that version
composer install --no-dev --optimize-autoloader

# Rebuild assets
npm ci && npm run build

# Re-cache
php artisan optimize

# Restart queue workers
php artisan queue:restart

# Disable maintenance mode
php artisan up
```

### Database Rollback

```bash
# Roll back the last migration
php artisan migrate:rollback

# Or restore from backup (preferred for data integrity)
gunzip < /var/backups/onemember/db_pre_deploy.sql.gz | mysql -u onemember_user -p onemember_production
```

**Always take a database snapshot before deploying migrations.**

---

## 16. Post-Deployment Checklist

After every deployment, verify:

```bash
# 1. Health endpoint
curl https://yourdomain.com/up

# 2. Migration status — no pending migrations
php artisan migrate:status

# 3. Scheduler registered
php artisan schedule:list

# 4. Queue workers running
supervisorctl status onemember-worker:*

# 5. Cache populated
php artisan config:show app.env
# Should return "production"

# 6. Application log — no critical errors
tail -50 storage/logs/laravel.log

# 7. Smoke test — log in and navigate
# Manual: register/login → dashboard → members → campaigns
```

---

## 17. Common Troubleshooting

### 500 Error After Deployment

```bash
# Check application log
tail -100 storage/logs/laravel.log

# Clear all caches and regenerate
php artisan optimize:clear
php artisan optimize

# Check file permissions
ls -la storage/
ls -la bootstrap/cache/
```

### Queue Jobs Not Processing

```bash
# Check worker status
supervisorctl status onemember-worker:*

# Restart workers
supervisorctl restart onemember-worker:*

# Check queue table for failed jobs
php artisan queue:failed
php artisan queue:retry all
```

### Migrations Fail

```bash
# Check migration status
php artisan migrate:status

# View migration error
php artisan migrate --force -v

# If migration is partially applied, check the DB manually
# Never edit migration files — create new ones
```

### Session / Login Issues

```bash
# Clear session table
php artisan session:table   # verifies table exists
php artisan db:table sessions

# Clear application cache
php artisan cache:clear

# Verify session configuration
php artisan config:show session
```

### `.env` Changes Not Taking Effect

```bash
php artisan config:clear
php artisan config:cache
```

### `storage:link` Missing After Deployment

```bash
php artisan storage:link
ls -la public/storage   # Should be a symlink
```

---

## Production Operations Reference

| Command | Purpose |
|---------|---------|
| `php artisan optimize` | Cache config, routes, views, events |
| `php artisan optimize:clear` | Clear all caches |
| `php artisan down` | Enable maintenance mode |
| `php artisan up` | Disable maintenance mode |
| `php artisan migrate --force` | Run migrations (skips confirmation) |
| `php artisan queue:work --tries=3` | Start queue worker |
| `php artisan queue:restart` | Signal workers to restart after deployment |
| `php artisan queue:failed` | List failed jobs |
| `php artisan queue:retry all` | Retry all failed jobs |
| `php artisan schedule:run` | Run scheduler (called by cron every minute) |
| `php artisan schedule:list` | List registered scheduled commands |
| `php artisan about` | Display application overview |
| `php artisan config:show app` | Show resolved app config |
| `php artisan key:generate` | Generate new APP_KEY |
| `php artisan route:list` | List all routes with middleware |

---

*This document is the authoritative deployment guide for OneMember V1.0. Update after each major infrastructure change.*
