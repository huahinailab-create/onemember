# OneMember — Development Phases

## Phase Overview

OneMember is built in three major phases. Each phase builds on the previous. No phase should be started before the previous is stable and in production.

```
Phase 1 ── Merchant Foundation      (Now → 6 months)
Phase 2 ── Customer Wallet          (6 → 18 months)
Phase 3 ── Regional Commerce Network (18 → 36 months)
```

---

## Phase 1 — Merchant Foundation

**Goal:** Give every small business in Thailand a professional loyalty and membership system they can launch in one afternoon.

**Status:** In Progress

### Phase 1 Milestones

| # | Milestone | Status |
|---|---|---|
| 1.1 | Merchant registration, onboarding, email verification | ✅ Complete |
| 1.2 | Loyalty programme creation (points + stamps) | ✅ Complete |
| 1.3 | Member management (add, view, archive, QR) | ✅ Complete |
| 1.4 | Reward creation and redemption | ✅ Complete |
| 1.5 | Customer self-service portal (web-based) | ✅ Complete |
| 1.6 | Subscription tiers + Stripe billing | ✅ Complete |
| 1.7 | Dashboard with key metrics | ✅ Complete |
| 1.8 | Settings (profile, preferences, branding) | ✅ Complete |
| 1.9 | CSV member import and data export | ✅ Complete |
| 1.10 | Developer tools and developer productivity suite | ✅ Complete |
| 1.11 | Birthday bonus and member notifications | ⬜ Planned |
| 1.12 | Point expiry (scheduled jobs) | ⬜ Planned |
| 1.13 | Campaign analytics dashboard | ⬜ Planned |
| 1.14 | Receipt QR purchase linking | ⬜ Planned |
| 1.15 | PromptPay integration | ⬜ Planned |
| 1.16 | POS-Lite (staff-facing sale entry) | ⬜ Planned |
| 1.17 | Merchant-defined point value (monetary equivalent) | ⬜ Planned |

### Phase 1 Exit Criteria

- All 1.x milestones complete
- Platform stable in production (< 0.1% error rate)
- At least 50 active merchants
- At least 500 loyalty members across all merchants
- Full Thai and English language support

---

## Phase 2 — Customer Wallet

**Goal:** Give customers a single loyalty wallet that works across all OneMember merchants. Make joining a new brand as frictionless as scanning a QR.

**Prerequisites:** Phase 1 complete and stable.

### Phase 2 Milestones

| # | Milestone | Status |
|---|---|---|
| 2.1 | Customer account registration (separate from merchant accounts) | ⬜ Planned |
| 2.2 | Universal QR scan → auto-join merchant | ⬜ Planned |
| 2.3 | Data consent management (per-merchant, per-data-type) | ⬜ Planned |
| 2.4 | Wallet dashboard (all brands, balances, rewards) | ⬜ Planned |
| 2.5 | Cross-brand promotions display | ⬜ Planned |
| 2.6 | Transaction history per brand | ⬜ Planned |
| 2.7 | Push notifications (web push, then native) | ⬜ Planned |
| 2.8 | Enterprise membership bridge API | ⬜ Planned |
| 2.9 | White-label mode for enterprise clients | ⬜ Planned |
| 2.10 | Privacy analytics (anonymized, consent-based) | ⬜ Planned |

### Phase 2 Exit Criteria

- At least 5,000 customer wallet accounts
- At least 3 brands per active wallet user on average
- Enterprise bridge in use by at least 1 client
- Consent management audited and legally reviewed for Thailand

---

## Phase 3 — Regional Commerce Network

**Goal:** Connect merchants and customers across Southeast Asia. Enable direct commerce that competes with Grab, TikTok, and Lazada — with the merchant owning the relationship.

**Prerequisites:** Phase 2 complete, customer wallet at scale.

### Phase 3 Milestones

| # | Milestone | Status |
|---|---|---|
| 3.1 | Product and menu listing | ⬜ Planned |
| 3.2 | Order management (pickup, delivery, shipping) | ⬜ Planned |
| 3.3 | Delivery radius and fee configuration | ⬜ Planned |
| 3.4 | PromptPay checkout (Thailand) | ⬜ Planned |
| 3.5 | Malaysia expansion (Malay language, DuitNow) | ⬜ Planned |
| 3.6 | Vietnam expansion (Vietnamese, VNPay) | ⬜ Planned |
| 3.7 | Singapore expansion (English, PayNow) | ⬜ Planned |
| 3.8 | Native iOS + Android app | ⬜ Planned |
| 3.9 | POS system integrations (API connector) | ⬜ Planned |
| 3.10 | LINE OA integration (Thailand) | ⬜ Planned |

### Phase 3 Exit Criteria

- Direct commerce GMV exceeds platform alternative fee cost for average merchant
- Active in at least 2 markets outside Thailand
- Native app launched with 10,000+ installs

---

## Architectural Decisions That Enable All Three Phases

These decisions must be made in Phase 1 even if the capability isn't used until Phase 2 or 3:

| Decision | Why It Matters Later |
|---|---|
| Clean service layer (no logic in controllers) | Native app and API clients need the same logic without HTML views |
| Multi-currency from the start | Phase 3 requires MYR, VND, SGD, USD without a rewrite |
| Multi-language infrastructure now | Thai first, but Malay and Vietnamese must be addable without code changes |
| Consent-first data model | Phase 2 wallet legally requires this; retrofitting is expensive |
| API-first thinking | Enterprise bridge and native app both need clean REST APIs |
| Payment provider abstraction | PromptPay, DuitNow, PayNow, Stripe all need to share interfaces |
