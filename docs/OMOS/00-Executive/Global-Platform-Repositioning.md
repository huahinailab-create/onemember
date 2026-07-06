# Global Platform Repositioning (GLOBAL-001)

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active — records Product Owner strategic direction of 2026-07-06 (supersedes previous strategic direction where conflicts exist) |
| **Last Updated** | 2026-07-06 |
| **Author** | Claude Fable 5 (repositioning sprint GLOBAL-001) |
| **Related Documents** | [Product-Bible.md](../02-Product/Product-Bible.md) (v2.0.0), [ADR-012](../12-ADR/ADR-012-Modular-Platform-Core-Apps-Extensions.md), [ADR-010](../12-ADR/ADR-010-Custodian-Identity-Consent.md), [ADR-011](../12-ADR/ADR-011-Commerce-Principles-Phase-4.md), [Domain-Model.md](../10-Architecture/Domain-Model.md) |

---

## 1. Mission Shift

OneMember evolves from a **Thailand-focused loyalty application** into a **global merchant membership platform**. OneMember supports merchants from any country; merchants acquire their own customers; OneMember provides software, never local operations. No design may require OneMember to open offices per country.

## 2. Core Philosophy (approved — the NOT list)

OneMember is **NOT**: a marketplace · a payment processor · a logistics company · a bank · an accounting system · a POS company · an ERP.

OneMember **IS** a merchant-first **Membership, Loyalty, Customer Identity and Engagement Platform**. Merchants remain completely independent. Customers control their identity. OneMember provides the technology.

**Ownership split (approved):**

| Merchants own | OneMember owns |
|---|---|
| Products, prices, loyalty rules, campaigns*, payments, customer service, inventory, fulfilment, taxes, invoices | Identity Platform, Membership Platform, Loyalty Engine, Rewards Engine, Campaign Engine, Customer Engagement, Analytics, APIs, AI Foundation |

*Merchants own their campaign content/rules; OneMember owns the campaign engine technology.

## 3. Three-Layer Platform Architecture (approved — ADR-012)

```
┌──────────────────────────────────────────────────────────┐
│ LAYER 3 — COUNTRY EXTENSIONS (optional, per country)     │
│ TH: PromptPay · MY: DuitNow · MM: KBZ Pay · IN: UPI      │
│ VN: VNPay · Global: Stripe, PayPal                       │
├──────────────────────────────────────────────────────────┤
│ LAYER 2 — ONEMEMBER APPS (optional, per merchant)        │
│ Commerce · POS · Accounting · Procurement · Restaurant   │
│ Hotel PMS · Appointment Booking · CRM · Inventory        │
│ AI Marketing · Shipping · Gift Cards · Coupons           │
│ Staff Management · Payroll · ERP Connectors              │
├──────────────────────────────────────────────────────────┤
│ LAYER 1 — ONEMEMBER CORE (every merchant, global)        │
│ Merchant Management · Customer Identity · Membership     │
│ Loyalty · Rewards · Campaigns · Analytics · Notifications│
│ Authentication · APIs · AI Foundation                    │
└──────────────────────────────────────────────────────────┘
```

### Layer 1 — OneMember Core definition
The Core is what every merchant gets: merchant management, customer identity (ADR-010), membership, loyalty, rewards, campaigns, analytics, notifications, authentication, APIs, and the AI foundation. **The Core must remain lightweight and global.** Country-specific code is avoided in Core whenever possible. The Core never becomes a monolithic everything-application.

### Layer 2 — OneMember Apps architecture
An official extension ecosystem (the concept previously referred to as *OneStore* — that name is retired). Apps are optional; merchants install only what they need. **Commerce is NOT Core — it is an installable App.** Installing Commerce grants: product catalogue, categories, images, inventory, shopping cart, Merchant Storefront, pickup, merchant delivery, shipping, order management. Merchants without Commerce use OneMember purely for membership and loyalty.

