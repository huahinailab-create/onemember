# PH2-001E — Native Passes (Apple Wallet + Google Wallet)

| Field | Value |
|---|---|
| **Status** | 🔲 Planning — **blocked by BD-04** (Bible amendment, Apple/Google accounts, budget) |
| **Classification** | Type B + Type C budget element |
| **Complexity** | Large (external platform contracts) |
| **Dependencies** | PH2-001D; Apple Pass Type ID cert + APNs key; Google issuer + service account; design Doc 07 |

## Objective
"Add to Apple Wallet" / "Save to Google Wallet" from W5, with balance auto-updates.

## Files Expected to Change
- `app/Services/Passes/PassKitService.php`, `GoogleWalletService.php` (+ interfaces, fixture-based fakes)
- Models `WalletPass`, `ApplePassRegistration`; job `RefreshWalletPass`; command `wallet:refresh-passes`
- Controllers: pass issue endpoints + Apple PassKit web-service routes (Design Doc 04 §2)
- Merchant branding asset variants job (strip/logo sizes)
- `config/passes.php`; secrets via env only

## Database Impact
Migrations 5–6: `wallet_passes`, `apple_pass_registrations`.

## Test Plan
- pkpass structure/signing unit tests with fixture certs (never real keys in repo)
- Apple web-service contract tests (register/list/fetch/unregister/log)
- Google JWT claim snapshot tests; PATCH-on-balance-change test with faked HTTP
- Revocation on unlink/deletion (Doc 07 §4 matrix)

## Acceptance Criteria
1. Pass installs on real devices (staging manual gate) and updates within 5 min of a balance change.
2. Cert-expiry alarm fires 30 days ahead (ops test).
3. Passes revoke correctly on unlink, consent withdrawal, account deletion.
