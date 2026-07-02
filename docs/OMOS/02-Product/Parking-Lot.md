# Parking Lot

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [MVP-Strategy.md](./MVP-Strategy.md), [00-Executive/Decision-Framework.md](../00-Executive/Decision-Framework.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md) |

---

## Purpose

The Parking Lot captures feature ideas and product requests that have been considered but explicitly deferred. An item in the Parking Lot is not rejected — it is waiting for the right time.

A Parking Lot entry must include:
- What the feature is
- Who requested it or why it was identified
- Why it is being parked (timing, dependency, missing spec)
- When to revisit (date or trigger condition)

The Parking Lot is reviewed every quarter by the Product Owner and ChatGPT CTO.

---

## Parking Lot

### PL-001 — Tier-Based Loyalty (Bronze/Silver/Gold)

**Feature:** Members accumulate points across all time and are assigned to tiers (Bronze, Silver, Gold) based on lifetime spend or visit count. Higher tiers unlock better rewards and benefits.

**Requested by:** Product Owner (identified as a high-value feature for salon and spa merchants)

**Why parked:** Requires a member history calculation engine and tier management UI that adds significant complexity to the member model. The current schema needs a decision (ADR) on how tier calculation is stored and refreshed before implementation.

**Revisit:** Phase 2, after Customer Wallet launches. Tier information belongs in the wallet profile.

---

### PL-002 — Multi-Location Management

**Feature:** A merchant with multiple branches can manage all locations from a single account. Members earn points across all locations. Analytics are available per location and in aggregate.

**Requested by:** Growing Restaurant and Retail Shop personas

**Why parked:** Requires architectural decisions about branch-level data isolation vs. aggregation, staff permissions per branch, and how member records link across branches. These are Type 3 decisions requiring ADRs.

**Revisit:** Phase 3, when Commerce and POS require multi-location architecture.

---

### PL-003 — Native Mobile App (Merchant)

**Feature:** A native iOS and Android app for merchants to manage their loyalty programme on the go.

**Requested by:** Product Owner (identified as important for on-the-go merchants)

**Why parked:** The web app (responsive Bootstrap 5) works on mobile browsers. A native app requires a separate development track (React Native or Flutter), separate release cycle, and app store approval process. The value does not justify the cost until merchant base is large enough to warrant the investment.

**Revisit:** Phase 4 when regional expansion justifies platform investment.

---

### PL-004 — WhatsApp Integration

**Feature:** Send loyalty notifications via WhatsApp instead of (or in addition to) email.

**Requested by:** Merchant personas in Thailand where WhatsApp is less common than LINE, but identified as important for Malaysian and Vietnamese market expansion.

**Why parked:** LINE integration is the priority for Thailand. WhatsApp requires a WhatsApp Business API account and approval. Revisit for Malaysia expansion.

**Revisit:** Phase 4 Malaysia expansion sprint.

---

### PL-005 — LINE OA Integration

**Feature:** Connect OneMember to a merchant's LINE Official Account to send loyalty notifications via LINE messages.

**Requested by:** Multiple Thai merchant personas (LINE is the dominant messaging platform in Thailand)

**Why parked:** High value for Thai market. Requires LINE API integration and handling of LINE user ID linking to OneMember member records. This is a meaningful engineering sprint with a clear architecture decision required first.

**Revisit:** Phase 2 — high priority immediately after Customer Wallet foundation is stable.

---

### PL-006 — Receipt OCR (Photo-Based Point Claiming)

**Feature:** Customers photograph their receipt. AI OCR reads the amount. Points are awarded automatically without staff action.

**Requested by:** Identified as a way to enable loyalty at merchants who cannot integrate with a POS.

**Why parked:** OCR accuracy for Thai receipts is uncertain. Fraud risk is high (customers re-submitting old receipts). The better solution is a lightweight POS integration (Phase 3) that removes the need for receipt OCR entirely.

**Revisit:** Revisit only if POS Lite is not adopted by the target merchant segments by Phase 3.

---

### PL-007 — Social Sharing and Referral

**Feature:** Members earn bonus points for sharing a merchant's QR code on social media, referring friends, or posting a review.

**Requested by:** Identified as a growth mechanic for customer acquisition.

**Why parked:** Social sharing mechanics create gameable behaviour (friends sharing with themselves, fake accounts). The quality of referred members may be lower than organically acquired members. Social features also risk drifting OneMember toward a social platform, which conflicts with the brand positioning.

**Revisit:** Revisit with evidence from similar platforms that the quality of socially-referred members justifies the implementation. Requires Product Owner decision.

---

## Parking Lot Archive

Items that have been implemented, permanently rejected, or superseded will be moved here.

*(Empty — no archived items yet)*
