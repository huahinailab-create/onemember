#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════════════════
# OneMember — Forge deploy script (DEPLOY-001)
# Paste into Forge → Site → App → Deploy Script. This ordering is the fix for
# "new routes not visible after deploy": caches are cleared and rebuilt AFTER
# the new code is in place, then PHP-FPM is reloaded so opcache drops old files.
# ═══════════════════════════════════════════════════════════════════════════
set -e

cd /home/forge/app.onemember.co   # ← adjust to your Forge site path

# 1. Pull the release
git pull origin $FORGE_SITE_BRANCH

# 2. Dependencies + assets
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci --no-audit --no-fund
npm run build

# 3. Database (additive migrations only; down() tested in CI per DX-001)
$FORGE_PHP artisan migrate --force

# 4. Caches — CLEAR FIRST, then rebuild from the new code.
#    Skipping optimize:clear is the #1 cause of stale/invisible routes.
$FORGE_PHP artisan optimize:clear
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan view:cache
$FORGE_PHP artisan event:cache

# 5. Workers pick up new code
$FORGE_PHP artisan queue:restart

# 6. Reload PHP-FPM so opcache serves the new release
#    (Forge provides this helper; it runs the reload safely.)
( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

# 7. Post-deploy verification — fails the deploy loudly if routes are missing
$FORGE_PHP artisan route:list | grep -q "control-room" || { echo "DEPLOY FAIL: control-room route missing"; exit 1; }
$FORGE_PHP artisan route:list | grep -q "go-live"      || { echo "DEPLOY FAIL: go-live route missing"; exit 1; }
curl -fsS https://app.onemember.co/up > /dev/null      || { echo "DEPLOY FAIL: health endpoint"; exit 1; }

echo "Deploy OK: $(git rev-parse --short HEAD)"

# ── Server .env requirements (one-time; deploy fails routes without them) ──
#   APP_ENV=production  APP_DEBUG=false
#   APP_DOMAIN=app.onemember.co
#   CORPORATE_DOMAIN=onemember.co
#   SESSION_DOMAIN=.onemember.co
# After ANY .env change: php artisan config:clear && php artisan config:cache
