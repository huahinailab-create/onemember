# CEO Decision Log

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [CTO-Decisions.md](./CTO-Decisions.md), [00-Executive/Vision.md](./00-Executive/Vision.md), [03-Business/Revenue-Model.md](./03-Business/Revenue-Model.md), [12-ADR/README.md](./12-ADR/README.md) |

---

## Purpose

This document records all significant decisions made by the Product Owner (CEO) about the direction, strategy, and business model of OneMember. Unlike ADRs (which record technical decisions) and sprint specifications (which record implementation decisions), this log captures business decisions that define what OneMember is and how it operates.

Every entry here has material consequences for the product roadmap, the revenue model, or the company's strategic direction. Decisions that are purely technical or operational live in `CTO-Decisions.md` or `12-ADR/`.

---

## Decision Log

### CEO-001 — OneMember is a Merchant Growth Platform, not a loyalty app

| Field | Value |
|---|---|
| **Decision ID** | CEO-001 |
| **Date** | 2026 (pre-OMOS) |
| **Status** | Approved |
| **Related** | [ADR-002](./12-ADR/ADR-002-Merchant-Growth-Platform.md) |

**Decision:** OneMember's identity is a Merchant Growth Platform. It is not a loyalty app, a CRM, or a marketing tool. Loyalty is the foundation, but the long-term platform includes Commerce, POS, Inventory, Procurement, Accounting, Analytics, and AI.

**Reason:** A loyalty app is a point solution with limited defensibility. A platform creates network effects, data compounding, and lock-in through value. The platform thesis is the only defensible long-term business.

**Impact:**
- Every sprint spec and product decision must be evaluated against the platform thesis, not just the loyalty feature
- The roadmap is structured in phases reflecting platform module addition
- Marketing and positioning use "merchant growth platform" language, not "loyalty app"

---

### CEO-002 — Hybrid Revenue Model

| Field | Value |
|---|---|
| **Decision ID** | CEO-002 |
| **Date** | 2026 (pre-OMOS) |
| **Status** | Approved |
| **Related** | [ADR-003](./12-ADR/ADR-003-Hybrid-Revenue-Model.md), [Revenue-Model.md](./03-Business/Revenue-Model.md) |

**Decision:** OneMember operates a hybrid revenue model with four streams: (1) merchant subscription, (2) enterprise integration fees, (3) commerce transaction fees, (4) future privacy-preserving analytics.

**Reason:** Subscription alone creates fragility — any platform disruption (pricing, competition) removes all revenue. Multiple streams that each scale with platform adoption create resilience. Commerce transaction fees align OneMember's incentives with merchant success.

**Impact:**
- Commerce module is required for the business model, not optional
- Enterprise Bridge must be designed to support fee-based API access
- Analytics data products require explicit merchant consent framework before they can be built
- Customer app is permanently free — this is a structural decision, not a temporary promotion

---

### CEO-003 — Customer Wallet Is Free Forever

| Field | Value |
|---|---|
| **Decision ID** | CEO-003 |
| **Date** | 2026 (pre-OMOS) |
| **Status** | Approved |
| **Related** | [Revenue-Model.md](./03-Business/Revenue-Model.md), [00-Executive/Vision.md](./00-Executive/Vision.md) |

**Decision:** The Customer Wallet (Phase 2 consumer app) is permanently free for consumers. No freemium upsell. No premium tier for consumers.

**Reason:** Consumer adoption drives merchant value. Any friction — including a paywall — reduces consumer adoption and therefore reduces merchant value. The consumer app generates no direct revenue; it generates indirect revenue by making merchant subscriptions more valuable.

**Impact:**
- Consumer app development is funded entirely by merchant subscription revenue
- Revenue model must generate sufficient margin from merchant and enterprise streams to fund wallet development
- No consumer revenue line in financial modelling

---

### CEO-004 — Thailand First, Regional Second

| Field | Value |
|---|---|
| **Decision ID** | CEO-004 |
| **Date** | 2026 (pre-OMOS) |
| **Status** | Approved |
| **Related** | [ADR-006](./12-ADR/ADR-006-Thailand-First-Strategy.md), [Known-Constraints.md](./Known-Constraints.md) |

**Decision:** OneMember's primary market is Thailand. Regional expansion (Malaysia, Vietnam, Singapore) begins only after Phase 1 is stable with 1,000+ active paying merchants.

**Reason:** Trying to localise for multiple markets simultaneously while still finding product-market fit in the first market dilutes focus and increases cost. Thailand is large enough to prove the model. The architecture must be built to support regional expansion without requiring a rewrite.

**Impact:**
- All Phase 1 features are built for Thailand first (Thai language, THB, PromptPay, Thai accounting)
- Localisation architecture (lang/ files, currency abstraction, timezone support) must be in place from the beginning even if only used for Thai initially
- Regional market timelines are Phase 4 — not before

---

### CEO-005 — Merchant Data Belongs to the Merchant

| Field | Value |
|---|---|
| **Decision ID** | CEO-005 |
| **Date** | 2026 (pre-OMOS) |
| **Status** | Approved |
| **Related** | [Core-Values.md](./00-Executive/Core-Values.md), [Revenue-Model.md](./03-Business/Revenue-Model.md) |

**Decision:** OneMember does not sell, share, or monetise individual merchant customer data without explicit, informed, and revocable merchant consent. This applies to every data product, every analytics feature, and every third-party integration.

**Reason:** Merchant trust is the foundation of the business. If merchants discover we sold their customer data, the reputational damage would be existential. Privacy is also the right thing to do.

**Impact:**
- Analytics data products (Revenue Stream 4) require explicit opt-in per merchant — they cannot be built on a "default opt-in" basis
- Third-party integrations that require sharing member data must present consent to merchants before activation
- This decision is not reversible. Future product proposals that require selling data without consent must be rejected regardless of revenue opportunity.

---

### CEO-006 — Security is Non-Negotiable

| Field | Value |
|---|---|
| **Decision ID** | CEO-006 |
| **Date** | 2026 |
| **Status** | Approved |
| **Related** | [Known-Constraints.md](./Known-Constraints.md), [11-Standards/Security-Standards.md](./11-Standards/Security-Standards.md) |

**Decision:** Security features are never disabled, weakened, or bypassed. Email verification remains mandatory. Developer tools are never exposed in production. Secrets are never hardcoded.

**Reason:** OneMember holds sensitive merchant and customer data. A breach would be both legally consequential (Thailand PDPA) and reputationally fatal. Security is an invariant, not a trade-off.

**Impact:**
- Claude Developer cannot disable security features regardless of sprint spec
- `DEV_TOOLS_ENABLED` is never set to true in production
- `APP_DEBUG` is always false in production
- This decision is enforced by EXECUTE.md and the Sprint Review checklist

---

### CEO-007 — Never Deploy Without Product Owner Approval

| Field | Value |
|---|---|
| **Decision ID** | CEO-007 |
| **Date** | 2026 |
| **Status** | Approved |
| **Related** | [EXECUTE.md](./EXECUTE.md), [11-Standards/Deployment-Standards.md](./11-Standards/Deployment-Standards.md) |

**Decision:** No code reaches production without explicit Product Owner approval. The CTO sprint review (technical quality) and the PO deployment approval (business decision) are separate, mandatory gates.

**Reason:** Deployment at the wrong time — before a merchant event, during peak hours, before a public announcement — can cause significant merchant disruption even when the code is technically correct.

**Impact:**
- Claude Developer never deploys independently
- Every sprint ends with a completion report awaiting CTO review, then PO approval before deployment
- "The sprint is done" does not mean "it's deployed"
