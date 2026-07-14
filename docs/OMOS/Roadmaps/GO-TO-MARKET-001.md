# GO-TO-MARKET-001 — Merchant Acquisition Strategy (First 100 Merchants)

| Field | Value |
|---|---|
| **Document type** | Business strategy — go-to-market blueprint (no software) |
| **Sprint** | GO-TO-MARKET-001 |
| **Status** | 📋 Strategy — awaiting CTO/PO ratification |
| **Author** | Claude Fable 5 (VP Sales / VP Marketing / COO / Growth Advisor role) |
| **Date** | 2026-07-10 |
| **Inputs** | INTERNATIONAL-001 blueprint, product state v0.9.1 (Launch Kit, Counter Mode, Commerce + Storefront, Help Center 47 EN + 6 TH articles, guided launch journey MR-001–003), merchant presentations v3 (EN/TH), corporate website, 30-day Professional trial, Free-100 offer (CORE-001), pricing tiers Free/Starter/Professional/Enterprise (amounts = DECISION-014, PO-owned) |
| **Related** | [INTERNATIONAL-001.md](./INTERNATIONAL-001.md), [../../11-Pricing-Strategy.md](../../11-Pricing-Strategy.md), business/ (sales, marketing, customer-success) |

**Rule:** strategy only. Every number that is a price is a placeholder for DECISION-014; every activity here is executed by people, not by this sprint.

---

## SECTION 1 — Vision

**Mission.** Give every small merchant in Southeast Asia the customer-loyalty power of a 7-Eleven — in ten minutes, on their own phone, in their own language.

**Vision.** OneMember becomes the loyalty layer of Southeast Asian street commerce: the default answer when any café, salon, or shop asks "how do I make customers come back?" — 1,000 merchants in Phase 1, a cross-merchant member network in Phase 2 (Customer Wallet), the regional loyalty standard by 2030.

**Unique value proposition.**
> "Your regulars, coming back more often — set up before your coffee gets cold."

Concretely, for a merchant:
1. **Ten minutes to launch** — guided journey from signup to printed QR poster (MR-001–003 built exactly this).
2. **No hardware, no app installs** — customer scans a poster; staff use Counter Mode on any phone.
3. **Thai-first, not translated-later** — full Thai UI, Thai manual, Thai support.
4. **Grows with the shop** — points/stamps today; storefront, ordering, and (future) wallet network without switching platforms.
5. **The merchant owns the customer** — unlike delivery platforms that own (and resell) the relationship.

**Competitive positioning.**

| Competitor class | Their pitch | Our counter |
|---|---|---|
| Paper stamp cards | Free, familiar | Lost cards, fraud, zero data, no win-back; OneMember Free tier beats free paper on value |
| LINE Official Account points | Everyone has LINE | Generic, no loyalty mechanics depth, no counter workflow, no storefront; we integrate *beside* LINE, not against it |
| POS-bundled loyalty (Ocha, Wongnai POS, StoreHub) | Already in the till | Requires *their* POS; loyalty is a checkbox feature. We are POS-agnostic and loyalty-deep — and a partner channel, not only a rival |
| Delivery platforms (Grab/LINE MAN) | Bring new customers | They tax every order and own the customer. We are the merchant's *own* channel — positioning: "delivery apps rent you customers; OneMember helps you keep yours" |
| Enterprise loyalty suites | Powerful | Priced and built for chains; unreachable for a 2-person café. We are the SME-sized version |

**Why OneMember exists.** Repeat customers are the highest-margin revenue a small merchant has, and the only group with zero affordable tooling for it. The fragmentation of SEA street commerce is not a bug in our market — it *is* our market.

---

## SECTION 2 — Priority Markets

Aligned with INTERNATIONAL-001 (technical rollout) — GTM adds the commercial lens:

| Rank | Market | GTM reasoning | Entry mode |
|---|---|---|---|
| 1 | 🇹🇭 **Thailand** | Product is Thai-ready today; founder proximity; PromptPay culture makes QR behaviour native | Direct, founder-led sales (first 100 merchants live here) |
| 2 | 🇲🇲 **Myanmar** | CTO-committed second market; underserved SMEs; Thai-Myanmar corridor gives warm intros (Mae Sot / Yangon trading families) | **Partner/reseller-led** — never direct at first (INTERNATIONAL-001 risks R2/R3) |
| 3 | 🇱🇦 Laos | Vientiane behaves commercially like a Thai province; Thai language partially workable | Extension of TH ops, not a new org |
| 4 | 🇰🇭 Cambodia | USD economy simplifies pricing; Phnom Penh café boom | Partner-led |
| 5 | 🇻🇳 Vietnam | Big but crowded; needs full VN localization + local team | Deferred until >500 TH merchants |
| 6 | 🇲🇾 Malaysia | English-workable, strong rails | After billing matures (Omise/2C2P decision) |
| 7 | 🇸🇬 Singapore | Highest ARPU, smallest count; credibility market | Enter for lighthouse/Enterprise logos |

**Rollout order recommendation:** All "first 100 merchants" effort goes to **Thailand only**. Myanmar activities in the first 180 days are limited to partner scouting and the INTERNATIONAL-001 Phase 3 gate. Do not split founder attention.

---

## SECTION 3 — Merchant Segments

| Segment | Typical size | Loyalty today | Pain points | Buying triggers | Rejection reasons | OneMember modules |
|---|---|---|---|---|---|---|
| **Coffee shops** ☕ | 1–2 branches, 2–6 staff, 80–300 cups/day | Paper stamp cards | Lost cards; can't tell regulars from tourists; morning-rush friction | New branch opening; competitor loyalty next door; slow season | "Paper works fine"; staff turnover fear | Stamps campaign, QR poster, Counter Mode |
| **Restaurants** 🍜 | Family-run to 20 staff; delivery-app dependent | None, or delivery-app points (not theirs) | 30% platform commissions; no direct customer channel; empty weekdays | Delivery-fee shock; want direct ordering | "Too busy at service time"; low-margin caution | Points, win-back alerts, Commerce storefront (direct orders) |
| **Hair salons** 💇 | 1 shop, 2–8 chairs; appointment-based | Discount for "regulars" (memory-based) | No-shows; clients drift after stylist leaves; irregular visit cycles | Stylist departure scare; slow months | Owner-stylist has no admin time | Points + birthday rewards, member notes, win-back (45-day cycle) |
| **Nail salons** 💅 | 1–4 techs; high repeat cadence (2–4 wks) | Punch cards, LINE chat lists | Competition density; price wars | Opening promo; Instagram competitor pressure | Price sensitivity | Stamps, birthday, QR poster at reception |
| **Massage & spa** 💆 | 4–20 beds; tourist/local mix | Paper vouchers, 10+1 deals | Tourist one-timers vs local regulars indistinguishable; staff commission tracking | High season prep; corporate packages | Cash culture; older ownership | Points, member tiers via rewards ladder, Counter Mode per therapist |
| **Hotels** 🏨 (boutique) | 10–60 rooms; independent | OTA-dependent; no repeat mechanism | OTA commissions; zero guest ownership | Direct-booking push; repeat-guest ambitions | IT approval chains; PMS integration expectations | Points on stays/F&B, Enterprise tier, storefront for vouchers — *sell last; longest cycle* |
| **Retail** 🛍️ (minimart, gifts) | 1–3 shops; thin margins | None or cashier-memory | Basket-size stagnation; nearby 7-Eleven gravity | Rent renewal panic; new competitor | Tiny tickets → "points feel silly" | Points per amount, expiry pressure campaigns |
| **Fashion** 👗 | Boutique + IG shop hybrid | IG followers as "loyalty" | Followers ≠ buyers; drop-based revenue spikes | New collection launches | "Instagram is enough" | Points, member-only rewards, storefront as catalogue |
| **Pet shops** 🐕 | 1 shop + grooming; monthly cadence | Grooming punch card | Grooming no-shows; food-purchase drift to online | Grooming competition; vet-shop bundling | Online price undercutting fear | Stamps (grooming), points (food), birthday (pet birthdays — delight feature) |
| **Beauty clinics** 💉 | 2–15 staff; high ticket, course-based | Course packages (prepaid) | Course completion drop-off; referral dependence | New machine ROI pressure; referral program formalization | Medical-adjacent caution; existing CRM | Points on treatments, reward = free add-on, Enterprise for chains |

