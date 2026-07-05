# ADR-007 — LoyaltyProgram (Code) vs Campaign (Product) Naming

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-05 |
| **Author** | Claude Developer (MVP-010) |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [02-Product/Product-Terminology.md](../02-Product/Product-Terminology.md), [02-Product/Glossary.md](../02-Product/Glossary.md), [CTO-Decisions.md](../CTO-Decisions.md) |

---

## Context

The database table is `loyalty_programs` and the Eloquent model is `App\Models\LoyaltyProgram`. Everything user-facing — routes (`/campaigns`), route names (`campaigns.*`), controllers (`CampaignController`), views, translations, and the product vocabulary — calls the same concept a **Campaign**.

This split grew organically: the schema was designed around the long-term platform concept ("a merchant runs one or more loyalty programs"), while merchant-facing UX research showed Thai SMEs understand "แคมเปญ / campaign" far better. New contributors regularly ask which name is correct, and code reviews have flagged the mismatch as a possible bug.

## Decision

**Both names are correct, at different layers. The split is intentional and permanent for Phase 1–2:**

1. **Persistence layer** (database table, Eloquent model, foreign keys like `loyalty_program_id`): `LoyaltyProgram`. Renaming the table/model would be a high-risk, zero-value migration.
2. **Product layer** (routes, controllers, views, translations, documentation, analytics event names): `Campaign`.
3. A class alias `App\Models\Campaign` is registered for `App\Models\LoyaltyProgram` so type hints, factories, and new code can use the product vocabulary without a schema migration. Both names resolve to the same class; `Campaign::find(1) instanceof LoyaltyProgram` is `true`.
4. New foreign keys and new tables must continue to reference `loyalty_program_id` for consistency with the existing schema.
5. Any future rename of the table itself requires a new ADR and a Type C (CEO) decision, because it touches production data.

## Options Considered

### Option A — Document the split + provide a `Campaign` class alias (chosen)
**Pros:** Zero migration risk. New code reads naturally. The boundary is now explicit and documented.
**Cons:** Two names continue to exist; the alias must be learned once.

### Option B — Rename table/model to `campaigns`/`Campaign`
**Pros:** One name everywhere.
**Cons:** Risky data migration on a production-bound schema, breaks every existing FK (`loyalty_program_id`), inflates a naming preference into a schema change. Contradicts KISS.

### Option C — Rename all product-layer usage to "Loyalty Program"
**Pros:** One name everywhere.
**Cons:** Fights validated merchant vocabulary; would require re-translating the entire UI and re-teaching pilot merchants.

## Consequences

- Developers may use `App\Models\Campaign` or `App\Models\LoyaltyProgram` interchangeably; prefer `Campaign` in new product-layer code and `LoyaltyProgram` when working close to the schema.
- The glossary entry for Campaign points to this ADR.
- No database changes now or planned.
