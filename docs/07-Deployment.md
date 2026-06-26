# 07 — Deployment

> **Status:** Draft  
> **Last updated:** 2026-06-27

## 1. Environments

| Environment | Purpose | Branch |
|-------------|---------|--------|
| Local       | Development | any |
| Staging     | QA / review | `develop` |
| Production  | Live traffic | `main` |

## 2. Server Requirements

- PHP 8.3+ with extensions: `mbstring`, `xml`, `curl`, `zip`, `pdo`, `pdo_mysql`, `bcmath`, `intl`
- Composer 2.x
- Node.js 20+ (build only)
- MySQL 8+ or PostgreSQL 15+
- Redis (queue & cache in production)
- Nginx or Apache with URL rewriting enabled

## 3. Deploy Checklist

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan queue:restart
```

## 4. Environment Variables (Production)

| Key | Notes |
|-----|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Generated via `php artisan key:generate` |
| `APP_URL` | Full HTTPS URL |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | Database credentials |
| `CACHE_STORE` | `redis` |
| `QUEUE_CONNECTION` | `redis` |
| `REDIS_HOST` | Redis server address |
| `MAIL_*` | Mail driver credentials |
| `SESSION_DRIVER` | `redis` or `database` |

## 5. Zero-Downtime Deployment

Consider [Laravel Envoyer](https://envoyer.io) or a CI/CD pipeline (GitHub Actions) for zero-downtime deploys with atomic symlink switching.

## 6. Backups

- Database: daily automated backups, 30-day retention
- Storage (`storage/app/`): sync to S3 or equivalent
