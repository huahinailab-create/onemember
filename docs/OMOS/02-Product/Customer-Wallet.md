# Customer Wallet

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Review — full design package delivered (PH2-000) |
| **Last Updated** | 2026-07-05 |
| **Related Documents** | [Product-Bible.md](./Product-Bible.md), [Enterprise-Bridge.md](./Enterprise-Bridge.md), [Commerce.md](./Commerce.md) |

---

## Purpose

Describes the customer-facing universal loyalty wallet: join-by-QR, cross-brand loyalty view, privacy controls, and notifications.

---

## Content

> **The complete Phase 2 design package now lives in [Customer-Wallet/](./Customer-Wallet/README.md)** (PH2-000, 2026-07-05): functional + technical specs, database, API, security, PDPA consent model, Apple/Google Wallet designs, identity & QR flows, diagrams, wireframes, risks, and [ADR-008 (Proposed)](../12-ADR/ADR-008-Phase-2-Customer-Wallet-Architecture.md).
> Implementation is gated on ADR-008 approval and business decisions BD-01…BD-10.

Customer Wallet will cover:
- Universal QR scan → join flow
- Customer account registration (separate from merchant accounts)
- Cross-brand loyalty dashboard (all brands in one view)
- Privacy and consent model (per-merchant, per-data-type)
- Reward redemption via wallet
- Push and email notifications
- Wallet settings and data export
