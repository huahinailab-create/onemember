# 11 — Risks & Future Scalability

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Risk Register

| ID | Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|---|
| R-01 | **Cold-start:** wallet ships, consumers don't adopt (avg memberships < 1.2) | Med | High | Claim flow makes existing members instant users; staff till-prompt scripts; success metrics reviewed at 90 days with kill/iterate decision |
| R-02 | SMS OTP cost blow-up / pumping fraud | Med | Med | Doc 05 §3 controls; daily spend alarm; BD-09 provider with Thai-number filtering |
| R-03 | PDPA non-compliance in consent copy | Low | Critical | BD-07 legal review is a launch gate, not a nice-to-have |
| R-04 | Wrong-person claim (SIM recycling: new owner of a recycled phone claims old member's points) | Low | Med | Claim shows masked name for confirmation; merchant notified on claim; support reversal flow; consider 90-day inactivity + name-mismatch heuristic hold |
| R-05 | Apple/Google account, cert, or review delays | Med | Low | Passes are additive (BD-04 can defer to 2.1 without touching core wallet) |
| R-06 | In-app browsers (LINE/FB) break OTP autofill/camera | High | Med | Manual code entry always available; "open in browser" fallback banner; test matrix includes LINE in-app explicitly |
| R-07 | Duplicate-member data quality at merchants pollutes claim flow | Med | Med | E-02 rule (link most recent, flag rest); merchant duplicate report |
| R-08 | Wallet load degrades merchant app (shared monolith/DB) | Low | Med | Same box initially is fine at Phase 2 scale (§2); rate limits; slow-query monitoring; split path defined below |
| R-09 | Scope creep into Enterprise Bridge during build | Med | Med | Bridge is a separate spec (PH2-003); wallet API deliberately customer-credentialed only |
| R-10 | Team burden: consumer support channel doesn't exist yet | Med | Med | BD list → PO must staff/route consumer support before launch (support@ exists; add wallet FAQ + in-app help) |

## 2. Scalability Path

**Phase 2 scale assumption:** 5k–50k customers, ≤ 3 links each, ≤ 10 balance events/customer/month — trivially inside a single MySQL + database queue.

Growth levers, in order, none requiring redesign:

1. **Read paths:** `Cache::remember` on wallet card list (invalidated by `WalletBalanceChanged`) — same pattern as MerchantIntelligence (PERF-001).
2. **Queue:** database → Redis driver is a config change (CTO-004 names database for current production; revisit at sustained >10 jobs/s).
3. **Pass updates:** already async + idempotent; horizontal workers scale linearly.
4. **DB:** links/consents are append-mostly and narrow; partitioning consents by year if audit table exceeds ~50M rows.
5. **Service split (only if needed, Phase 3+):** the wallet domain group + services are already a clean seam; extracting `wallet.onemember.co` to its own app sharing the DB, then its own DB with an events bridge, is a documented option in ADR-008 — not a commitment.

## 3. Forward Compatibility

| Future item | How this design accommodates it |
|---|---|
| Enterprise Bridge (PH2-003) | Bridge maps external memberships to `customer_member_links` rows with a `linked_via=bridge` provenance value (enum extensible) |
| Tier-based loyalty (PL-001) | Tier lives on Member/Campaign side; wallet card already renders "progress" abstractly |
| LINE OA notifications (PH2-002) | Notification dispatch already event-driven; LINE becomes another listener channel + consent `data_type` addition |
| Native apps (Phase 4) | Wallet API v1 is the app's backend as-is (Sanctum tokens) |
| Commerce (Phase 3) | Customer identity + consent rails are the prerequisite Commerce assumes; order events reuse `WalletBalanceChanged` |
| Regional (Phase 4) | Phone E.164 + locale per customer from day one; consent model is jurisdiction-parameterisable (`consent_version` per market) |
