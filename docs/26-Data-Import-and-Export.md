# 26 — Data Import & Export

> **Sprint:** 6.4  
> **Last updated:** 2026-06-30  
> **Decision reference:** DECISION-058  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/12-SaaS-Architecture.md](12-SaaS-Architecture.md)

---

## 1. Overview

Sprint 6.4 adds bulk data import and export for merchants. Merchants can:

- **Import** members from a CSV file via a 3-step wizard (Upload → Map Columns → Confirm)
- **Export** members, campaigns, rewards, purchases, and redemptions as UTF-8 CSV files

All operations are tenant-isolated. No data from another merchant can appear in an import validation or an export.

---

## 2. Architecture

```
DataManagementController
    │
    ├── ImportService          ← All import logic (parse, map, validate, execute)
    │     └── ImportMembersJob ← Queued for >5,000 rows
    │
    ├── ExportService          ← All export logic (streamed CSV per entity type)
    │
    ├── AnalyticsService       ← Events: import_started, import_completed, import_failed, export_generated
    └── SecurityLogger         ← Events: data.import.*, data.export.generated (no PII)
```

### Services

| Service | File | Responsibility |
|---|---|---|
| `ImportService` | `app/Services/ImportService.php` | CSV parsing, column mapping, validation, execution |
| `ExportService` | `app/Services/ExportService.php` | Streamed CSV responses for all entity types |

### Controller

`DataManagementController` — thin, delegates to services. No business logic inside the controller.

---

## 3. Import Flow

### 3-Step Wizard

```
Step 1: Upload
  POST /settings/data/import/members/upload
  → Parse CSV, auto-detect column mapping
  → Store temp file in storage/app/import-temp/{merchant_id}/{uuid}.csv
  → Show mapping table

Step 2: Map Columns
  POST /settings/data/import/members/preview
  → Apply merchant's column mapping
  → Validate all rows against business rules and duplicate detection
  → Show validation report + first 20 valid rows

Step 3: Confirm
  POST /settings/data/import/members/execute
  → Execute import (sync or queue depending on row count)
  → Clean up temp file
  → Show result summary
```

### Duplicate Detection

Duplicates are detected in two passes:

1. **Against existing records** — pre-loads all `phone` and `email` values for the merchant from the database before validation begins (single query each).
2. **Within the CSV** — tracks phones/emails seen in the current import to catch intra-file duplicates.

A row is marked as a duplicate (not an error) if either phone or email matches an existing record. Duplicates are **skipped**, never overwritten (DECISION-058).

### Per-Row Transactions

Each valid row is wrapped in a `DB::transaction()`. A row that fails (e.g., unique constraint race condition) is counted in `failed` and logged, but does not abort the rest of the import.

### Queue Threshold

| Row count | Behaviour |
|---|---|
| ≤ 5,000 | Synchronous — result shown immediately |
| > 5,000 | `ImportMembersJob` dispatched — merchant sees "Import Queued" screen |

The threshold constant is `ImportService::MAX_SYNC_ROWS = 5000`.

---

## 4. Column Mapping

### Auto-Detection

`ImportService::detectMapping()` normalises CSV headers to lowercase and matches them against known aliases:

| OneMember Field | Accepted CSV Names |
|---|---|
| `first_name` | first name, firstname, first_name, name, given name, ชื่อ, ชื่อจริง |
| `last_name` | last name, lastname, last_name, surname, family name, นามสกุล |
| `phone` | phone, mobile, telephone, tel, contact, เบอร์โทร, เบอร์, โทรศัพท์ |
| `email` | email, e-mail, email address, mail, อีเมล, อีเมล์ |
| `date_of_birth` | dob, birthday, date of birth, birth date, วันเกิด |
| `gender` | gender, sex, เพศ *(accepted but not stored — no DB column)* |
| `notes` | notes, note, comment, remarks, หมายเหตุ |
| `nickname` | nickname, nick, nick name, ชื่อเล่น |
| `tags` | tags, tag, labels, แท็ก *(accepted but not stored — future use)* |

### Writable Fields

Only these fields are written to the `members` table:

| CSV Field | DB Column | Mapping |
|---|---|---|
| first_name + last_name | `name` | Concatenated: "First Last" |
| phone | `phone` | Normalised (strip spaces/dashes) |
| email | `email` | Lowercased |
| date_of_birth | `birthday` | Parsed to `Y-m-d` |
| notes | `notes` | Trimmed |
| nickname | `nickname` | Trimmed |

---

## 5. Validation Rules

| Field | Rule |
|---|---|
| first_name | Required |
| phone | Required, 7–20 digits (with optional `+` prefix) |
| email | Valid email format (if present) |
| date_of_birth | Parseable date (if present); warns if unparseable but still imports |
| Duplicates | Skipped with explanation |
| Row length | Warning if column count ≠ header count |

### Date Parsing Formats

The service tries these formats in order:

`Y-m-d`, `d/m/Y`, `d-m-Y`, `m/d/Y`, `d.m.Y`, `Y/m/d`, `d M Y`, `d F Y`, and Carbon natural parse as fallback.

---

## 6. Export Flow

All exports are **streamed** — they never buffer the full dataset in memory. Data is chunked in batches of 500 rows using Laravel's `chunk()`.

### Available Exports

| Type | Route | Data Source |
|---|---|---|
| Members | `GET /settings/data/export/members` | `members` table, active only |
| Campaigns | `GET /settings/data/export/campaigns` | `loyalty_programs` table |
| Rewards | `GET /settings/data/export/rewards` | `rewards` table |
| Purchases | `GET /settings/data/export/purchases` | `transactions` WHERE type = 'earn' |
| Redemptions | `GET /settings/data/export/redemptions` | `redemptions` table |

