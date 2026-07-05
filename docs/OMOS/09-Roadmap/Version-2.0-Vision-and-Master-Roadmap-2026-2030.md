# OneMember Version 2.0 Vision & Master Roadmap (2026–2030)

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Review — strategic proposals require Product Owner ratification (§14) |
| **Last Updated** | 2026-07-05 |
| **Author** | Claude Fable 5 (sprint VISION-001) |
| **Related Documents** | [Product-Bible.md](../02-Product/Product-Bible.md) · [Long-term-Roadmap.md](./Long-term-Roadmap.md) · [Roadmap.md](./Roadmap.md) · [Customer-Wallet package](../02-Product/Customer-Wallet/README.md) · [Scalability-Review-2026-07](../10-Architecture/Scalability-Review-2026-07.md) · [Operations-Manual](../06-Operations/Operations-Manual.md) · [14-Version-2.0-Ideas](../../14-Version-2.0-Ideas.md) · [Bible-Gap-Review](../02-Product/Bible-Gap-Review-2026-07.md) |

**Reading rule:** Everything here is a blueprint, not a commitment. Items marked **[DR-n]** require a Product Owner decision recorded in the Decision Register (§14) before any spec is written. Nothing in this document overrides the Product Bible; where this document proposes going beyond the Bible, it says so explicitly.

---

## 1. Executive Vision

### Mission (proposed — Mission.md is currently unwritten; ratification = DR-01)
> OneMember helps small and medium merchants in Thailand grow by turning every customer visit into a lasting relationship — professional loyalty, honest data, and direct customer connections, without needing a marketing team.

### Long-term vision (proposed, DR-01)
> By 2030, OneMember is Thailand's leading customer-engagement ecosystem: the wallet in every consumer's phone, the growth platform behind every neighbourhood merchant, and the trusted bridge between them — built on consent, not surveillance.

### Core principles (restating what OMOS already holds; no new invention)
1. **Merchant value first** — every feature must create merchant value (Bible Product Rule 1).
2. **Customer friction never increases** (Bible Rule 2).
3. **Consent, not surveillance** — data flows only where the customer explicitly allows (PH2-000 privacy model; "we do not resell customer data").
4. **Boring technology, exciting product** — one monolith, one database, proven patterns until scale forces otherwise (ADR-004/008/009).
5. **Thailand-first, region-ready** — Thai defaults everywhere; architecture keeps E.164/locale/consent-version hooks for the region (DECISION-067, wallet design).
6. **Every phase strengthens the flywheel** (Long-term-Roadmap): merchants → QR joins → wallet value → consumer preference → merchant results → referrals.

### Success definition
- **North Star (candidate, ratification = DR-02):** Weekly Active Loyalty Members — members who earned or redeemed in the past 7 days, network-wide.
- 2030 headline targets (§13): 50,000 paying merchants · 3M wallet customers · NSM 750k · ฿-positive unit economics per merchant cohort within 6 months.

---

## 2. Product Evolution Timeline

Sequencing is capacity-realistic (small team + AI development system). Dates are planning estimates, not promises; **priority order is firm, timing is not** (Roadmap.md doctrine).

| Phase | Window | Theme | Entry gate |
|---|---|---|---|
| **Phase 1 close-out** | 2026 H2 | SCALE-001 hardening, pilot → paying merchants, DOC-001/002/003 debt | ADR-009 + BD-13 |
| **Phase 2 — Customer Wallet** | 2026 H2 – 2027 | PH2-001A→F (specs exist), LINE OA (PH2-002), tiers (PL-001), Enterprise Bridge v1 (PH2-003) | BD-01…BD-10 |
| **Phase 3 — Commerce Network** | 2028 – 2029 H1 | Product/menu listing, ordering, PromptPay checkout, POS Lite, inventory, multi-location (PL-002) | Wallet ≥ 100k MAU + DR-10 |
| **Phase 4 — Regional OS** | 2029 – 2030 | Malaysia → Vietnam → Singapore, native apps, accounting/procurement, advanced AI | Phase 3 economics proven + DR-20 |
| **Beyond 2030** | — | Platform maturity: public API ecosystem, enterprise white-label at scale | — |

