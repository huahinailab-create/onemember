# Deployment Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [08-Deployment-Rules.md](./08-Deployment-Rules.md), [Security-Standards.md](./Security-Standards.md), [Review-Standards.md](./Review-Standards.md) |

---

## Purpose

Standards and required steps for deploying OneMember to production.

---

## Standards

### Deployment Platform
Laravel Forge + DigitalOcean. HTTPS via Let's Encrypt (managed by Forge).

### Deployment Command Sequence
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart
```

### Pre-Deployment Gates
All of these must be true before deployment:
1. Sprint review is ✅ Approved by ChatGPT CTO
2. Product Owner has given explicit verbal or written approval
3. `php artisan test` passes locally — zero failures
4. All new `.env` variables are documented and added to Forge
5. `php artisan migrate:rollback` tested locally (if migration is included)

### Production Environment Must-Have
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DEV_TOOLS_ENABLED` — not set (or set to `false`)
- `QUEUE_CONNECTION=database`
- `SESSION_DRIVER=database`
- `MAIL_MAILER=resend`

### Post-Deployment Verification
1. `https://app.onemember.app/up` returns `{"status":"ok"}`
2. Login flow works
3. Dashboard loads for a real merchant
4. No 500 errors in Forge logs for 5 minutes
5. `php artisan queue:monitor` shows workers are processing

### Rollback
```bash
php artisan migrate:rollback     # if migration was run
git revert HEAD --no-edit
# rebuild caches for reverted version
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan queue:restart
```

> Full deployment rules: `ai/08-Deployment-Rules.md`