**Beachhead recommendation:** **Coffee shops + nail/hair salons** first (fast cycles, low friction, dense clusters, visible posters that market to the *next* merchant on the street). Restaurants second (bigger pain, bigger noise). Hotels/clinics only opportunistically until 100 merchants.

---

## SECTION 4 — Merchant Personas

### P1 — "Khun Nok" — Owner-operator (coffee shop, 1 branch)
- **Goals:** more regulars, busier weekday afternoons, feel "professional" like a chain
- **Concerns:** monthly cost vs. cups sold; staff won't use it; looks complicated
- **Budget:** ≤ a few hundred THB/month mental ceiling; annual = harder than monthly
- **Tech confidence:** LINE + IG native, spreadsheet-averse; everything on phone
- **Decision process:** decides alone, same-week; asks a merchant friend first; will try anything free that takes <30 min
- **Sell:** demo on *her* phone, Free tier start, printed poster in-hand before we leave

### P2 — "Khun Ploy" — Hired manager (restaurant, owner absent)
- **Goals:** hit owner's revenue target; avoid anything that creates staff complaints
- **Concerns:** being blamed if it flops; training burden; another login to manage
- **Budget:** none personally — must build owner's case
- **Tech confidence:** moderate; POS-literate
- **Decision process:** gatekeeper → influencer; needs a one-page owner summary with numbers
- **Sell:** equip her to sell upward — ROI one-pager, "manager hero" framing, offer to demo to the owner on LINE call

### P3 — "Khun Chai" — Multi-branch owner (4 salons)
- **Goals:** consistency across branches; know which branch/stylist retains clients; brand image
- **Concerns:** fragmented member lists per branch; staff fraud; whether the platform will still exist in 3 years
- **Budget:** real (thousands THB/month) if per-branch value is provable
- **Tech confidence:** delegates; wants dashboards, not settings
- **Decision process:** 2–6 weeks; wants references and a pilot branch first
- **Sell:** pilot one branch → roll out; Enterprise narrative (multi-branch controls, white-label) as the *future* he's buying into

### P4 — "Khun Mint" — Operations manager (boutique hotel / clinic chain)
- **Goals:** reporting for owners; SOP-able processes; vendor that answers email
- **Concerns:** data privacy (PDPA), staff permissions, audit trail, integration questions
- **Budget:** holds a line item; procurement-ish process
- **Tech confidence:** high; will read documentation (our Help Center is a sales asset here)
- **Decision process:** longest (1–3 months); security/permission questions; needs Terms/DPA clarity (DR-33 matters commercially)
- **Sell:** professionalism — documentation, roadmap transparency, named support contact, Enterprise tier

---

## SECTION 5 — Pricing Positioning

(Amounts remain PO-owned — DECISION-014. This section positions the tiers; it does not price them.)

| Tier | Positioning | Who it's for | Psychology |
|---|---|---|---|
| **Free** | "Better than paper, forever free" — up to 100 members (Free-100, CORE-001) | Khun Nok trying it out; micro-merchants | Removes all risk; the poster on their counter is our street-level ad. Free tier is a *distribution strategy*, not lost revenue |
| **Starter** | "Your regulars, on autopilot" — unlocks growth features beyond the basics | Single-shop merchants past 100 members | The 100-member wall is a *success moment* ("you have 100 regulars!") — upgrade framed as graduation, never as punishment |
| **Professional** | "Run it like a chain" — full campaigns, analytics, commerce, win-back | Serious single/dual-branch merchants | This is the 30-day trial tier: everyone *starts* at full power, then keeps what they now can't live without |
| **Enterprise** | "Your brand, your network" | Chains & franchises | See below |

