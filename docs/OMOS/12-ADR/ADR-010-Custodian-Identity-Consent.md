# ADR-010 — Custodian Model: Identity, Consent & Data Access

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-06) |
| **Date** | 2026-07-06 |
| **Author** | Claude Fable 5 (governance sprint GOV-001), decisions by Product Owner |
| **Supersedes** | Refines ADR-008 (wallet architecture stands; linking/consent semantics tightened) |
| **Related Documents** | [Product-Bible.md](../02-Product/Product-Bible.md), [Customer Wallet design package](../02-Product/Customer-Wallet/README.md), [ADR-008](./ADR-008-Phase-2-Customer-Wallet-Architecture.md) |

---

## Context

Phase 2 introduces a customer identity that spans merchants. That raises the platform's defining question: who owns the customer? The Product Owner has answered it definitively, and the answer constrains every data model and flow from Phase 2 onward.

## Decision (approved)

1. **OneMember is the custodian, not the owner.** Customers own and control their identity and what they share. Merchants own their business relationship with their customers. OneMember holds identity and consent in trust and supplies the connecting technology.
2. **One mobile phone number = one global OneMember identity.** Duplicate customer accounts must not exist. (Design consequence: the PH2-000 edge case E-06, which tolerated a second account on a second phone, is narrowed — one identity per phone is absolute; a person with two phones is a support-merge case, not a norm.)
3. **Linking is consent-gated, always.** Existing Member records at any merchant may be connected to a OneMember identity, and existing loyalty data surfaced in the Wallet, **only with explicit, clear, optional customer consent**. This supersedes the PH2-000 recommendation (BD-05) to auto-link on OTP-verified phone match: verification proves phone ownership, but **consent is still asked explicitly** before any link is created.
4. **Merchant loyalty sovereignty.** Each merchant keeps its own loyalty rules. Points are never combined, converted, or merged across merchants unless a future, explicitly approved business rule allows it.
5. **The OneMember Card / OneMember ID.** The customer's QR contains only a secure token or OneMember ID — never raw personal data. (Consistent with the PH2-000 QR design, Doc 05 §4; now a binding product rule, not just an implementation choice.)
6. **Scan-to-join.** A customer may present their OneMember Card at any merchant; the merchant scans it to enrol the customer without re-entering information — subject to the customer's consent for that specific merchant. This adds a **merchant-initiated, customer-present** join path alongside the PH2-000 QR-join and claim flows (design update in package Doc 08 §4b).
7. **Access follows subscription.** Merchants can access member data inside OneMember only while their account and subscription are active; lapsed/suspended merchants lose access until restored. The customer's own view of their memberships is unaffected.
8. **No automatic merchant-to-merchant data sharing.** Every cross-merchant membership join is customer-approved.

## Consequences

- PH2-001B/C specs must reflect: consent screen before every link (including claim), scan-to-join flow, and the subscription-gated merchant access check.
- BD-02 (identity anchor = phone) and BD-05 (dedup/link rule) are now **decided**; BD-05's answer is "explicit consent required", not auto-link.
- A merchant-access gate (subscription state) becomes an enforcement point alongside consent in `ConsentService`/policies.
- Support tooling for legitimate identity merges (two phones, changed number) moves from "Phase 2.1 maybe" to a required post-launch capability, since duplicates are now prohibited rather than tolerated.

## Options Considered

**Custodian model (chosen)** vs **platform-owns-customers** (network value accrues to OneMember; rejected — destroys merchant trust, contradicts the flywheel and PDPA posture) vs **pure per-merchant silos, no global identity** (status quo; rejected — no wallet, no network effect, fails Phase 2's purpose).
