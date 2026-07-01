# Deployment Rules

Every production deployment must follow this protocol. No shortcuts, no exceptions.

---

## Pre-Deployment Checklist

Before any code reaches production, confirm:

- [ ] Sprint review is ✅ Approved by ChatGPT CTO
- [ ] Product Owner has given explicit deploy approval
- [ ] `php artisan test` passes locally (zero failures)
- [ ] All new `.env` variables are documented and added to Forge
- [ ] Database migration is reversible (`down()` tested)
- [ ] Queue worker restart is planned
- [ ] No `APP_DEBUG=true` in production `.env`
- [ ] No `DEV_TOOLS_ENABLED=true` in production `.env`

---

## Deployment Steps (Forge / DigitalOcean)

Run in this exact order:

```bash
# 1. Pull latest code
git pull origin main

# 2. Install/update PHP dependencies (production optimized)
composer install --no-dev --optimize-autoloader

# 3. Run new migrations
php artisan migrate --force

# 4. Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Restart queue workers
php artisan queue:restart

# 6. (Optional) Clear application cache if needed
php artisan cache:clear
```

**Note:** Forge deploy scripts should include steps 1–5 automatically. Verify the Forge deploy script matches this sequence.

---

## Environment Variables

### Required in Production

| Variable | Description | Safe to log? |
|---|---|---|
| `APP_ENV` | Must be `production` | Yes |
| `APP_DEBUG` | Must be `false` | Yes |
| `APP_KEY` | Laravel encryption key | **No** |
| `APP_URL` | Full production URL with https | Yes |
| `DB_*` | Database credentials | **No** |
| `RESEND_API_KEY` | Resend mail API key | **No** |
| `QUEUE_CONNECTION` | Must be `database` | Yes |
| `SESSION_DRIVER` | `database` | Yes |
| `SESSION_DOMAIN` | Production domain | Yes |
| `STRIPE_SECRET` | Stripe secret key | **No** |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook signing key | **No** |

### Must NOT Be Set in Production

| Variable | Reason |
|---|---|
| `DEV_TOOLS_ENABLED` | Developer tools must never appear in production |
| `MAIL_MAILER=array` | Array mailer silently drops emails |
| `APP_DEBUG=true` | Exposes stack traces and source code |

---

## Rollback Procedure

If a deployment causes issues:

```bash
# 1. Revert the migration (if one was run)
php artisan migrate:rollback

# 2. Revert code to previous commit
git revert HEAD --no-edit
# or hard reset to last known good commit (use only if revert is not possible)
git reset --hard [last-good-commit-hash]

# 3. Redeploy previous version
git push origin main --force  # Only if absolutely necessary

# 4. Rebuild caches for previous version
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart queue workers
php artisan queue:restart
```

**Data rollback:** If the migration deleted or altered data, a database backup restore may be required. Always take a manual database snapshot before any destructive migration.

---

## Zero-Downtime Guidelines

OneMember does not currently have a zero-downtime deployment pipeline. To minimize disruption:

- Deploy during low-traffic periods (e.g., late night Bangkok time, UTC+7).
- Migrations that add new nullable columns are safe to run with the app live.
- Migrations that rename or drop columns require maintenance mode:

```bash
php artisan down --render="errors::503"
# ... run migration ...
php artisan up
```

---

## Post-Deployment Verification

After every deployment, confirm:

1. `https://app.onemember.app/up` returns `{"status":"ok"}` (health check)
2. Login flow works
3. Dashboard loads for a real merchant
4. No 500 errors in Forge logs for 5 minutes
5. Queue worker is processing jobs (`php artisan queue:monitor`)

---

## Production Access Rules

- No direct SSH `php artisan tinker` on production data unless it is a data emergency.
- No direct `DB::statement()` or raw SQL on production without CTO approval.
- All production data changes must be done via migrations or audited admin commands.
- Developer tools (`/dev/*`) must return 404 — verify this after every deployment.
