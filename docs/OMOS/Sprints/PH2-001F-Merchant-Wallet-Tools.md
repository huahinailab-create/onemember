# PH2-001F — Merchant Wallet Tools: Invites, Counter Scan, Analytics

| Field | Value |
|---|---|
| **Status** | 🔲 Planning — blocked by PH2-001B |
| **Classification** | Type B |
| **Complexity** | Medium |
| **Dependencies** | PH2-001B (links exist); Counter Mode (MVP-007) |

## Objective
Close the merchant side of the loop: invite existing members to the wallet, scan wallet QRs at the counter, see linked-member impact.

## Files Expected to Change
- Merchant members list: "Invite to Wallet" (single + bulk) → signed one-time claim links (Design Doc 08 §4), `linked_via=merchant_invite`
- `Wallet QR at counter`: Counter Mode input accepts `OM1:` payload — parse member_code + validate TOTP suffix (`WalletQrService`)
- Merchant dashboard: linked-members count card + wallet join funnel (qr_join / claim / invite split)
- Invite email mailable (event-driven, respects member email presence)

## Database Impact
None beyond PH2-001B tables (reads + link provenance).

## Test Plan
- Invite link single-use + expiry + wrong-phone rejection tests
- Counter scan: valid/stale/forged OM1 payloads
- Funnel counts by provenance; tenant isolation on all new merchant reads

## Acceptance Criteria
1. Bulk invite queues ≤ 1 email per member with a valid one-time claim link.
2. Counter Mode records a purchase from a wallet QR scan as fast as member-code search today.
3. Merchant dashboard shows linked count + provenance funnel, scoped correctly.