Illustrative install profiles (approved):
- Coffee shop → Membership + Rewards
- Restaurant → Membership + Rewards + Commerce + QR Menu
- Hotel → Membership + Hotel Connector
- Retail → Membership + Commerce + Inventory

### Layer 3 — Country Extension architecture
Country-specific functionality (payment QR display formats, local compliance, local channels) ships as extensions, never hardcoded into Core. Extensions become available to merchants based on their chosen country. Payment extensions only ever configure **display of the merchant's own payment identity** — the ADR-011 no-money-touch rule applies at every layer.

### Technical shape of modularity (documented intent, not code yet)
Modularity is **product modularity first, code modularity second**: the existing Laravel monolith (ADR-004/008/009 stand) gains a per-merchant `installed_apps` concept and app-scoped feature gating; Apps begin as first-party modules inside the monolith with clean namespaces and their own migrations/routes/policies, extractable later along the SCALE-000 §1.19 seams. No microservices are created by this repositioning (Scalability Review rejection list still applies).

## 4. Merchant Storefront (Commerce App)

The Merchant Storefront belongs to the Commerce App. Merchant controls products, pricing, inventory, delivery, shipping, pickup, customer service. OneMember provides identity, ordering, membership, loyalty, engagement, analytics. All ADR-011 rules apply unchanged (merchant of record, direct payment, no fees, merchant fulfilment, restaurant delivery radius).

## 5. Payment Model (unchanged, re-affirmed)

Payments always go directly to merchants. OneMember never receives, holds, settles, transfers, or escrows money. Merchant QR codes are displayed; merchants issue invoices, handle refunds, own taxes.

## 6. Business Model & Pricing Direction (direction approved; detail = PO decision)

Revenue = **subscriptions only**. No commission, no GP, no transaction fees.

Pricing direction (detailed tiers require Product Owner approval — **DR-32**):
- **Free**: up to 100 members.
- Paid plans scale on: members, features, automation, API access, **apps installed**.

## 7. Customer Identity (unchanged, re-affirmed — ADR-010, live since PH2-001A)

One phone = one OneMember Identity; no duplicates; identity global, memberships/loyalty/rewards local; points never merge; token-only QR; scan → consent → connect; no repeated registration.

## 8. Localization Strategy (approved)

- **Language is configurable, never browser-sniffed** (browser detection was already removed in DECISION-067; this extends the rule globally).
- Merchant chooses **Country, Language, Currency, Timezone** — at onboarding and in Merchant Settings.
- Customer language selection arrives with the customer-facing settings surface (future).
- Country choice determines which Country Extensions are offered.
- Engineering groundwork already in place: locale files with completeness tests, `app.default_currency` config (TD-005 resolved), E.164 phones, per-customer `locale` column.

## 9. Onboarding Strategy (approved — spec for a future sprint)

Onboarding collects: Business Name · **Country** · Currency · Timezone · **Preferred Language** · Industry · Business Size · Loyalty Preference · **Plan** · **Terms Acceptance**. Future onboarding recommends Apps by business type (e.g., Restaurant → Commerce, QR Menu, Table Ordering, Restaurant Analytics). Current onboarding (5 steps, Thai-centric defaults) is superseded as documentation; implementation is a scheduled sprint (see §14).

## 10. Legal (approved requirement; wording = counsel)

Onboarding must include acceptance of: Subscription Terms, Payment Terms, Trial Terms, Free Plan Limits, Upgrade Rules, Privacy Policy, Acceptable Use, Merchant Responsibilities. All legal wording is **"Draft pending legal review"** until counsel signs off (extends BD-07 into a general legal workstream — **DR-33**).

## 11. Product Bible Audit (Thailand-only assumptions & misplacements found)

