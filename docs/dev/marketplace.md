# Marketplace Framework (PLATFORM-002 P1)

## Concepts
- **Manifest** (`App\Marketplace\Manifest`) — typed metadata for one App:
  key, icon, status (`available|coming_soon|deprecated`), version, category,
  dependencies, permissions, feature flags, default config, navigation,
  provider class, migrations path, seeder, docs path. Built from
  `config/apps.php` entries; legacy `icon`+`status` entries remain valid.
- **AppRegistry** (singleton) — all manifests. `register()` accepts runtime
  manifests: the future third-party path needs no Core change.
- **AppManager** — `install / uninstall / enable / disable / configure`.
  Install validates status + dependencies; uninstall is blocked while
  installed apps depend on the target; disable gates access (`hasApp()`
  false) but keeps the install and all data (DR-34: nothing deleted).
- **State** — legacy `merchant.settings.installed_apps` stays the install
  list (backward compatible); `merchant_apps` rows add version, enabled,
  per-merchant config. No row ⇒ enabled (pre-marketplace merchants).

## Lifecycle events
`App\Marketplace\Events\{AppInstalled, AppUninstalled, AppEnabled, AppDisabled}` —
plus audit rows (`app.*`) on every change.

## Health
- `AppRegistry::health()` — platform level: manifest integrity, provider
  class exists, migrations path exists, dependencies known.
- `AppManager::healthFor($merchant)` — per tenant: unknown keys, disabled,
  version drift against the registry.

## Migrations & seeders
Declare `migrations` in the manifest; `MarketplaceServiceProvider` loads the
path so plain `php artisan migrate` stays the only migration entry point.
Declare `seeder` (FQCN) for optional demo/reference data — never auto-run.

## Adding an app (checklist)
1. `app/Apps/<Name>/` with an `AppProvider` subclass (see plugin-sdk.md).
2. Registry entry in `config/apps.php` (status `available` when installable).
3. `apps.name_<key>` + `apps.desc_<key>` lang keys (EN + TH).
4. Migrations under `app/Apps/<Name>/database/migrations`.
5. Routes gated with `app.installed:<key>`.
6. Tests: gating 403, tenant isolation, lifecycle.
7. A doc page under `docs/dev/apps/`.
