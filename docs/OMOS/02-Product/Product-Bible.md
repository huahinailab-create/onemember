# Product Bible

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 0.2.0 |
| **Status** | In Progress |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Vision.md](../00-Executive/Vision.md), [Mission.md](../00-Executive/Mission.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md), [Glossary.md](./Glossary.md), [Product-Terminology.md](./Product-Terminology.md) |

---

## Purpose

The Product Bible is the master reference for what OneMember is, who it serves, and how every product area works. It is the most important product document in OMOS.

Every sprint spec, architecture decision, and brand guideline must be consistent with the Product Bible. When there is a conflict, the Product Bible wins — unless the Product Owner explicitly approves an exception and updates this document.

---

## Introduction

OneMember is a **Merchant Growth Platform**.

It is not a loyalty app. It is not a CRM. It is not a marketing tool. It is not a POS system. It is a platform that brings all of these capabilities together in service of one goal: **helping merchants build lasting, profitable relationships with their customers**.

The platform is built in phases. In Phase 1, OneMember is the Merchant SaaS — a complete, professional loyalty platform for small and medium businesses. In Phase 2, it becomes a consumer platform through the Customer Wallet. In Phase 3, it becomes a commerce platform. In Phase 4, it becomes a regional operating system for merchant-customer relationships across Southeast Asia.

Every module connects. Loyalty drives repeat visits. Repeat visits drive commerce. Commerce drives inventory management. Inventory drives procurement. All of it drives analytics. Analytics drives AI. AI drives merchant decisions. Merchant decisions drive loyalty. This is the flywheel.

---

## Who OneMember Serves

### Primary User: The Merchant
A small or medium business owner in Southeast Asia who wants to grow their business by building better relationships with their customers. They are not technical. They do not have a marketing team. They need results, not complexity.

### Secondary User: The Consumer (Member)
A customer who shops at OneMember-powered merchants. They want to earn rewards effortlessly and not be buried in apps and paper cards. They want to control their data. They want convenience.

### Tertiary User: The Enterprise (Phase 2+)
A large organisation that wants to connect their existing CRM, POS, or membership system to OneMember's customer wallet infrastructure.

---

## Platform Modules

For detailed module specifications, see the individual documents in `docs/OMOS/02-Product/`:

| Module | Status | Document |
|---|---|---|
| Merchant Platform | Phase 1 — Live | [Merchant-Platform.md](./Merchant-Platform.md) |
| Customer Wallet | Phase 2 — Planned | [Customer-Wallet.md](./Customer-Wallet.md) |
| Enterprise Bridge | Phase 2 — Planned | [Enterprise-Bridge.md](./Enterprise-Bridge.md) |
| Commerce | Phase 3 — Planned | [Commerce.md](./Commerce.md) |
| POS Lite | Phase 3 — Planned | [POS.md](./POS.md) |
| Inventory | Phase 3 — Planned | [Inventory.md](./Inventory.md) |
| Procurement | Phase 4 — Future | [Procurement.md](./Procurement.md) |
| Thailand Accounting | Phase 4 — Future | [Accounting.md](./Accounting.md) |
| Analytics & Intelligence | Continuous | [Analytics.md](./Analytics.md) |
| AI Features | Continuous | [AI-Features.md](./AI-Features.md) |

---

## Product Rules

These rules apply to every product decision made about OneMember. They are derived from the [Product Principles](../00-Executive/Product-Principles.md).

1. Every feature must create merchant value.
2. Every feature must reduce customer friction — or at minimum not increase it.
3. Every feature must fit the long-term architecture as documented in `docs/OMOS/12-ADR/`.
4. Every feature must be testable and maintainable by a future developer.
5. Every feature must have a defined success metric before it ships.

---

## What the Product Bible Is Not

The Product Bible is not a sprint specification. Sprint specifications are in `docs/OMOS/SprintSpecification.md` and the `ai/` folder.

The Product Bible is not a technical architecture document. Architecture lives in `docs/OMOS/10-Architecture/` and `docs/OMOS/12-ADR/`.

The Product Bible is not a marketing document. Positioning lives in `docs/OMOS/00-Executive/Product-Positioning.md`.

The Product Bible is the authoritative answer to: **what is OneMember, and why does each part of it exist?**

---

## Document Status

This document is in progress. The module-level documentation (individual product area files) will be completed in Sprint AI-02C and subsequent sprints. The introduction and structure above represent the approved framework.

Sections to be completed:
- Per-module feature specifications with user stories
- Edge case handling per module
- Cross-module interaction rules
- Accessibility and localisation requirements per module
