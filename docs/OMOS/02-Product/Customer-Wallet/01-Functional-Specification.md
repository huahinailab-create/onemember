# 01 — Functional Specification

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Product Summary

The Customer Wallet is the consumer-facing surface of OneMember (Bible: "a single consumer can see and manage all their OneMember loyalty memberships in one place"). One account, every OneMember merchant, one QR.

**In scope (Phase 2.0):** customer account, universal QR join, cross-merchant dashboard, per-merchant consent, reward redemption view, email notifications, data export, Apple/Google wallet passes (pending BD-04).

**Out of scope (explicitly):** payments, commerce, chat, social features (Non-Roadmap list), Enterprise Bridge implementation (separate spec PH2-003), native mobile apps (Phase 4), tier-based loyalty (PL-001 — revisit after wallet launch).

## 2. Actors

| Actor | Definition (Glossary) |
|---|---|
| Customer | A consumer with a wallet account. May be linked to 0..n Member records. |
| Member | Phase 1 per-merchant loyalty record. Unchanged. |
| Merchant | Business running a loyalty programme. Gains wallet-join QR + linked-member indicators. |
| Staff | Uses Counter Mode; can scan wallet QR exactly like member-code search today. |

## 3. User Stories

### Account & identity
- **W-01** As a consumer, I create a wallet account with my phone number so I don't manage passwords. *(auth method = BD-02)*
- **W-02** As a customer, I sign in on a new device and see all my memberships.
- **W-03** As a customer, I can delete my account; my per-merchant Member records survive under the merchant's own lawful basis, but the link and wallet data are erased (PDPA).

### Joining merchants
- **W-10** As a consumer, I scan a merchant's universal QR and join their programme in under 15 seconds (Bible: "fast enough for any customer to join").
- **W-11** As an existing Member (created by the merchant in Phase 1), I scan the same QR and the wallet offers to claim my existing record because my verified phone matches *(BD-05)*.
- **W-12** As a customer, joining merchant B from inside the wallet requires exactly one tap plus consent — my profile fields are reused.

### Wallet dashboard
- **W-20** As a customer, I see every membership as a card: merchant logo/brand colours (existing MerchantBrandingService), points/stamps balance, tier of progress toward the next reward.
- **W-21** As a customer, I open a membership and see transaction history, available rewards, and my member QR — the same data the Phase 1 customer portal shows today, unified.
- **W-22** As a customer, I present one universal QR at any linked merchant's counter; staff scan resolves to the correct Member record for that merchant.

### Consent & privacy
- **W-30** As a customer, I grant/withdraw consent per merchant and per data type (profile, birthday, contact-for-marketing) at any time.
- **W-31** As a customer, withdrawing marketing consent stops that merchant's win-back/birthday emails to me within 24 hours.
- **W-32** As a customer, I download all my wallet data as a machine-readable file (PDPA data portability).

### Notifications
- **W-40** As a customer, I receive an email when I earn points, when a reward becomes claimable, and on my birthday — deduplicated with the Phase 1 member emails (MVP-006) so I never get two emails for one event.

### Native passes (pending BD-04)
- **W-50** As a customer, I add a membership card to Apple Wallet / Google Wallet; the pass shows live balance and my member QR, and updates when my balance changes.

## 4. Merchant-facing additions

- **M-01** Merchant dashboard shows which members are wallet-linked (badge on member list; count on dashboard).
- **M-02** Merchant settings gains `wallet_visible` toggle *(BD-06)* and a printable universal join QR poster (A5/A4 PDF, brand colours).
- **M-03** Counter Mode accepts wallet universal QR scans (input already accepts member code paste; QR resolves to member code).

## 5. Edge Cases

| # | Case | Behaviour |
|---|---|---|
| E-01 | Customer scans QR of a merchant they already joined | Open that membership card, no duplicate link |
| E-02 | Phone matches 2+ Member records at one merchant (data entry duplicates) | Link the most recently active; flag others to merchant as "possible duplicates" — never auto-merge (merchant owns Member data) |
| E-03 | Merchant archives a linked Member | Card shows "archived by merchant"; history read-only; consent intact until withdrawn |
| E-04 | Merchant subscription expires | Cards remain visible read-only; joins disabled for that merchant |
| E-05 | Customer changes phone number | Re-verify via OTP on both numbers where possible; else support flow. Links persist (link is by customer_id, not phone) |
| E-06 | Same person creates two wallet accounts (two phones) | Permitted; no auto-merge. Support-driven merge tool is Phase 2.1 |
| E-07 | Consent withdrawn, then re-granted | New consent row (versioned audit trail), never update-in-place |
| E-08 | Universal QR screenshot shared publicly | Join QR is public by design (poster). Member QR rotates (existing regenerate mechanic) and is single-customer |

## 6. Success Metrics (Bible rule 5)

| Metric | Target (first 6 months) |
|---|---|
| Wallet accounts created | 5,000 |
| Avg memberships per customer | ≥ 2.0 (network effect proof) |
| QR join conversion (scan → joined) | ≥ 60% |
| Join time p50 | ≤ 15 s |
| Consent withdrawal rate | < 5% (trust proxy) |
| Wallet-attributed repeat-visit lift | +10% vs unlinked members |