**Enterprise positioning (chains, white-label, corporate controls):**
- **Chain stores:** multi-branch member view, per-branch performance, centrally-managed campaigns with branch execution — sell as "consistency without micromanaging."
- **White-label branding:** *their* logo, colors, store URL on every customer surface (foundation already real: branding service, store identity, portal theming) — sell to brands who want "our own member app" without building one.
- **Corporate controls:** staff roles/permissions, audit logs, exports, SLA support, invoicing — the P4 persona checklist.
- Priced by conversation, not by page. The public page shows "Talk to us" — Enterprise leads are relationship sales.

**Upgrade path recommendation:** Register → 30-day Professional trial (existing) → on expiry choose Free (keep data, capped) or pay → Free merchants hit the 100-member success wall → Starter → outgrow into Professional (analytics/commerce pull) → multi-branch conversation → Enterprise. **Never delete a lapsed merchant's data** — dormant Free merchants are the warmest future pipeline we own.

---

## SECTION 6 — Sales Process (the OneMember Sales Journey)

| Stage | What happens | Owner | Exit criteria |
|---|---|---|---|
| **1. Prospect** | Street-cluster canvassing (see §8), inbound from posters/social, referrals logged in a simple CRM sheet | Founder/first sales hire | Merchant name + contact + segment captured |
| **2. Qualify** | 3 questions: Do repeat customers matter to you? (all say yes) · Roughly how many customers/day? · Who decides? | Sales | Persona identified (P1–P4); decision-maker reachable |
| **3. Demo** | 10-minute phone demo using `onemember:demo-seed` data — *their* segment's quick-start article open; end on the printed QR poster | Sales | "Can I try it?" moment reached |
| **4. Trial** | Self-serve signup on *their* phone during the visit; guided launch journey (MR-003) takes over; day-2 and day-7 human check-ins | Product + CS | Launch checklist ≥ 60% by day 7 (our activation metric) |
| **5. Conversion** | Trial-ending reminders (built) + human call at day 21 offering help, not pressure; objection handling from §8 | Sales | Paid plan or explicit Free-tier choice (both are wins) |
| **6. Onboarding** | Already productized: launch checklist → Launch Ready 🎉; CS ensures poster is physically printed and displayed | CS | 100% Launch Ready + poster on counter |
| **7. Success** | Monthly "your numbers" touch (members, redemptions, win-back wins) — WhatsApp/LINE message, not email; watch health-card reds | CS | Merchant sees ROI in their own words |
| **8. Renewal** | Monthly billing = continuous renewal; watch usage-drop signals (admin MerchantHealthService exists for exactly this) | CS | <3% monthly logo churn |
| **9. Referral** | "Which shop owner do you know who needs this?" asked at every success touch; referral reward (free month both sides — PO to ratify) | Everyone | ≥25% of new merchants referral-sourced by merchant #100 |

---

## SECTION 7 — Acquisition Channels (evaluated)

| Channel | Verdict | Why / How |
|---|---|---|
| **Facebook** | 🟢 Core (TH SME life runs on FB) | Merchant-story videos, before/after member counts; FB groups for café/salon owners; modest boosted posts geo-targeted to shop districts |
| **LINE** | 🟢 Core (support + nurture, not ads) | LINE OA as the support/sales channel — Thai merchants close deals in LINE chat, not email. Broadcast monthly tips |
| **Instagram** | 🟡 Secondary | Aesthetic segments (nails, fashion, cafés); repost merchants' own poster photos — social proof loop |
| **TikTok** | 🟡 Experimental | "Shop makeover" style content has organic reach in TH; low cost to test, drop if CAC ugly |
| **Google Business** | 🟢 Hygiene | Profile + reviews; merchants will google us before paying. Free |
| **SEO** | 🟡 Slow-burn | Thai-language content: "ระบบสะสมแต้ม" cluster; Help Center articles already exist to repurpose. Compounds by month 6+ |
| **Email** | 🔴 De-prioritize | Thai SMEs don't live in email; use only for receipts/billing (already built) |
| **Cold outreach (street)** | 🟢 **The #1 channel for merchants 1–50** | Physical canvassing of dense districts with tablet + printed poster samples. Nothing beats "let me show you on your counter" |
| **Referral** | 🟢 Build from day 1 | §6 stage 9; the poster itself carries "Powered by OneMember" — every customer scan is an impression to other merchants |
| **Business associations** | 🟡 Month 3+ | Thai Restaurant Association, salon guilds, provincial chambers — one talk = 50 warm SMEs; needs credibility (case studies) first |
| **Franchise groups** | 🟡 Enterprise pipeline | Small TH franchise brands (5–30 branches); long cycle; start relationships early, close after case studies |
| **Partners (agencies/consultants)** | 🟡 Month 6+ | Marketing agencies serving SMEs white-labeling or referring; commission model |
| **POS vendors** | 🟢 Strategic (not exclusive) | Ocha/StoreHub/local POS resellers bundling OneMember where their loyalty is weak; we are POS-agnostic — sell that. Also the natural Myanmar entry (POS resellers exist in Yangon) |
| **Payment providers** | 🟡 Watch | Omise/2C2P conversations for billing (INTERNATIONAL-001 §7) can grow into distribution partnerships; don't lead with it |

