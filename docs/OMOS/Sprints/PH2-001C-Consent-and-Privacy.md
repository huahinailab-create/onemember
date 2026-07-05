# PH2-001C — Consent Engine & Privacy Centre (PDPA)

| Field | Value |
|---|---|
| **Status** | 🔲 Planning — blocked by PH2-001B; BD-07 (legal copy), BD-10 (retention) |
| **Classification** | Type C elements (privacy/legal) — CEO sign-off on copy + retention |
| **Complexity** | Medium-Large |
| **Dependencies** | PH2-001B; legal-reviewed consent text v1 |

## Objective
Versioned, append-only consent with a single enforcement path; Privacy Centre (W6); PDPA export and account deletion.

## Files Expected to Change
- `app/Services/ConsentService.php` (sole read/write path), `Wallet/ConsentController`, `Wallet/PrivacyController`
- Enforcement hooks: `MemberEmailSubscriber`, `EmailEventSubscriber` (winback), profile/birthday sync job
- Jobs: `PropagateConsentWithdrawal`, `BuildCustomerExport`, `EraseCustomerAccount` (7-day cooling-off), `wallet:anonymise-inactive` command
- Views: W3 (now enforced), W6 Privacy Centre, deletion + export screens
- `lang` consent copy keyed by `consent_version`

## Database Impact
Migration 4: `consents` (append-only). No updates/deletes ever issued by code — enforced by model guarding + test.

## Test Plan
- Latest-state resolution across grant→withdraw→re-grant sequences
- Withdrawal suppresses merchant marketing mail within job window (time-travel test)
- Export JSON schema snapshot test; deletion severs links, keeps Member records, hard-deletes wallet PII after cooling-off
- Append-only guard: any UPDATE attempt throws

## Acceptance Criteria
1. All four data-type consents enforced at every boundary listed in Design Doc 06 §4.
2. Consent history is complete and immutable (audit query per customer+merchant).
3. Export delivered by signed link; deletion flow matches Doc 06 §5 exactly.
4. Legal-approved copy versions rendered in TH/EN (BD-07 gate).
