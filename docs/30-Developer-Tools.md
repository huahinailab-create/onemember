# 30 — Developer Tools

Developer Tools is a **development-only module** that provides a full-featured admin toolkit for OneMember engineers. It is **permanently blocked in production** by two independent guards.

## Security Model

### Access Requirements (BOTH must be true)

| Condition | Required Value |
|---|---|
| `DEV_TOOLS_ENABLED` | `true` |
| `APP_ENV` | NOT `production` |

The `DevToolsAccess` middleware enforces this on every request. If either condition fails, the route returns **404**. The nav section is hidden in the layout with `@env('production')`.

### Feature Flag

```env
# Enable developer tools (never true in production)
DEV_TOOLS_ENABLED=true
```

Config key: `devtools.enabled` (from `config/devtools.php`)

---

## Enabling Developer Tools

### Local Development

In your `.env`:

```env
APP_ENV=local
DEV_TOOLS_ENABLED=true
```

Visit `/dev` after logging in.

### Staging (Laravel Forge)

In Forge → Site → Environment, add:

```env
APP_ENV=staging
DEV_TOOLS_ENABLED=true
```

Then run:
```bash
php artisan config:clear
php artisan config:cache
```

### Production

**Never enable developer tools in production.**

- `DEV_TOOLS_ENABLED` must be `false` or absent
- `APP_ENV=production` triggers a hard 404 regardless of the flag

---

## Pages — Sprint DEV-02

| Route | Page | Description |
|---|---|---|
| `/dev` | Developer Dashboard | System stats, health cards, env summary |
| `/dev/quick-actions` | Quick Actions | One-click data generation with queued jobs |
| `/dev/mail-inspector` | Mail Inspector | Mail config, send test, Resend API check |
| `/dev/queue-inspector` | Queue Inspector | Stats, failed jobs, retry/delete/restart |
| `/dev/env-inspector` | Environment Inspector | Runtime info + health in one view |
| `/dev/performance` | Performance Tools | Artisan cache/optimize/clear commands |
| `/dev/logs` | Log Viewer | Tail last 100 lines, search, filter, download, clear |
| `/dev/demo-reset` | Demo Reset | Wipe all demo data for a merchant (transactional) |
| `/dev/feature-flags` | Feature Flags | Read-only view of env flags + how-to-enable docs |

## Pages — Sprint DEV-01

| Route | Page |
|---|---|
| `/dev/users` | User management |
| `/dev/members` | Member management |
| `/dev/merchants` | Merchant management |
| `/dev/mail` | Test mail (legacy, superseded by Mail Inspector) |
| `/dev/database` | Database + artisan commands |
| `/dev/queue` | Queue management (legacy, superseded by Queue Inspector) |
| `/dev/storage` | Storage + log management |
| `/dev/helpers` | Data generation helpers |
| `/dev/environment` | Environment info |
| `/dev/health` | System health |
| `/dev/danger` | Danger zone (requires typing DELETE) |

---

## Audit Log

Every developer action is recorded in the `developer_actions` table:

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Auto-increment |
| `user_id` | bigint | The authenticated developer |
| `action` | string | e.g. `quick.generate_members` |
| `target_type` | string | e.g. `App\Models\Merchant` |
| `target_id` | bigint | Target model ID |
| `details` | JSON | Additional context |
| `ip_address` | string | Request IP |
| `user_agent` | text | Browser/client |
| `created_at` | timestamp | When the action occurred |

---

## Quick Actions — Job-Based Data Generation

All heavy data generation dispatches `GenerateDemoDataJob` to the queue. The job runs in the background using `QUEUE_CONNECTION`. Supported types:

- `members` — Creates fake Member records (MemberFactory)
- `purchases` — Creates Earn transactions with fake purchase amounts
- `points` — Creates Earn loyalty point transactions
- `stamps` — Creates Earn stamp transactions
- `redemptions` — Creates Redemption + Redeem transaction pairs
- `birthday` — Creates members with `birthday = today`
- `notifications` — Inserts raw notification rows

---

## Demo Reset

The Demo Reset page wipes all data for a selected merchant in a single database transaction:

1. Deletes all transactions for the merchant's members
2. Deletes all redemptions for the merchant's members
3. Deletes notifications (if `notifications` table exists)
4. Force-deletes all members (including soft-deleted)
5. Force-deletes all rewards
6. Clears `failed_jobs` and `jobs` tables

Requires typing `DELETE` in the confirmation input.

---

## Decisions

- DECISION-062 — Developer Tools module (DEV-01)
- DECISION-063 — Developer Productivity Suite (DEV-02)