### CSV Format

- **Encoding:** UTF-8 with BOM (`\xEF\xBB\xBF`) for Excel compatibility
- **Delimiter:** Comma (`,`)
- **Headers:** First row is always the column header row
- **Filename:** `onemember_{type}_{merchant-slug}_{YYYYMMDD_HHmmss}.csv`

---

## 7. Security

### File Upload Security

- **Extension check:** Only `.csv` files are accepted. The file extension is validated explicitly (the `mimes:csv,txt` rule alone is not enough because PHP's Mime detector sometimes accepts text as `text/plain`).
- **MIME check:** Accepted MIME types: `text/plain`, `text/csv`, `application/csv`, `application/vnd.ms-excel`, and any `text/*`. Executables are rejected.
- **Max size:** 10 MB (`max:10240` in KB).
- **Path traversal:** Temp files are stored at `import-temp/{merchant_id}/{uuid}.csv` under the `local` disk (private, not web-accessible). The path is constructed by the service — not from user input.

### Tenant Isolation

- **Import validation:** Duplicate detection queries always include `WHERE merchant_id = ?`. A phone or email belonging to another merchant is never flagged as a duplicate for the current merchant.
- **Export:** All queries include `merchant_id` scope via the Merchant model relationship. The controller resolves the merchant from `$request->user()->merchant` — not from a URL parameter.
- **Temp file namespace:** `import-temp/{merchant_id}/` ensures one merchant cannot access another's upload.

### No PII in Logs

Security log entries (`data.import.*`, `data.export.generated`) record only counts and event type — never member names, phones, or emails.

---

## 8. Queue Behaviour

Large imports use `ImportMembersJob`:

```php
ImportMembersJob::dispatch(
    merchantId: $merchant->id,
    userId:     $user->id,
    tempFilePath: $tempPath,       // relative to local disk
    headers:    $headers,
    mapping:    $mapping,
    validRowCount: $validation['valid'],
);
```

The job:
1. Re-parses the CSV from the temp file.
2. Re-validates (to catch any changes since the preview step).
3. Executes the import (per-row transactions).
4. Tracks analytics and security log.
5. Deletes the temp file (in `finally` — always runs even on failure).

**Queue connection:** `database` (default). Workers must be running for queued imports to process.

**Timeout:** 600 seconds (10 minutes). Tries: 1 (no retry — import rows are not idempotent without a clear unique key).

---

## 9. CSV Template

The recommended template format for member imports:

```csv
First Name,Last Name,Phone,Email,Date of Birth,Notes,Nickname
Alice,Smith,0812345678,alice@example.com,15/06/1990,VIP customer,Ali
Bob,Jones,0823456789,,,,
```

Rules:
- First row must be the header row.
- Column order does not matter — the mapping step handles it.
- Empty cells are fine for optional fields.
- Phone must not contain spaces if no normalisation is desired (the service strips them).
- Date of birth accepted in most common formats: `d/m/Y`, `Y-m-d`, `d-m-Y`, etc.

---

## 10. Localization

All UI strings are in:
- `lang/en/data.php` — 67 keys
- `lang/th/data.php` — 67 keys (identical key count)

Both `lang/en/settings.php` and `lang/th/settings.php` have the `tab_data` key added.

---

## 11. Analytics Events

| Event | Properties | When |
|---|---|---|
| `import_started` | `type`, `row_count` | After successful upload and parse |
| `import_completed` | `type`, `imported`, `failed`, `duplicates`, `skipped` | After synchronous import |
| `import_failed` | `type`, `error` | On exception during import |
| `export_generated` | `type` | When any export is downloaded |

---

## 12. Testing

**File:** `tests/Feature/DataImportExportTest.php` (36 tests)

| Test Category | Count |
|---|---|
| ImportService unit (column detection, mapping, should-queue) | 3 |
| Validation (required fields, email, duplicates, DOB, warnings) | 8 |
| Tenant isolation (import + export) | 2 |
| Import execution | 1 |
| Queue threshold | 1 |
| HTTP upload validation (wrong type, oversized, wrong extension) | 3 |
| CSV encoding (BOM, Thai UTF-8) | 2 |
| Export (headers, BOM, all types, invalid type 404) | 6 |
| Authorization (guest + no-merchant on import + export) | 4 |
| Analytics/logging (no exception) | 2 |
| Settings page (Data tab render) | 1 |
| Storage (temp file cleanup) | 1 |

All tests are mocked — no external service calls, no real queue dispatch.

---

## 13. Production Considerations

1. **Queue workers must be running.** Large imports are queued. If no worker is running, the import will never complete. Use `php artisan queue:work` in production or configure Supervisor/Horizon.

2. **Temp file cleanup.** Temp files are deleted after a successful or failed import. If a user abandons the wizard mid-flow (closes the browser), the temp file is orphaned. A scheduled cleanup command deleting `import-temp/` files older than 24 hours should be added in a future sprint.

3. **Memory.** Exports use streamed responses with 500-row chunks — memory usage is bounded regardless of dataset size. Imports parse the full CSV into memory for validation. A 10 MB CSV at 500 bytes/row ≈ 20,000 rows ≈ ~50 MB peak. This is acceptable for sync imports; large imports are queued.

4. **MySQL vs SQLite.** Phone normalisation (`preg_replace`) behaves identically. Date parsing relies on Carbon (PHP) not the database, so no dialect differences.

5. **Excel exports.** The UTF-8 BOM (`\xEF\xBB\xBF`) at the start of each CSV ensures Excel on Windows opens the file with correct encoding without any import wizard.

---

*Last updated: Sprint 6.4 — 2026-06-30*