**Channel budget stance for first 100:** 70% founder street sales + referral, 20% Facebook/LINE content, 10% experiments. CAC discipline: paid channels must beat the cost of a founder afternoon in Ari.

---

## SECTION 8 — Thailand Strategy

**How to sell:** street-cluster, relationship-led, LINE-closed.
1. **Pick 3 dense districts** (e.g. Ari/Phahon Yothin cafés, Siam Square salons, a provincial city center — one non-Bangkok cluster on purpose). Saturate one street at a time: every poster installed makes the next pitch easier ("same as ร้าน next door").
2. **The 10-minute counter demo:** phone out → demo-seeded merchant → record a purchase in Counter Mode → show the win-back alert → print/show the QR poster with *their* shop name (Launch Kit does this live). Close on "let's set yours up now — it's free to start."
3. **Follow-up in LINE within 24h** with their launch checklist status and one tip.

**Preferred communication:** LINE first, phone call second, in-person for demos, email never (except invoices). All materials Thai-first (they exist: presentation v3 TH, speaker notes TH, Thai manual).

**Typical objections & answers:**

| Objection | Answer |
|---|---|
| "Paper cards work fine" | "How many stamps did you give away to customers who lost the card and started over? OneMember remembers — and tells you who stopped coming so you can win them back." |
| "My staff won't use it" | "Counter Mode is two taps — search, confirm. Show the cashier once. There's a Thai staff guide printed from the Launch Kit." |
| "Another monthly fee…" | "Start free, up to 100 members, no card needed. Pay only when it's already working." |
| "I already have LINE OA" | "Keep it — OneMember gives your LINE broadcasts something to say: 'you're 2 stamps from a free coffee' beats 'we exist'." |
| "Is my customer data safe / is this PDPA-okay?" | Consent capture is built-in; data belongs to the merchant; (requires DR-33 terms ratification to say this with full force — legal dependency flagged) |
| "Will you still exist next year?" | Roadmap transparency + local presence + case studies; the honest answer that wins Thai trust is a person who shows up again |

**Sales materials (Thai):** presentation v3 TH + speaker notes (exists) · one-page ROI leaflet (needs creation) · pricing card (blocked on DECISION-014) · printed sample QR poster (Launch Kit) · case-study sheets (after pilot) · LINE OA rich menu with demo-booking.

**Demo flow (scripted, 10 min):** Problem (30s: "who came back this week? you don't know") → Poster scan as a customer (2 min) → Counter Mode purchase + points (2 min) → Dashboard: health card + win-back alert (2 min) → Launch Kit print moment (1 min) → their signup (2 min) → leave with checklist at ~40%.

---

## SECTION 9 — Myanmar Strategy

**Entry principle: partner-led, pilot-gated, never founder-direct.** (Risks R1–R3 from INTERNATIONAL-001 stand.)

