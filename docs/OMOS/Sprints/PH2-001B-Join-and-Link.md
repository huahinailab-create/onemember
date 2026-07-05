# PH2-001B — Universal QR Join, Claim & Linking

| Field | Value |
|---|---|
| **Status** | 🔲 Planning — blocked by PH2-001A; BD-05 (dedup rule), BD-06 (merchant opt-out) |
| **Classification** | Type B |
| **Complexity** | Large (touches Member creation path — read-only reuse, no modification) |
| **Dependencies** | PH2-001A; design Docs 03/05/08 |

## Objective
A consumer scans a merchant's universal QR and joins (or claims an existing Member) in ≤ 15 s; merchant gets poster + linked-member visibility.

## Files Expected to Change
- `app/Services/WalletQrService.php` (HMAC sign/verify), `WalletLinkService.php` (join/claim/dedup per BD-05, E-02)
- Controllers: `Wallet/JoinController`, `Wallet/MembershipController` (list/detail/unlink)
- Views: W1 landing, W3 consent step (UI only — enforcement in 001C), W4 card list, W5 detail (reuses CustomerPortalService data shaping)
- Merchant app (additive): member-list wallet badge, settings `wallet_visible` toggle + poster PDF download (dompdf already a dependency? verify — else server-side print view)
- Events: `MembershipLinked` + listener email

## Database Impact
Migration 3: `customer_member_links` (unique member_id; partial unique customer+merchant). New Member creation goes through the existing Phase 1 rules/service — no schema or write-path changes to `members`.

## Test Plan
- Join new / claim existing / duplicate-phone (E-02) / already-joined (E-01) / archived-member (E-03)
- Signed-QR tamper rejection; expired/rotated slug
- Tenant isolation: customer A cannot see customer B links; merchant sees only own linked counts
- p50 join-flow timing assertion in browser test (staging)

## Acceptance Criteria
1. Poster QR → joined membership in ≤ 15 s (measured, staging).
2. Claim requires OTP-verified phone equality; provenance recorded (`linked_via`).
3. Member table untouched by linking; unlink is soft and reversible.
4. `wallet_visible=false` merchants unreachable via directory and join landing shows "not accepting wallet joins" (BD-06 semantics).
