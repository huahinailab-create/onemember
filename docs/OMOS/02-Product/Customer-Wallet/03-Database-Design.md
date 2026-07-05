# 03 — Database Design & Migration Plan

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Design Rules

- Single database, additive only. **No Phase 1 table is altered** except one nullable column on `merchants` settings (JSON — no migration needed) and indexes.
- Every wallet-side query is scoped by `customer_id`; every merchant-side query stays scoped by `merchant_id` (CTO-005). The link table is the only bridge.
- Soft deletes where the entity is user-visible; hard delete only via the PDPA erasure job.

## 2. New Tables

### `customers` — wallet identity
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| public_uuid | uuid, unique | External identifier; never expose `id` |
| name | varchar(150) | |
| phone | varchar(30), unique | E.164 normalised; primary identifier (BD-02) |
| phone_verified_at | timestamp null | |
| email | varchar(255) null, unique | Optional at signup |
| email_verified_at | timestamp null | |
| birthday | date null | Only shared with merchants under consent |
| locale | char(2) default 'th' | |
| last_login_at | timestamp null | |
| anonymised_at | timestamp null | Retention job marker |
| timestamps, softDeletes | | |

### `customer_otps` — OTP challenges (BD-02)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| phone | varchar(30), index | Challenge target (pre-account) |
| code_hash | varchar(255) | bcrypt of 6-digit code; never plaintext |
| purpose | enum(register, login, change_phone) | |
| attempts | tinyint default 0 | Max 5 |
| expires_at | timestamp | 5 minutes |
| consumed_at | timestamp null | |
| created_at | | |

### `customer_member_links` — the bridge
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| customer_id | FK customers, index | |
| member_id | FK members, unique | A Member belongs to at most one wallet |
| merchant_id | FK merchants, index | Denormalised for scoped queries without join |
| linked_via | enum(qr_join, claim_existing, merchant_invite) | Provenance |
| linked_at | timestamp | |
| unlinked_at | timestamp null | Soft unlink keeps audit trail |
| timestamps | | |
| **Unique** | (customer_id, merchant_id) where unlinked_at null | One live membership per merchant per customer |

### `consents` — versioned, append-only (Doc 06)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| customer_id | FK, index | |
| merchant_id | FK, index | |
| data_type | enum(profile, birthday, marketing, analytics) | Doc 06 §3 |
| granted | boolean | |
| consent_version | varchar(20) | Links to published consent text version |
| acted_at | timestamp | |
| source | enum(join_flow, privacy_centre, support) | |
| created_at | | **No updates or deletes — append only** |

Current consent state = latest row per (customer, merchant, data_type).

### `wallet_passes` — Apple/Google pass issuance (Doc 07, pending BD-04)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| customer_member_link_id | FK, index | One pass per membership per platform |
| platform | enum(apple, google) | |
| serial_number | uuid unique | Apple pass serial / Google object suffix |
| auth_token | varchar(255) | Apple web-service token (hashed) |
| last_pushed_at | timestamp null | |
| revoked_at | timestamp null | |
| timestamps | | |

### `apple_pass_registrations` — APNs device registry (Apple spec requirement)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| wallet_pass_id | FK, index | |
| device_library_identifier | varchar(255) | |
| push_token | varchar(255) | |
| timestamps | | Unique (wallet_pass_id, device_library_identifier) |

## 3. Relationship Map

```
customers 1 ──< customer_member_links >── 1 members (existing, unchanged)
customers 1 ──< consents >── 1 merchants (existing, unchanged)
customer_member_links 1 ──< wallet_passes 1 ──< apple_pass_registrations
```

## 4. Indexing Plan

- `customer_member_links`: covering index (customer_id, unlinked_at) for dashboard card list; (merchant_id, unlinked_at) for merchant "linked members" count.
- `consents`: (customer_id, merchant_id, data_type, id desc) — latest-state lookup.
- `customers.phone` unique — claim-existing lookup is `members.phone = customers.phone` per merchant; add index on `members.phone` if not present (verify — Phase 1 has per-merchant unique constraint already).

## 5. Migration Plan (order + rollback)

| # | Migration | Down() |
|---|---|---|
| 1 | create_customers_table | drop |
| 2 | create_customer_otps_table | drop |
| 3 | create_customer_member_links_table | drop |
| 4 | create_consents_table | drop |
| 5 | create_wallet_passes_table | drop |
| 6 | create_apple_pass_registrations_table | drop |

- All six are pure creates — rollback is `migrate:rollback --step=6`, no data loss risk to Phase 1 (DX-001 satisfied trivially; down() must still be run in staging per Deployment Standards).
- No backfill required: links are created only by customer action.
- Deploy sequence: migrate → deploy code with `FEATURE_WALLET=false` → enable on staging → PO approval → enable production.

## 6. Explicitly Not Changing

- `members` gains **no** `customer_id` column — the link table owns the relationship (keeps Phase 1 write-paths untouched and allows unlink-with-audit).
- `merchants` gains no columns; `wallet_visible` lives in existing `settings` JSON (CTO-008 accessor already null-safe).
