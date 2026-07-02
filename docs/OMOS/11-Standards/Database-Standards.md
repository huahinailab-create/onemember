# Database Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [](./), [Coding-Standards.md](./Coding-Standards.md), [README.md](./README.md) |

---

## Purpose

Standards for database design, migrations, and Eloquent usage in OneMember.

---

## Standards

### Naming
- Tables: plural snake_case (`loyalty_programs`, `stamp_transactions`)
- Columns: singular snake_case (`merchant_id`, `created_at`)
- Foreign keys: `{singular_model}_id` (`merchant_id`, `member_id`)
- Indexes: `{table}_{column(s)}_index` or let Laravel name them

### Migration Rules
- Every migration must have a working `down()` method
- `down()` must be tested locally before committing
- Never use `migrate:fresh` in any environment with real data
- Nullable columns must have a documented reason (in the migration comment or ADR)
- JSON columns are always nullable, always have a `[]` default in the model accessor

### Model Rules
- Always define `$fillable` explicitly — never use `$guarded = []`
- Cast all enum columns to PHP-backed enums
- Cast JSON columns to `array` — override accessor to coerce null → `[]`
- Soft delete with `SoftDeletes` trait on any model where data must be recoverable
- Override `resolveRouteBinding` on soft-deleted models to include `withTrashed()`

### Multi-Tenancy
- Every merchant-scoped table has a `merchant_id` foreign key
- Every query on merchant data is scoped to `$request->user()->merchant->id`
- Never query merchant data without a tenant scope

### Query Performance
- Always eager-load relationships that are accessed in loops (`with()`)
- Avoid N+1 queries — Telescope or query log in dev to verify
- Add indexes on frequently queried foreign keys and filter columns
- Never `SELECT *` when only specific columns are needed