Rule carried from OMOS: each phase gate is an ADR + CEO decision; phases never start on enthusiasm alone (the Phase-2 gate discipline stays).

---

## 3. Customer Wallet Vision

Authoritative design: [PH2-000 package](../02-Product/Customer-Wallet/README.md). Strategic summary:

- **Universal OneMember ID** — one OTP-verified phone identity linking many per-merchant memberships; merchants keep sovereignty of their Member records (link, don't merge).
- **Digital membership card** — every membership a branded card: balance, progress, history, member QR; PWA first, installable day one.
- **Cross-merchant experience** — one wallet, one QR, every OneMember shop; discovery only via opt-in directory; **no cross-merchant data leakage, ever**.
- **Apple Wallet / Google Wallet** — designed (package Doc 07), gated on BD-04 (Bible amendment + accounts). Strategic value: the pass surface is retention real estate on the lock screen.
- **QR identity** — signed universal join QRs on every counter; rotating member QRs at presentation; the QR *is* the brand ritual ("scan, earn, return").
- **Privacy-first design** — per-merchant, per-data-type, versioned consent; PDPA as feature, not compliance chore. Marketing position: "the loyalty app that doesn't spy on you." (DR-03: whether to lead marketing with privacy.)

2030 wallet ambition: 3M customers, ≥2.5 memberships each, wallet-attributed repeat-visit lift ≥15% — the number merchants renew for.

---

## 4. Merchant Platform Evolution

Grounding: `14-Version-2.0-Ideas.md` already parks most of these. Status column is honest about Bible coverage.

| Capability | 2026–2030 shape | Bible status / gate |
|---|---|---|
| Advanced CRM | Member timeline (visits, rewards, notes, consent state), saved segments, bulk actions | Extension of live member management; spec in DOC-001 successor |
| Marketing automation | Trigger→action rules ("no visit 30 d → send offer") building on MVP-008 win-back rails; quiet hours, caps, consent-gated | V2-Ideas §3; needs spec (DR-11) |
| AI recommendations | §5 | Analytics.md successor |
| Customer segmentation | Rule-based segments (recency, frequency, spend, birthday month, postal code — postal field already live) feeding campaigns + automation | V2-Ideas §6 |
| Coupon engine | Single-use / limited-count coupon codes as a Reward subtype (reuses redemption ledger) | **Not in Bible** — DR-12 before any spec |
| Referral programs | Member-refers-friend with both-sides reward; fraud controls | V2-Ideas §7; DR-13 |
| Gift cards | Stored-value = regulated e-money territory in Thailand | **Not in Bible; regulatory** — DR-14, default posture: defer |
| Membership tiers | Bronze/Silver/Gold on lifetime points (PL-001, revisit after wallet) — tier logic on Member, display in wallet | Parked → Phase 2.1 |
| Franchise management | Brand HQ oversees franchisee merchants: shared campaign templates, roll-up analytics; builds on multi-branch | **Not in Bible** — DR-15; earliest Phase 3 |
| Multi-branch support | One merchant, many locations, member earns anywhere (PL-002) | Parked → Phase 3 (Commerce needs it anyway) |
| Staff management | Staff accounts + roles (Glossary promises this; Counter Mode is the natural first consumer) — PIN-per-staff, per-action audit | V2-Ideas §5; BD-18 timing |

Sequencing principle: CRM/segments/automation ride the wallet's consent rails — build after PH2-001C, never before (otherwise we build marketing tools that PDPA forces us to rebuild).

---

## 5. AI Strategy

Foundation exists: `MerchantIntelligenceService` + `RuleBasedInsightProvider` behind `InsightProviderInterface` — the interface is the strategy: providers upgrade (rules → LLM → fine-tuned) without touching consumers.

| Capability | What it is | When |
|---|---|---|
| AI dashboard summaries | Natural-language "this week at your shop" digest from existing metrics | 2027 (first LLM provider behind the interface) |
| AI customer insights | Segment discovery, anomaly callouts ("Tuesdays are dying") | 2027 |
| AI campaign generation | Draft campaign settings + copy from merchant goal ("fill quiet Mondays") — merchant approves everything | 2027–2028 (DR-16) |
| AI marketing content | Thai-first post/message copy for automation touches | 2027–2028 (DR-16) |
| AI churn prediction | Per-member risk scores upgrading win-back from thresholds to ranked lists | 2028 (needs transaction volume) |
| AI reward recommendations | Reward pricing/selection suggestions from redemption elasticity across the network (anonymised, consent-gated) | 2028 |
| AI business advisor | Monthly "what to do next" plan blending all of the above | 2029 |
| Conversational assistant | Chat over the merchant's own data ("how were the holidays vs last year?") — Thai language a differentiator | 2029–2030 (DR-17) |

Guardrails (non-negotiable, extend CEO-006 spirit):
- AI never auto-sends to customers — merchant approval on every outbound message.
- AI reads aggregates and the merchant's own data only; cross-merchant learning uses anonymised network statistics under the §3 privacy promise.
- Cost discipline: LLM spend is a per-plan feature gate (revenue §10), not an unbounded COGS.
- Every AI feature ships with a rules-based fallback (the current provider) — no availability dependency on a model vendor.

---

## 6. Ecosystem Integrations

Priority = where Thai merchants and consumers already are. Every integration is adapter-pattern behind an interface (SmsProviderInterface precedent) — vendors swappable, none load-bearing.

| Integration | Purpose | Priority / gate |
|---|---|---|
| **LINE OA** | THE Thai channel: notifications, member card in LINE, join via LINE Login | Phase 2 (PH2-002 exists in backlog) — highest |
| SMS providers | OTP + transactional (BD-09) | Phase 2, required |
| Email providers | Already abstracted (SES et al.) | Live |
| Facebook / Instagram | Join links in bio/posts; automation content export; **not** ad-pixel data sharing (privacy promise) | 2027–2028, light-touch (DR-18) |
| TikTok | Same light-touch posture: trackable join links only | Nice-to-have, 2028+ |
| WhatsApp | Minor channel in Thailand; regional relevance (Phase 4 markets) | Defer to Phase 4 |
| POS systems | Inbound webhooks: sale → points automatically (kills manual entry) — top merchant-retention integration | 2028 (Phase 3, alongside POS Lite; DR-19) |
| Payment gateways | PromptPay first (roadmapped Phase 3 checkout); gateway-agnostic interface | Phase 3 |
| Accounting software | Export → Thai accounting tools; full module is Phase 4 (Accounting.md) | Phase 4 |
| CRM systems | Enterprise-side sync via Enterprise Bridge, not per-CRM plugins | With Bridge v2 |
| Public APIs | §7 — the umbrella for third-party ecosystem | 2028+ |

---

## 7. Enterprise Platform

Foundation: Enterprise-Bridge.md (placeholder — must be written before PH2-003; Gap-Review item). Evolution ladder:

1. **Bridge v1 (Phase 2):** REST + OAuth2 client-credentials; enterprise pushes membership/points events into wallet-linked records; point-conversion config.
2. **RBAC & staff (2027–2028):** roles on merchant accounts (owner/manager/staff) — prerequisite for franchise + enterprise seats (BD-18).
3. **Audit logging:** merchant-action audit table (Glossary already anticipates `audit_logs`) — enterprise procurement requirement and good hygiene for all.
4. **SSO (2028+):** SAML/OIDC for enterprise back-office users — buy-not-build via a library; only when a signed enterprise demands it.
5. **Multi-company (2028+):** holding-company view over multiple merchant entities (franchise DR-15 sibling).
6. **White-label (2029+, DR-20):** wallet + merchant app under enterprise brand (V2-Ideas §9). High revenue, high complexity — only after core wallet wins on its own brand.
7. **Enterprise analytics:** roll-up dashboards + scheduled exports on the read-replica/rollup infrastructure (§8) — never live OLTP queries.
8. **API platform (2028+):** public, versioned, keyed + rate-limited API with developer portal; starts read-only (balances, members under consent), grows to webhooks. (DR-21 — commercial model in §10.)

---

## 8. Technical Evolution

Authoritative: [Scalability-Review-2026-07](../10-Architecture/Scalability-Review-2026-07.md). Milestones:

| Milestone | Trigger | Year (est.) |
|---|---|---|
| Redis (cache/queue/session) + Horizon + object storage + observability | SCALE-001, pre-launch | 2026 |
| Read replica for admin/exports/analytics | B-08 (~1M members) | 2027 |
| Rollup tables (`daily_merchant_stats`, `daily_campaign_stats`) | B-09 (~10M tx) | 2027 |
| Queued exports + signed links everywhere | with replica | 2027 |
| Search engine (Meilisearch) — **admin + wallet directory only** | B-14 / BD-11 | 2028 if directory search ships |
| Event processing: Laravel events + Redis queues remain THE bus; add outbox pattern for Bridge webhooks only | Bridge v2 | 2028 |
| Transactions partitioning/archival | ~100M rows approached | 2029 |
| Regional read replicas / multi-region | Phase 4 expansion | 2029–2030 |

**Future service boundaries** (extraction order if ever needed — Scalability §1.19): wallet → pass services → analytics/rollups → Enterprise Bridge.

**Explicitly NOT microservices prematurely (2026–2028, restated as policy):**
- The loyalty core (members/transactions/campaigns/rewards) — it IS the product; splitting it buys distributed-transaction pain for nothing.
- Auth/identity — two guards in one app is simple and safe.
- The wallet — it's a domain group with a documented seam, extract only on sustained load evidence.
- No Kafka/event-bus, no GraphQL federation, no NoSQL, no per-tenant databases, no Kubernetes before there are ≥3 services to orchestrate.

---

## 9. Mobile Strategy

| Step | What | When |
|---|---|---|
| **PWA (wallet)** | Installable from first wallet release; offline last-known balances + QR (design Doc 10) | Phase 2.0 |
| **PWA (merchant)** | Mobile-first web app already live (RELEASE-2B); add install prompt + Counter Mode offline queue (record → sync) | 2027 (offline queue = DR-22, conflict rules needed) |
| **Push notifications** | Web Push for wallet (points/rewards/birthday) after email baseline (BD-08); LINE notifications may outrank Web Push in Thailand — measure, then choose | 2027 |
| **Customer App (native)** | Wallet API v1 is already the backend; native shell (single codebase, Flutter/RN = DR-23) when wallet MAU ≥ 250k or pass/push limitations demand it | 2029 (Phase 4 per Long-term-Roadmap) |
| **Merchant App (native)** | Only if PWA measurably blocks merchants (PL-003 posture: value doesn't justify cost yet) | 2029+, evidence-gated |
| **Offline mode** | Wallet: read-only offline (done in PWA). Counter: queued writes with server-side idempotency keys | 2027–2028 |

Doctrine: web-first until the web measurably fails a job; native apps are Phase 4 deliverables in the existing roadmap, not vanity milestones.

---

## 10. Revenue Strategy

Current model: SaaS plans free/starter/professional/enterprise (11-Pricing-Strategy). Evolution — all pricing changes are CEO territory (DR-30 umbrella):

| Stream | Shape | Earliest |
|---|---|---|
| Merchant subscriptions | Core forever; wallet features strengthen plan differentiation (e.g., invites/passes on paid tiers — BD-03) | Live |
| Enterprise licensing | Bridge seats + SLA + white-label premium | 2027–2028 |
| Premium AI features | AI advisor/campaign-gen as add-on or top-tier inclusion; LLM COGS priced in | 2027–2028 |
| Marketing services | Automation sends bundled by volume (email free-tier, SMS/LINE metered at cost+margin) | 2027 |
| API usage | Free developer tier → metered calls/webhooks for ISVs | 2028+ |
| Partner ecosystem | POS/accounting referral & certification programme | 2028+ |
| **Never:** advertising, data resale, payment-float income, consumer fees for the wallet (kills the flywheel; §16) | — | — |

---

## 11. International Expansion

Sequence (Long-term-Roadmap Phase 4, unchanged): **Malaysia → Vietnam → Singapore**, earliest 2029, each market gated by DR-20 and the previous market's unit economics.

Per-market readiness checklist (architecture hooks already exist):
- Language: full locale files (en/th pattern extends; completeness test enforces parity per locale).
- Payments: DuitNow / VNPay / PayNow adapters behind the Phase 3 gateway interface.
- Privacy: consent copy + `consent_version` per jurisdiction (Malaysia PDPA, Vietnam Decree 13, Singapore PDPA); counsel per market (BD-07 pattern).
- Identity: E.164 phones day-one ready; per-market SMS providers; number-plan allow-lists.
- Currency: TD-005 (hardcoded THB fallback) must be resolved **before** the first non-THB merchant — already flagged "before regional expansion" in the Engineering Backlog.
- Data residency: evaluate per market (DR-24) — regional read replicas planned in §8; full data-partition only if law requires.
- Go-to-market: never enter without a local partner/first-10-merchants plan — product localisation is the cheap part.

---

## 12. Risk Assessment

| Category | Top risks | Mitigation |
|---|---|---|
| **Product** | Wallet cold-start (R-01); merchant feature-breadth overwhelming SME simplicity (Bible: "results, not complexity") | Claim flow converts existing members; ruthless §15 prioritisation; every feature behind "does a salon owner get it in 10 seconds?" |
| **Technical** | Single-DB blast radius; AI vendor dependency; integration sprawl (each adapter = maintenance) | Scalability plan + drills (Ops Manual); provider interfaces + rules fallback; integration count capped per year (DR-25) |
| **Business** | Churn if loyalty ROI invisible; LINE/large player launches competing SME loyalty; pricing squeeze at low ARPU | Analytics that show ฿ value per campaign (RELEASE-4A onward); flywheel + privacy positioning as moat; enterprise/AI streams lift ARPU |
| **Regulatory** | PDPA enforcement tightening; e-money rules if gift cards/stored value (DR-14); SMS sender-ID regulation | Consent architecture already exceeds baseline; default-defer stored value; registered sender IDs + provider compliance |
| **Scaling** | Ops maturity lags growth (one-engineer bus factor); cost of Redis/replica/LLM stack ahead of revenue | Operations Manual §17 onboarding + runbooks; tiered plan ties infra spend to bottleneck triggers, not ambition |

---

## 13. Five-Year Milestones (realistic, measurable)

| Year | Theme | Measurable goals (EOY) |
|---|---|---|
| **2026** | Launch & harden | Production live (Go-live checklist passed) · SCALE-001 done · 300 paying merchants · 30k members · wallet PH2-001A–C live behind flag · NSM baseline established |
| **2027** | Wallet network | 2,000 paying merchants · 250k members · 75k wallet customers (≥1.8 memberships avg) · LINE OA + tiers + automation v1 · read replica + rollups · churn < 3%/mo |
| **2028** | Intelligence & openness | 8,000 merchants · 1M members · 500k wallet MAU · AI insights/campaign-gen GA · Bridge v1 with 3 enterprise logos · POS integrations live · API beta · ARPU +25% via AI/automation tiers |
| **2029** | Commerce & first border | 20,000 merchants · Commerce + PromptPay checkout GA · POS Lite + multi-location · Malaysia pilot (500 merchants) · native customer app decision executed (DR-23) · NSM 300k |
| **2030** | Regional ecosystem | 50,000 merchants across TH+MY+VN · 3M wallet customers · NSM 750k · enterprise/white-label ≥15% of revenue · company default-alive on subscription revenue alone |

Checkpoint discipline: every EOY, this table is re-forecast in a VISION review sprint; misses are explained in writing, not silently rebased.

---

## 14. Decision Register (strategic — Product Owner approval required; nothing assumed)

Carried open: **BD-01…BD-18** (PH2-000 + SCALE-000 registers). New strategic decisions:

| ID | Decision | Sections |
|---|---|---|
| DR-01 | Ratify Mission & Vision statements (00-Executive docs are unwritten drafts) | §1 |
| DR-02 | Ratify North Star Metric (candidate: Weekly Active Loyalty Members) | §1 |
| DR-03 | Lead consumer marketing with privacy positioning? | §3 |
| DR-10 | Phase 3 entry criteria + budget | §2 |
| DR-11 | Marketing automation scope v1 (channels, caps, pricing) | §4 |
| DR-12 | Coupon engine — admit to Bible? | §4 |
| DR-13 | Referral programme — admit to Bible + fraud budget | §4 |
| DR-14 | Gift cards / stored value — regulatory posture (default: defer) | §4 |
| DR-15 | Franchise management — target segment & Bible amendment | §4 |
| DR-16 | AI outbound content: approval UX + LLM vendor + COGS ceiling | §5 |
| DR-17 | Conversational assistant investment | §5 |
| DR-18 | Social platform integration depth (links-only vs deeper) | §6 |
| DR-19 | POS integration partner strategy (open webhook vs certified partners) | §6 |
| DR-20 | White-label + international entry gates | §7, §11 |
| DR-21 | Public API commercial model | §7, §10 |
| DR-22 | Counter offline-queue conflict rules | §9 |
| DR-23 | Native app framework + trigger threshold | §9 |
| DR-24 | Data residency stance per market | §11 |
| DR-25 | Annual integration budget/cap | §12 |
| DR-30 | All pricing evolutions (umbrella — every §10 change is individually CEO-approved) | §10 |

## 15. Recommended Priorities

**Must Build** (flywheel-critical): SCALE-001 · PH2-001A–D,F (wallet core + merchant tools) · LINE OA · consent/privacy centre · CRM segments + automation v1 · rollups/replica · AI insights v1 (summaries) · membership tiers.

**Should Build** (clear value, after Must): Apple/Google passes (BD-04) · Bridge v1 · churn prediction · staff accounts/RBAC · referral programme (DR-13) · POS webhooks · Web Push · audit logging · API read-only beta.

**Nice to Have** (evidence-gated): coupon engine · franchise tools · conversational assistant · TikTok/IG deep links · white-label · native apps · directory geo-search.

**Do Not Build** (in the 2026–2030 window): gift cards/stored value (regulatory, DR-14 default) · WhatsApp before Phase 4 · marketplace features · custom per-merchant code · multi-vendor commerce · any §16 item.

## 16. What OneMember Should Never Become

Extends the Bible's Non-Roadmap list into standing policy:

| Never | Why |
|---|---|
| An advertising network or data broker | The privacy promise is the moat; monetising attention breaks the flywheel and PDPA trust |
| A social platform (reviews, feeds, followers) | Distraction from loyalty-commerce thesis (already excluded) |
| A bank: lending, credit, stored-value float, crypto | Regulatory gravity that crushes an SME SaaS (already excluded; gift cards only under DR-14 with counsel) |
| A horizontal CRM/Salesforce clone | Vertical focus is the advantage (already excluded) |
| A marketplace competing with its own merchants | We are infrastructure for merchants, never their competitor |
| A consultancy (per-merchant custom builds) | One product, many tenants — custom code destroys the multi-tenant economics |
| A microservice zoo | Team-of-few + monolith discipline is a feature (§8); services only at documented seams on load evidence |
| A surveillance analytics vendor ("see what your customers do elsewhere") | Explicitly promised never — even with technical consent, it poisons consumer trust |
| Free-for-consumers-turned-paid | Charging consumers kills wallet adoption, which kills the merchant value proposition |

---

*Review cadence: annually each Q4 (first review 2026 Q4), plus at every phase gate. Changes require Product Owner approval and a version bump.*
