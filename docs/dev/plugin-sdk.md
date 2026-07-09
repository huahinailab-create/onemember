# Plugin SDK (PLATFORM-002 P2)

Every App ships one `App\Marketplace\Sdk\AppProvider` subclass, referenced by
its manifest `provider`. The marketplace boots it; the base class wires:

| Surface | Override | Notes |
|---|---|---|
| Routes | `routesFile()` | Return an absolute path; wrap routes in `Route::domain(config('domains.app'))->middleware(['web','auth','verified','app.installed:<key>'])` |
| Translations | `translationsPath()` | Loaded under the `<key>::` namespace (main `lang/` files remain fine for first-party apps — the EN/TH completeness test covers those) |
| Navigation | `navigation()` / `menus()` | `[{route, icon, label(lang key)}]` — the merchant sidebar renders these automatically for installed+enabled apps |
| Permissions | `permissions()` | Ability strings, e.g. `queue.tickets.manage` |
| Policies | `policies()` | `[Model::class => Policy::class]`, registered on boot |
| Events | `events()` | `[Event::class => [Listener::class]]`, registered on boot |
| Widgets | `widgets()` / `dashboardCards()` | Blade view names / `{view, sort}` for the merchant dashboard |
| Settings | `settingsSchema()` | `{key, type, label, default}` rows describing per-merchant config (stored via `AppManager::configure`) |

Single-method contracts live in `App\Marketplace\Sdk\Contracts\Provides*`
for classes that expose one surface without extending the base provider.

Reference implementations: `App\Apps\Queue\QueueAppProvider` (full),
`App\Apps\Procurement\ProcurementAppProvider`.