| Finding | Class | Correction |
|---|---|---|
| Bible/marketing framed audience as "Southeast Asia" / Thailand-first | Thailand-only assumption | Bible v2.0.0: global platform, Thai market remains launch market |
| "Thailand-first, region-ready" principle (Master Roadmap §1) | Assumption | Reframed: "Global platform, launch-market Thailand" — localization by configuration |
| PromptPay treated as roadmap feature of Core | Belongs in Extension | Thailand Country Extension |
| Commerce/POS/Inventory as Phase-4 *platform modules* | Belongs in Apps | Commerce App, POS App, Inventory App (availability still targeted Phase 4) |
| Accounting/Procurement as future Core modules | Belongs in Apps | Accounting App, Procurement App |
| Hotel/appointment verticals unaddressed | Gap | App ecosystem examples recorded |
| Gift cards / coupons (DR-12/DR-14) | Belongs in Apps | If ever approved, they are Apps, not Core |
| Regional expansion as a *phase* with per-country pushes (DR-31) | Architecture conflict with global-software model | **DR-31 resolved by this directive:** no regional "phase"; any-country support via configuration + Country Extensions; no local-operations designs |
| Onboarding hardcodes THB/Asia-Bangkok defaults, no country/language/plan/terms steps | Thailand-only assumption | Onboarding redesign spec (§9), sprint queued |
| `SubscriptionPlan` free tier limits vs "Free up to 100 members" | Pricing conflict to reconcile | DR-32 (PO pricing decision) |
| Glossary "Points Programme… Thai SMEs" flavour text | Cosmetic | Neutralised in DOC-002 pass |

**Features that should NOT be built (confirmed/no change):** marketplace mechanics, payment processing/escrow, logistics operations, banking/lending, OneMember-run accounting service (an Accounting *App* is merchant tooling, not OneMember-as-accountant), full ERP, social feeds, ad network, data resale, per-merchant custom code.

## 12. Existing Features That Remain Valid (all of Phase 1 + PH2-001A)

Merchant management/onboarding/billing, members, campaigns, rewards, transactions, counter mode, launch kit, analytics/intelligence, admin platform, notifications/email rails, localization infrastructure, Identity Platform (customers/links/consents/card/scan-to-join), corporate site. **Nothing shipped is discarded** — everything shipped is Core by definition except the Launch Kit's commerce-adjacent pieces (none exist) — no rework required.

## 13. Features That Move Into Apps (documentation repositioning)

Commerce/Storefront (incl. QR menu, table ordering), POS Lite, Inventory, Accounting, Procurement, Gift Cards*, Coupons*, Shipping integrations, Hotel PMS connector, Appointment Booking, Staff Management*, Payroll, ERP Connectors, AI Marketing (advanced tiers; the AI *foundation* stays Core). *Where a DR gate existed (DR-12/14/18) it still applies — App placement doesn't approve the feature.*

## 14. Migration Plan From Current Architecture

Documentation-level plan; no code in this sprint:

1. **Now (this commit):** Bible v2.0.0, ADR-012, roadmap re-labelling, decision register.
2. **Next implementation sprint (recommended): CORE-001 — Global Onboarding & App Framework Foundation**
   - Onboarding v2: country/language/currency/timezone/industry/size/plan/terms (§9, §10)
   - `installed_apps` merchant concept + app registry config + gating helper (`merchant->hasApp('commerce')`)
   - Terms-acceptance capture (versioned, audited), legal copy marked draft
   - No behavioural change for existing merchants (migration defaults: country TH, current locale/currency, all-current-features-as-Core)
3. **Then:** wallet auth track (BD-09) and PH2-001B–F continue unchanged — identity is Core.
4. **Phase 4 planning** builds Commerce as the first real App on the framework, proving the model.
5. **Extraction later, only on load evidence** (SCALE-000 seams; unchanged).

## 15. Platform Philosophy (summary for every future contributor)

> Every merchant installs only what they need. The Core stays small, global, and boring. Growth comes from Apps, not from bloating Core. Money never touches OneMember. Identity belongs to customers, relationships belong to merchants, technology belongs to us — and that's the whole business.
