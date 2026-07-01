# Architecture Rules

These are standing technical decisions for OneMember. All sprints must comply. No rule may be overridden without a documented decision in `docs/08-Product-Decisions.md` and explicit Product Owner approval.

---

## Stack

| Layer | Technology | Version |
|---|---|---|
| Language | PHP | 8.3+ (production: 8.5) |
| Framework | Laravel | 13.x |
| Frontend | Bootstrap 5 | 5.x |
| Database | MySQL | 8.x |
| Queue | Laravel Queue (database driver) | — |
| Mail | Resend via `resend/resend-php` | 1.4.0+ |
| Storage | Laravel filesystem (local / S3) | — |
| Deployment | Laravel Forge + DigitalOcean | — |

**Do not introduce:**
- Tailwind CSS
- Vue.js, React, or Livewire (not currently in the stack)
- Additional PHP frameworks or ORMs
- Direct database connections (use Eloquent or Query Builder only)

---

## Multi-Tenancy Model

OneMember is a **single-database, shared-schema multi-tenant application**.

- Each `Merchant` is a tenant.
- Every model that belongs to a merchant must have a `merchant_id` foreign key.
- Every query on merchant-scoped data must filter by the authenticated user's `merchant_id`.
- Never return data from one merchant to another merchant's session.
- Controllers must resolve `$merchant = $request->user()->merchant` and scope all queries to it.

**Tenant isolation is a security requirement, not a feature.**

---

## Authentication & Email Verification

- Laravel built-in auth (Breeze scaffolding, no Jetstream or Fortify).
- Email verification is **required** and must not be disabled.
- The `verified` middleware must be applied to all routes that serve merchant data.
- Email verification uses `VerifyEmailNotification extends VerifyEmail implements ShouldQueue` to decouple verification email from the HTTP response.
- Verification emails are queued — never sent synchronously in the controller.

---

## Email Architecture

- All emails use Resend as the transport (`MAIL_MAILER=resend`).
- Controllers must **never** send email directly.
- Email sending must go through: `event(new SomeEvent($model))` → Listener → Notification/Mailable.
- `Resend::client()` requires a non-null API key — always queue email to prevent `TypeError` on missing key.
- Test environment uses `MAIL_MAILER=array`.

---

## Queue

- Production: `QUEUE_CONNECTION=database`
- All jobs must implement `ShouldQueue`.
- Jobs that send email, generate reports, or process large datasets must always be queued.
- Job failures must be logged to `failed_jobs` table.
- Queue workers must be restarted after each deployment (`php artisan queue:restart`).

---

## Database Conventions

- All tables use snake_case naming.
- All models use PascalCase.
- Primary keys: `id` (auto-increment unsigned bigint).
- Foreign keys: `{model}_id` (e.g., `merchant_id`, `member_id`).
- Soft deletes: use `SoftDeletes` trait on any model where records should be recoverable.
- Timestamps: all tables have `created_at` and `updated_at` unless explicitly excluded.
- JSON columns: nullable, cast to `array` via Eloquent — always provide a `[]` default in the accessor for null values.
- Never run `migrate:fresh` in any environment with real data.

---

## Routing

- All authenticated routes: `['auth', 'verified']` middleware.
- All routes must be named.
- Route names use dot notation: `merchants.show`, `campaigns.rewards.create`.
- Route model binding used by default; override `resolveRouteBinding` on models with `SoftDeletes`.
- Developer tools routes: additional `DevToolsAccess` middleware (returns 404 in production).

---

## Models

- Use `$fillable` (not `$guarded = []`) for explicit mass-assignment protection.
- Cast all enum columns to PHP-backed enums.
- Cast all JSON columns to `array` — override accessor if default may be null.
- Soft-deleted models override `resolveRouteBinding` to include `withTrashed()`.
- User model: no `SoftDeletes` (no `deleted_at` column).

---

## Controllers

- Thin controllers: business logic belongs in Service classes under `app/Services/`.
- One public method per action (no `__invoke` for resource controllers, use named methods).
- Always resolve the merchant from `$request->user()->merchant`.
- Redirect after POST (PRG pattern) — never render a view directly after form submission.
- Use Form Request classes for validation (`php artisan make:request`).

---

## Service Layer

- `app/Services/` contains stateless service classes injected via the constructor.
- Services are bound via Laravel's service container (auto-resolved by type hint).
- Services must not interact with the HTTP layer (`Request`, `Response`, `redirect()`).
- Services may throw exceptions — controllers catch and handle them.

---

## Security

- Never expose `APP_DEBUG=true` in production.
- All sensitive config values loaded from `.env` (never hardcoded).
- Audit-sensitive actions using the `developer_actions` table or application event log.
- Rate limiting applied to auth endpoints (`throttle:6,1` on login, register, verify).
- File uploads: validate MIME type and extension; store in private disk; never in `public/`.

---

## Developer Tools

- Gated by two conditions: `APP_ENV !== 'production'` AND `DEV_TOOLS_ENABLED=true`.
- `DevToolsAccess` middleware returns 404 unconditionally in production.
- Every dev action is logged to `developer_actions` table with user, action, IP, and user agent.
- Developer tool routes are prefixed `/dev/` and named `dev.*`.
- No dev tool functionality is ever exposed in a production code path.

---

## Localization

- Application supports English (`en`) and Thai (`th`).
- All user-visible strings in `lang/en/` and `lang/th/`.
- Locale detection via `SetLocale` middleware reading `merchant->settings['locale']`.
- Currency, date format, and timezone from merchant settings — never hardcoded.