- **Country risks:** political volatility; banking/sanctions exposure (any money path must be screened); connectivity/power reliability; Zawgyi/Unicode content risk; currency instability (MMK). Consequence: no fixed launch date — a *gate*, not a calendar.
- **Partner strategy:** one exclusive-for-12-months **Yangon distribution partner** — ideal profile: POS/IT reseller or F&B supplier with 200+ SME relationships and Unicode-literate staff. Partner owns: sales, cash collection (solves MMK billing per INTERNATIONAL-001 §7), first-line support. OneMember owns: product, training, second-line support. Revenue share over reseller margin (skin in the game both ways).
- **Support strategy:** Burmese first-line via partner (trained on our manual); our Burmese Getting Started articles (INTERNATIONAL-001 Phase 3) as the training corpus; escalation to EN second-line in LINE/Telegram; support SLA realistic for timezone (+06:30, minor).
- **Language strategy:** sequence per INTERNATIONAL-001 §3 — customer surfaces in Burmese *before* any merchant sees a demo; merchant UI Burmese before scaled rollout (pilot can run mixed EN/Burmese with partner hand-holding); all Burmese content authored in Unicode only.
- **Commercial shape:** pilot 5–10 Yangon merchants (tea shops, beauty salons — same beachhead logic) run by the partner with our weekly reviews; success = same activation metrics as TH pilot (§10) plus zero unresolved encoding complaints. Only after pilot passes do we localize pricing and sign the 12-month exclusivity.

---

## SECTION 10 — Pilot Merchant Program ("Founding Merchants")

**Design: 20 Thai merchants, 8 weeks, hand-held, loud.**

| Element | Design |
|---|---|
| **Cohort** | 20 merchants: 8 coffee, 5 salon/nail, 4 restaurant, 3 retail/other; ≥5 outside Bangkok; mix of P1/P2/P3 personas |
| **Offer** | 6 months Professional free + "Founding Merchant" badge/plaque + direct founder LINE access; in exchange: weekly feedback, logo usage rights, testimonial if satisfied |
| **Success criteria (per merchant)** | Launch Ready ≤ 7 days · ≥50 members by week 4 · ≥1 win-back campaign run · owner can state the value in one sentence unprompted |
| **Success criteria (program)** | ≥15/20 hit member target · ≥70% weekly active (any transaction) at week 8 · NPS ≥ 40 · ≥10 referral names collected · ≥5 case-study-grade stories |
| **Feedback process** | Weekly 15-min LINE call, one question fixed: "what almost made you stop using it this week?"; in-app feedback modal (built) tagged `pilot`; fortnightly synthesis to CTO as prioritized list (bugs > friction > wishes) |
| **Support process** | Dedicated LINE group per merchant; <2h response during business hours; every support question checked against Help Center — a question the manual can't answer = a content bug, logged |
| **Case studies** | Template: shop photo · before (paper cards/nothing) · 8-week numbers (members, repeat visits, redemptions) · owner quote · segment tag. Target 5 written, 3 with video |
| **Testimonials** | Collected at the week-6 high point, not week 8 goodbye; short vertical video (15–30s, owner at counter, Thai) for FB/TikTok; written quote + photo for web/deck |

Pilot graduates seed §7's association talks and franchise conversations.

---

## SECTION 11 — Sales Toolkit (asset inventory)

| Asset | Status | Action |
|---|---|---|
| **Website** (onemember.co corporate + app landing) | ✅ Exists (RELEASE-1B/2A) | Add: case studies page, pricing page (post-DECISION-014), Thai testimonial videos |
| **Sales deck** | ✅ v3 EN + TH with speaker notes | Refresh with pilot numbers after week 8 |
| **Investor deck** | ❌ Not built | Build from Vision (§1) + traction metrics (§12) once pilot data exists — needed for any funding conversation |
| **Brochure / one-pager** | ❌ | One A5 Thai leaflet: problem, 3 screenshots, QR to demo, LINE contact. Print 500 |
| **ROI calculator** | ❌ | Simple sheet/web-embed: avg ticket × visits × members → incremental revenue vs plan price. Also the P2 persona's owner-pitch weapon |
| **Demo script** | 🟡 Implicit in speaker notes | Formalize the §8 10-minute flow; train every future hire on it verbatim first |
| **FAQ** | 🟡 Partially in Help Center | Extract top-20 sales objections/answers (Thai) as a public page + internal battle-card |
| **Videos** | ❌ | 3 needed: 90s product tour (TH), 30s customer-scan loop (for merchant counters!), pilot testimonials |
| **Merchant manual** | ✅ 47 EN + 6 TH articles in-product | Grow TH coverage (INTERNATIONAL-001 Phase 1); print "quick start" as Launch Kit insert |
| **Customer manual** | 🟡 Join/portal flows are self-explaining by design | One poster-adjacent table tent: "How to collect points here" (TH) — customers teach themselves |
| **Pilot kit** | ❌ | Founding Merchant welcome pack: plaque, poster, table tents, staff guide, LINE QR |

---

## SECTION 12 — Success Metrics

**First 30 days (pilot ramp):** 20 pilot merchants signed · 15+ Launch Ready ≤7 days · 500+ total members network-wide · founder does ≥40 street demos · demo→trial ≥50%.

**First 100 merchants (~month 4–6):**

| KPI | Target | Definition |
|---|---|---|
| **Activation** | ≥70% | Launch checklist 100% within 14 days of signup |
| **Time-to-value** | ≤7 days median | Signup → first real member transaction |
| Trial→paid-or-Free-active | ≥60% | Merchant still transacting in week 5 |
| Trial→**paid** | ≥25% | Paid plan at trial end (Free chooser ≠ failure) |
| **Retention** | <5% monthly logo churn | Paying merchants |
| Usage retention | ≥60% WAU/MAU | Merchants with ≥1 transaction/week |
| **Referral** | ≥25% of new signups | Source = existing merchant |
| **Revenue** | MRR per DECISION-014 grid; ARPU tracked from merchant #21 | (First 20 are free pilots — excluded from revenue KPIs) |
| Members network-wide | ≥8,000 | Proves the Phase-2 wallet thesis is forming |

**First 1,000 merchants (Phase 1 exit horizon):** referral ≥35% · churn <3% monthly · CAC payback <6 months · 50,000+ members (Phase 1 exit criterion) · ≥2 sales hires productive on the scripted demo · Myanmar pilot passed its gate · Free→paid conversion ≥10%/quarter of eligible Free merchants.

---

## SECTION 13 — Roadmap

### 90 days — "Prove it" (Thailand only)
1. DECISION-014 pricing ratified + DR-33 terms legal review (both block charging anyone)
2. Founding Merchant pilot: recruit 20 (weeks 1–3), run 8 weeks, weekly feedback loop
3. Build missing tier-1 assets: one-pager, ROI calculator, demo script formalized, LINE OA
4. Street-cluster sales in 3 districts; 40+ demos/month founder cadence
5. Collect 5 case studies + 3 testimonial videos at week 6–8

### 180 days — "Repeat it"
1. Public launch of pricing; convert pilot cohort (target ≥12/20 to paid at pilot end)
2. Merchants 21–100 via street + referral engine + FB/LINE content flywheel
3. First sales/CS hire trained on scripted demo; founder moves to closing + partnerships
4. Association talks (2) + first POS-vendor partnership conversation
5. Myanmar: partner scouting + INTERNATIONAL-001 Phase 2 foundations begin (per that roadmap)
6. Investor deck built from real metrics (optional raise readiness)

### 365 days — "Scale it"
1. 300–500 TH merchants; second cluster city fully owned (Chiang Mai or Khon Kaen)
2. Enterprise motion opens: 2–3 small franchise chains from §7 pipeline
3. Myanmar pilot (5–10 Yangon merchants) via exclusive partner — gated, not dated
4. Partner/agency channel formalized (referral commissions)
5. Laos opportunistic entry as TH-ops extension if TH engine is healthy (<3% churn)
6. Phase 2 (Customer Wallet) business case reviewed against 50k-member trajectory

**Execution order rationale:** price + legal first (can't sell without them), pilot before scale (proof before promises), founder-led before hired sales (script must be proven by the person who can change the product), Thailand before everything (focus is the strategy).

---

*End of strategy. No production code, Laravel, database, routes, controllers, or features were touched.*
