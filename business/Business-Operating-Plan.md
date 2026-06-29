# OneMember Business Operating Plan

> **Version:** 1.0
> **Date:** 2026-06-29
> **Owner:** Product Owner (Huahin)
> **Status:** Approved — living document, reviewed quarterly
> **Cross-reference:** [docs/13-Product-Vision.md](../docs/13-Product-Vision.md) · [docs/15-Go-To-Market-Strategy.md](../docs/15-Go-To-Market-Strategy.md) · [business/sales/Sales-Playbook.md](sales/Sales-Playbook.md) · [business/marketing/Merchant-Personas.md](marketing/Merchant-Personas.md)

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [OneMember Today](#2-onemember-today)
3. [One-Year Outlook](#3-one-year-outlook)
4. [Three-Year Vision](#4-three-year-vision)
5. [Five-Year Vision](#5-five-year-vision)
6. [Strategic Priorities](#6-strategic-priorities)
7. [Growth Phases](#7-growth-phases)
8. [Operating Principles](#8-operating-principles)
9. [Hiring Roadmap](#9-hiring-roadmap)
10. [Financial Principles](#10-financial-principles)
11. [Partnership Strategy](#11-partnership-strategy)
12. [Expansion Strategy](#12-expansion-strategy)
13. [Product Evolution](#13-product-evolution)
14. [Competitive Strategy](#14-competitive-strategy)
15. [Risk Register](#15-risk-register)
16. [Business KPIs](#16-business-kpis)
17. [Quarterly Operating Rhythm](#17-quarterly-operating-rhythm)
18. [Decision Framework](#18-decision-framework)
19. [Culture Principles](#19-culture-principles)
20. [Founder Checklist](#20-founder-checklist)

---

## 1. Executive Summary

### Purpose of the Business

OneMember exists to help independent businesses keep their customers coming back.

Most independent merchants — cafés, salons, restaurants, clinics, gyms, pet groomers — depend on repeat customers for the majority of their revenue. Yet most of them have no structured way to recognise and reward that loyalty. They lose regulars to competitors not because the experience was poor, but because there was nothing specific holding that customer to them.

OneMember is the tool that changes that. A digital loyalty programme that takes 10 minutes to set up, works without a customer app, and runs automatically once it is live. Built for owner-operators who are excellent at their craft and need a simple, affordable tool — not complex software designed for enterprise IT departments.

---

### Mission

> Empower independent merchants to build lasting customer loyalty — without the complexity or cost of enterprise software.

---

### Vision

OneMember will become the go-to customer retention platform for independent and small-chain merchants across Southeast Asia. Every merchant, regardless of technical sophistication, will be able to launch a professional loyalty programme in minutes, retain more customers, and grow repeat business — from a single, affordable, and honest SaaS product.

---

### Core Values

**Simplicity.** We build for the merchant behind the counter, not the analyst with a dashboard. Every feature must be explainable in one sentence. Every screen must have one primary action. If something requires a manual to use, it is not finished.

**Honesty.** We do not exaggerate what our product does. We do not invent outcomes. We do not pressure merchants into decisions. We tell the truth even when it is inconvenient.

**Merchant success first.** Our commercial success is a consequence of merchant success — not the other way around. When those two things conflict, merchant success wins. Every time.

**Long-term thinking.** We are building a business for years, not quarters. We do not sacrifice trust for short-term revenue. We do not compromise quality for speed. We do not take shortcuts that damage the relationship with merchants who depend on us.

**Continuous improvement.** The first version of everything is imperfect. We ship, we listen, we improve. We are never finished — but we are always moving forward deliberately.

---

### Operating Philosophy

OneMember operates as a lean, focused, merchant-first business. We do one thing — customer retention — and we do it well. We do not attempt to become a POS system, a CRM, a marketing agency, or a business intelligence platform. We add features only when they make OneMember better at the one thing it was built to do.

We compete on simplicity, trust, and price — not on feature count. We grow by making existing merchants more successful, which produces referrals, which produces organic growth. We do not chase every market opportunity. We go deep before we go wide.

---

### Founder Principles

These principles are personal commitments that govern how OneMember is run.

1. **Know the merchant.** Every quarter, speak directly with at least five merchants about how they use the product and what would make it better. Do not run the company from analytics alone.

2. **Ship something every week.** Momentum matters. A business that ships nothing for a month loses confidence in itself and signals stagnation to early merchants.

3. **Say no more than yes.** Every feature request that does not pass the Decision Framework is a no. Say it clearly, explain why, and record it. Protecting the product's simplicity is a full-time job.

4. **Fix the critical thing first.** A broken experience for an existing merchant is always more important than a new feature for a future one.

5. **Write things down.** Decisions that exist only in conversation do not exist. Every significant decision gets documented before implementation. This is not bureaucracy — it is the foundation of trust.

6. **Hire slow, trust fast.** Take time to find the right person. When the right person is in the role, give them genuine ownership. Micromanagement is incompatible with the culture we are building.

7. **Protect the long term.** When in doubt about a decision's long-term implications, wait. The urgent path is rarely the right one.

---

## 2. OneMember Today

### Current Product

OneMember Version 1.0 is a multi-tenant SaaS loyalty platform for independent merchants. Built on Laravel 13 / PHP 8.3+, Bootstrap 5, and Alpine.js, it delivers the complete loyalty cycle:

- **Merchant onboarding** via a 6-step wizard that auto-creates a starter campaign
- **Member management** — add, view, edit, archive, with full activity history
- **Points campaigns** — spend-based earning, configurable earn rates, flexible redemption thresholds
- **Stamps campaigns** — visit-based earning, configurable stamp counts, reward on completion
- **Reward management** — create, configure, and archive rewards with quantity limits
- **Purchase recording** — merchant team records purchases at the counter; points/stamps update instantly
- **Reward redemption** — immediate redemption, immutable transaction ledger
- **Dashboard** — KPI cards, quick actions, recent activity, top members, active campaigns
- **Settings** — business profile, preferences (currency, timezone, date format, expiry, birthday), account info, password management
- **Billing** — 30-day Professional trial on sign-up; Stripe integration planned

The product is available in English and Thai (translation parity across all 12 namespaces). Desktop-first responsive design. No customer-facing app required.

---

### Current Launch Market

**Thailand — primary.**
The product was developed with the Thai independent merchant market as the first commercial target. English-language interface for V1.0; Thai-language UI is a V1.x consideration. THB currency support confirmed.

---

### Current Strengths

- **Complete core loyalty cycle.** The earn → accumulate → redeem cycle works end-to-end with real merchant data.
- **Fast onboarding.** The 6-step wizard enables a merchant to go from registration to a live loyalty campaign in under 10 minutes — confirmed in design and testing.
- **No customer app.** The merchant's team manages everything. Customers simply provide a name. This removes the most common adoption barrier in the loyalty software category.
- **Merchant-native language.** The UI uses merchant terminology throughout: Members, Campaigns, Rewards, Activity. Not user/program/redemption/transaction.
- **Both loyalty models.** Points (spend-based) and Stamps (visit-based) in the same product. Merchants choose the model that fits their business.
- **Data integrity.** Immutable transaction ledger. Audit log. Soft deletes. No data is permanently lost.
- **Translation parity.** All 12 language namespaces are in sync between English and Thai.

---

### Current Limitations

- **Billing not yet live.** Stripe integration is planned but not implemented. The product cannot currently generate revenue.
- **No transactional email.** Welcome emails, redemption confirmations, and birthday triggers require the email notification system (planned for Milestone 3).
- **No member search.** Members are accessed by navigating the list. Search and filter are planned.
- **No reports module.** Dashboard provides KPI summary only. A dedicated reports module is planned.
- **No staff accounts.** The product currently supports one account per merchant. Staff account creation and role-based access are planned.
- **No logo upload.** The column exists in the schema but file upload functionality is not implemented.
- **Birthday reward delivery is not automated.** The birthday reward models and types exist, but the scheduler-driven delivery logic is not built.
- **No multi-location support.** Single location per merchant account in V1.0.

---

### What V1.0 Intentionally Excludes

These are deliberate omissions — not limitations. They were excluded to protect product focus and launch timeline.

- POS system or point-of-sale integration
- Customer-facing mobile app (V2.0)
- Marketing automation and mass messaging
- Full CRM features (advanced segmentation, automated triggers)
- E-commerce integration
- Gift cards
- Digital memberships / subscription tiers
- Public API for third-party integration
- Two-factor authentication (V1.x)
- SMS notifications (V1.x)
- Multi-location management (V1.x)
- White-label / reseller version

---

## 3. One-Year Outlook

This section describes where OneMember should realistically be within approximately twelve months of commercial launch. No revenue figures are stated. Targets are directional — they should be set based on real launch-day baselines.

---

### Product Maturity

Milestone 3 (Product Hardening) and Milestone 4 (Commercial Readiness) are complete. The product is commercially live with:

- Stripe billing integrated and tested
- Plan limits enforced per tier
- Transactional email notifications running
- Member search and filter
- Birthday reward automated delivery
- Manual point adjustment for merchants
- Staff account creation and role-based access
- Logo upload
- Reports module (basic — visit frequency, member growth, campaign performance)
- Reward catalogue view

One or two high-priority V1.x features have shipped based on real merchant feedback — not assumptions. The roadmap is being driven by what merchants actually ask for.

---

### Merchant Adoption

A cohort of paying merchants is active across multiple industry types. The most important signal is not the number of merchants — it is the quality of their engagement. Specifically:

- The majority of active merchants log in at least weekly
- Active merchants have a meaningful number of enrolled members
- Some merchants have reached a first reward redemption
- Merchants are adding members consistently, not just at initial setup
- Churn is being tracked and understood

The split between industries is informing which sectors to focus marketing and sales effort on going forward.

---

### Support

Email support is the primary channel. Response SLAs are defined and being met. A support knowledge base is live with answers to the most common questions. The volume of support tickets per merchant is decreasing as onboarding quality improves. No merchant has left because of a support experience.

---

### Brand

OneMember is known in the Thai independent merchant community as a simple, affordable, trustworthy loyalty programme. The brand is not yet large — but it is clean. Early merchants speak positively about it. Referrals are beginning to come in organically. The sales playbook is being used consistently.

---

### Team

At the one-year mark, the founding team has grown thoughtfully. The most likely first hires are a customer success person and a part-time or contract engineer to accelerate Milestone 3 and V1.x development. The founder is not doing everything alone.

---

### Operations

The product is running on managed infrastructure. Daily database backups are automated and tested. Uptime monitoring is in place. The Operations Runbook is followed consistently. There have been no data loss events. The most recent security review was completed within the past six months.

---

## 4. Three-Year Vision

### Company Maturity

OneMember is a small but established SaaS business. Revenue is sufficient to fund ongoing operations and a small team without external dependency. The founder is operating as a CEO — setting direction and priorities — not as the only person doing every job.

The company has a clear identity: the loyalty platform for independent merchants across Southeast Asia. It is not attempting to be all things to all businesses.

---

### Regional Expansion

Thailand is the primary market and the most mature. Singapore or Malaysia has been opened as a second market, with localization, payment gateway, and support in place. The expansion was driven by merchant demand and referrals — not by a desire to be regional.

The decision to expand to each market is made only after achieving sustainable commercial performance in the prior market. Expansion does not happen at the expense of existing merchant quality.

---

### Product Maturity

The core loyalty platform is stable, well-tested, and actively used. V1.x features that have shipped based on merchant demand include at least some of: staff accounts at scale, reporting improvements, multi-location support, and a customer-facing mobile experience.

Every feature on the roadmap was evaluated against the Decision Framework before being built. Features that were requested but did not pass the framework were not built — regardless of how frequently they were asked for.

---

### Customer Success

The merchant experience from sign-up to first redemption is documented, measured, and consistently improving. Merchant churn is tracked and the most common reasons for churn are known and actively addressed. Merchants who have been active for a year are in regular contact with the team.

A customer success function exists — whether as a dedicated hire or a structured process owned by the founder or a team member.

---

### Business Reputation

OneMember has a reputation in the Thai independent merchant community for being honest, simple, and reliable. The brand is what the product and the team have made it — not what marketing has claimed. Merchants recommend it to other merchants because the experience has been genuinely good.

---

## 5. Five-Year Vision

### Long-Term Aspiration

OneMember is the standard loyalty platform for independent and small-chain merchants across Southeast Asia. When a merchant in Bangkok, Singapore, Kuala Lumpur, or Dubai thinks about keeping their customers coming back, OneMember is the natural answer.

The product has evolved from a loyalty platform into a customer retention platform — a broader set of tools that help merchants understand and strengthen their relationship with their customers. This includes loyalty programmes, engagement automation, customer insights, and eventually a customer-facing experience.

It has not become an ERP. It has not become a POS. It has not become a CRM replacement. Everything it does connects directly to one purpose: helping businesses keep customers coming back.

---

### What Will Never Change at Five Years

- **The core promise:** Keep customers coming back.
- **The target customer:** The owner-operator behind the counter, not the IT department.
- **The simplicity standard:** Any feature must be explainable in one sentence and usable without training.
- **The honesty standard:** No invented metrics, no exaggerated claims, no pressure.
- **The merchant-first principle:** Commercial decisions never take priority over merchant wellbeing.

---

### Product at Five Years

The platform includes:

- The full V1.0 loyalty core — points, stamps, rewards, member management — still working simply
- A customer-facing mobile experience (app or PWA) for members to check balances and receive notifications
- Marketing automation — re-engagement emails, birthday triggers, milestone celebrations — managed from the merchant dashboard
- Multi-location support for merchants who have grown
- A public API for POS partners who want to integrate natively
- Potentially: digital gift cards, referral programmes, digital membership tiers

None of these exist because they were interesting ideas. Each one was added because it helps merchants keep customers coming back, passed the Decision Framework, and was built only after the prior stage was stable.

---

## 6. Strategic Priorities

The following priorities are ranked. When resources are constrained — which is most of the time in an early-stage business — the higher-ranked priority wins.

---

**Priority 1 — Merchant success**

If merchants are not succeeding — if they are not adding members, not seeing customers return, not getting value — nothing else matters. Every other priority exists in service of this one. The fastest way to grow OneMember is to make existing merchants successful enough that they refer others.

*Why it ranks first:* Because every piece of growth infrastructure — referrals, word of mouth, case studies, renewal, upsell — depends entirely on merchants achieving a real outcome.

---

**Priority 2 — Product reliability**

The product must work every time. A merchant who cannot record a purchase, add a member, or process a redemption at the counter loses trust immediately — and that trust is very difficult to rebuild. Downtime during business hours is a critical failure.

*Why it ranks second:* A product that merchants cannot rely on is not a product. Reliability is table stakes for SaaS. Everything else is built on top of it.

---

**Priority 3 — Ease of use**

Every interaction a merchant or their staff has with OneMember should be faster and simpler than the alternative. Setup in 10 minutes. Member added in 30 seconds. Purchase recorded in 15. If an action takes longer than it should, it is a product problem — not a training problem.

*Why it ranks third:* Ease of use is what separates OneMember from enterprise software and from competitors who add complexity with every release. It must be actively protected — because complexity accumulates naturally without deliberate effort to prevent it.

---

**Priority 4 — Security**

Merchants trust OneMember with their customer data. That data must be protected with the same seriousness as merchant revenue data. Security is not a feature — it is a commitment. OWASP Top 10, regular reviews, no secrets in logs, proper authentication and authorisation at every layer.

*Why it ranks fourth:* A security breach would damage merchant trust and OneMember's reputation in ways that are very hard to recover from in a small, relationship-driven market.

---

**Priority 5 — Retention**

Keeping existing merchants is more important than acquiring new ones. A business that grows its merchant base while losing merchants at the back at the same rate is not growing — it is running in place. Retention is the single most important commercial metric.

*Why it ranks fifth:* Revenue comes from retained merchants, not from trial starts. Every month a merchant renews is a month of validated product-market fit.

---

**Priority 6 — Product quality**

Quality means: the right features, built well, with no unnecessary complexity, tested, and documented. It does not mean perfection. It means deliberate, consistent improvement. Shipping something imperfect and iterating is better than delaying indefinitely.

*Why it ranks sixth:* Quality is what protects reliability and ease of use over time. Without deliberate quality standards, both of those erode.

---

**Priority 7 — Growth**

Growth — merchant acquisition, market expansion, revenue — is important. But it is ranked seventh deliberately. Growth built on a foundation that is not solid (unreliable product, unhappy merchants, no support capacity) creates problems faster than it creates value.

*Why it ranks seventh:* Sustainable growth is a result of getting priorities 1–6 right. It is not a substitute for them.

---

**Priority 8 — Hiring**

Hiring well is important — and premature hiring is expensive and disruptive. Hire when the need is clear, the role is defined, and the business can genuinely support the position. Hire for values first, skills second.

*Why it ranks eighth:* The right hire at the right time accelerates everything. The wrong hire at the wrong time sets everything back.

---

**Priority 9 — International expansion**

Expanding to Singapore, Malaysia, and the UAE is part of the vision. It is not part of the immediate plan. Each market expansion requires merchant success in the prior market, localization work, payment gateway integration, support capacity, and regulatory review.

*Why it ranks ninth:* Expanding too early dilutes focus and resources. Each market entered badly is harder to recover than a market not yet entered.

---

## 7. Growth Phases

### Phase 1 — Launch

**Goal:** Get the first real merchants using the product with real members and real transactions.

**What success looks like:**
- The product is publicly available
- Stripe billing is live and working
- The first merchants have started trials
- At least some merchants have reached first redemption
- There are no P0 or P1 bugs outstanding
- Support is responding to every ticket

**Entry criteria:** Milestone 3 (Product Hardening) and Milestone 4 (Commercial Readiness) complete; Launch Checklist fully signed off; legal pages published.

**Exit criteria:** A defined number of merchants have completed trials; first paid subscriptions are live; conversion funnel baseline is established.

**Primary risks:**
- Onboarding friction prevents merchants from reaching activation
- Stripe billing issues create a poor upgrade experience
- Support volume exceeds capacity in the first weeks

---

### Phase 2 — Product-Market Fit

**Goal:** Find the specific conditions under which OneMember reliably creates value — for which merchants, in which industries, in which situations.

**What success looks like:**
- A clear understanding of which industries convert best and retain longest
- A clear understanding of what prevents activation for the merchants who don't succeed
- Merchants who have been using the product for 60+ days are still active and adding members
- At least one merchant has referred another
- Churn is understood — the reasons are documented and at least some are being addressed
- The product roadmap is being driven by real merchant feedback, not assumptions

**Entry criteria:** First paid merchants active; conversion data available; support tickets showing real usage patterns.

**Exit criteria:** Consistent retention signal; repeatable activation pattern; a clear ICP that the product clearly serves.

**Primary risks:**
- Activation rate is low — merchants sign up but don't use the product
- The product doesn't fit the most common industry segment as expected
- Merchants churn after 30–60 days because the perceived value doesn't match

---

### Phase 3 — Repeatable Sales

**Goal:** Build a sales and marketing process that consistently brings in the right merchants at a sustainable cost.

**What success looks like:**
- Multiple acquisition channels are working (at least two: organic + one paid or partner)
- Sales conversations follow the playbook consistently
- The time from first contact to trial start is predictable
- Referrals are a meaningful percentage of new trials
- Cost per trial and cost per paid merchant are being tracked

**Entry criteria:** Product-market fit established; ICP confirmed; sales playbook in use; CRM operational.

**Exit criteria:** Growth is no longer founder-dependent; a second person can conduct effective discovery and demo conversations; referral programme formalised.

**Primary risks:**
- Sales conversations are inconsistent — different people give different pitches
- Marketing spend produces trials that don't activate or convert
- Partner channels require more support than they produce

---

### Phase 4 — Regional Expansion

**Goal:** Extend OneMember's proven Thailand model into one or two additional Southeast Asian markets.

**What success looks like:**
- Thailand operations are stable and not requiring constant founder attention
- The first expansion market (likely Singapore or Malaysia) has local payment integration
- At least one local partnership or distribution arrangement is in place in the expansion market
- Support is available in the local language or the market is primarily English-served
- The product is localised for currency, date format, and any relevant regulatory requirements

**Entry criteria:** Thailand is commercially stable; a specific expansion market has been chosen; payment, legal, and support requirements are confirmed.

**Exit criteria:** First paying merchants in the expansion market; support capacity in place; no quality degradation in Thailand from the expansion effort.

**Primary risks:**
- Expansion dilutes founder focus at the expense of Thailand quality
- Payment gateway setup takes longer than expected
- Cultural or market differences require product changes not anticipated

---

### Phase 5 — Platform Growth

**Goal:** Evolve OneMember from a single-purpose loyalty tool to a broader customer retention platform — while maintaining simplicity as the defining characteristic.

**What success looks like:**
- V2.0 features are shipping based on real merchant demand and usage data
- The customer-facing mobile experience exists and is being used by members
- Marketing automation is available for merchants who have outgrown basic loyalty
- The platform supports multi-location merchants
- A public API enables POS vendor integrations
- The engineering team can ship new features without the founder writing code

**Entry criteria:** Revenue is sustainable; team is in place; product core is stable and well-tested; a clear V2.0 roadmap exists and is validated by merchant feedback.

**Exit criteria:** Platform is not yet defined — this phase is ongoing. Exit is into sustained platform maturity.

**Primary risks:**
- Feature expansion makes the product harder to use — violating the simplicity promise
- Platform growth requires technical debt paydown that delays new features
- Growing the team at scale creates culture and quality risks

---

## 8. Operating Principles

### Decision Making

Decisions are made at the level closest to the information. The founder makes strategic and product decisions. In future, role-holders make operational decisions within their domain. When a significant decision requires reversal later, it should be documented with what changed and why.

Every architectural or business decision that affects the product or its direction must be recorded in `docs/08-Product-Decisions.md` before implementation begins. No exceptions.

---

### Speed vs Quality

We ship regularly and we ship with care. These are not opposites. "Ship fast" does not mean ship broken. "Ship with care" does not mean ship slowly.

The standard is: ship something that works, document any known limitations, fix critical issues within one sprint. We do not delay indefinitely waiting for perfection. We do not ship things we know are broken and hope no one notices.

---

### Technical Debt

Technical debt is managed deliberately. We track it. We do not let it accumulate invisibly. When a decision creates technical debt — a workaround, a deferred refactor, a known limitation — it is documented. Debt that affects product reliability or security is addressed promptly. Debt that affects developer experience is addressed in planned maintenance cycles.

We do not add technical debt to meet an arbitrary deadline unless the alternative is missing a critical commercial moment. When we do, we document it and plan the resolution.

---

### Customer Requests

Every merchant request is listened to. Not every request is built. The response to a request that will not be built is honest and direct: "That is not on our current roadmap because [reason]. We are focused on [priority]. We will revisit this when [condition]."

Merchants appreciate honesty. What they do not appreciate is being told a feature is coming and then waiting indefinitely.

Feature requests are logged. When the same request comes from multiple merchants independently, it moves up the priority list. When a request comes from one merchant with unusual needs, it is noted but does not jump the queue.

---

### Feature Acceptance

A feature is accepted into a sprint only if it passes the Decision Framework (Section 18). The framework is not a suggestion — it is a filter. Features that do not pass the filter are not built, regardless of how appealing they seem in isolation.

The Product Owner makes the final call on every feature acceptance. This authority does not transfer.

---

### Support Philosophy

Support is a product function. A support ticket is a signal about the product's quality, clarity, and usability. Every ticket should be answered with the question: "Why did this ticket need to exist? What in the product, documentation, or onboarding should have prevented it?"

Support is not a necessary evil. It is the direct relationship between OneMember and its merchants — and every interaction either strengthens or weakens that relationship.

Response time and resolution quality are both measured. We do not close tickets without confirming the merchant's issue is resolved.

---

### Documentation

Decisions are documented before implementation. Architecture is documented as it is built. Operating procedures are documented as they are established. This is not overhead — it is the mechanism by which the business remains legible as it grows.

A business that runs on undocumented knowledge is fragile. Every process that exists only in someone's head is a risk.

---

### Communication

Internal communication is written and specific. "I'll look into it" is not a commitment. "I'll respond by Wednesday with a recommendation" is. Every handoff — between sprints, between team members, between phases — is documented.

External communication with merchants is warm, direct, and honest. We do not use corporate language. We do not make promises we cannot keep. We do not communicate more than is necessary — but we never communicate less than a merchant needs.

---

## 9. Hiring Roadmap

This section describes the recommended hiring sequence for OneMember, the responsibilities of each role, and the reasoning behind the order. No dates are attached — each hire should happen when the business need is genuinely established and sustainable.

---

### Stage 0 — Founder Only

**Who:** Founder (Product Owner)

**Responsibilities:** Everything. Product decisions, product ownership, sales conversations, merchant support, operations, and strategy. This is the current state.

**When to move past this stage:** When the support volume, sales volume, or product build pace is consistently exceeding what one person can handle without compromising quality.

---

### Hire 1 — Customer Success (Part-Time or Contract)

**Why first:** Merchant activation and retention are the most important commercial signals at launch. A person dedicated to helping merchants succeed — not just answering support tickets, but proactively checking in, identifying friction, and guiding merchants to their first redemption — has direct impact on the metrics that matter most.

**Responsibilities:**
- Onboarding new trial merchants (Day 0, 3, 7 check-ins per the Trial Success Playbook)
- Responding to merchant support emails
- Identifying and escalating product issues from merchant feedback
- Documenting common friction points for the product team
- Merchant success interviews (win/loss, churn)

**Profile:** Empathetic communicator, organised, genuinely curious about small businesses. Does not need to be technical. Must be comfortable learning the product deeply.

---

### Hire 2 — Sales (Part-Time or Contract)

**Why second:** Once the product is commercially live and the ICP is confirmed, systematic merchant outreach becomes the priority. The founder cannot conduct enough sales conversations and run the product simultaneously.

**Responsibilities:**
- Outbound lead generation and qualification
- Discovery conversations
- Product demos
- Trial follow-up and conversion
- CRM management and pipeline hygiene
- Referral programme execution

**Profile:** Consultative by nature. Patient. Genuinely interested in independent businesses. Must read and work from the Sales Playbook — not improvise a different approach.

---

### Hire 3 — Backend Engineer (Full-Time)

**Why third:** By the time Milestone 3 and V1.x features are in demand simultaneously, the founder is unlikely to be able to build, review, and maintain the product at the required pace. A backend engineer focused on Laravel / PHP 8.3+ enables the product roadmap to advance without the founder becoming the bottleneck.

**Responsibilities:**
- Feature development per sprint specifications
- Bug fixes and technical debt management
- Security reviews and patch management
- Code review
- Documentation of technical decisions

**Profile:** Laravel-experienced. Values clean code and documentation. Not easily frustrated by constraints (the technology stack is decided). Comfortable working from a structured product specification.

---

### Hire 4 — Marketing

**Why fourth:** Once repeatable sales are established and the ICP is confirmed, marketing spend and organic content can be systematised. Until then, it is more effective to validate the model through direct sales than to invest in marketing infrastructure.

**Responsibilities:**
- SEO content creation (English and eventual Thai)
- Social media presence (Facebook, Instagram, LinkedIn)
- Paid campaign management (Google Ads, Facebook/Instagram Ads)
- Email newsletter and merchant communication
- Brand consistency across all channels
- Partner and co-marketing coordination

**Profile:** Content-capable, analytical, and patient. Understands that organic marketing for SME SaaS compounds slowly. Not expecting overnight results.

---

### Hire 5 — Frontend Engineer

**Why fifth:** As the product expands into customer-facing experiences (mobile app, member portal) and the merchant dashboard grows, frontend engineering becomes a distinct workload from backend. A dedicated frontend engineer enables product quality improvements without splitting the backend engineer's attention.

**Responsibilities:**
- Dashboard and UI feature development
- Mobile PWA or React Native if a customer-facing app is approved
- Design implementation from product specifications
- Performance optimisation for low-bandwidth contexts (relevant in Thailand)
- Accessibility improvements

**Profile:** Bootstrap/Alpine.js comfortable, or willing to work within the existing stack. Does not require a complex modern JS framework to feel productive.

---

### Hire 6 — QA / Testing

**Why sixth:** As the product and team grow, manual testing by engineers and the founder becomes insufficient. A dedicated QA function protects product reliability as complexity increases.

**Responsibilities:**
- Test case creation and maintenance
- Regression testing before releases
- Bug documentation and reproduction
- Automated test coverage improvement
- Release readiness sign-off

---

### Future Hires (Order TBD by Stage)

**Operations Manager:** When internal processes — support SLAs, billing management, partner coordination, vendor relationships — require dedicated ownership.

**Finance:** When revenue, costs, and cross-border payments create complexity that exceeds what the founder can manage alongside product responsibilities.

**Mobile Developer:** When a customer-facing mobile app is approved and the PWA approach is insufficient.

**HR:** When the team is large enough that hiring, onboarding, and culture maintenance require a dedicated function.

**Legal Counsel:** When expansion into multiple regulated markets, partnership agreements, and enterprise contracts require legal expertise not covered by external advisors.

---

### Hiring Principles

- Hire for values alignment first. Skills are learnable. Trust is not.
- Define the role before hiring. Do not hire a person and then figure out what they do.
- Make hiring decisions with care — removing a poor fit is harder and more costly than waiting longer.
- Default to part-time or contract for the first engagement. Validate fit before committing to full-time.
- Pay competitively within the business's real means. Underpaying the right person is a false economy.

---

## 10. Financial Principles

No revenue, MRR, or valuation figures are stated in this document. Financial targets are set by the Product Owner based on real commercial data after launch.

---

### Revenue Streams

**Primary: Subscription Revenue**
Monthly and annual subscription fees across the Free, Starter, Professional, and Enterprise plan tiers. The 30-day Professional trial is the primary acquisition mechanism. Trial-to-paid conversion is the primary commercial lever.

**Secondary (Future): Partner Revenue**
Referral fees or revenue-share from verified partner channels — POS vendors, agencies, business associations — where a formal partnership programme is established. No partner revenue should be counted on before it is contracted.

**Not a revenue stream:**
- Per-transaction fees (no transaction fees are charged — this is a deliberate product decision)
- Data sale or advertising (merchant data is never monetised; this is a trust commitment, not just a policy)
- Professional services or custom implementation (OneMember is self-service; consulting engagements are not part of the model)

---

### Major Cost Categories

**Infrastructure:** Hosting, database, file storage, CDN, email delivery, monitoring. These scale with merchant count but are generally predictable.

**People:** The largest cost category as the team grows. People costs must be budgeted conservatively — salaries, benefits, and any contract or freelance work.

**Software and tools:** CRM, support platform, design tools, monitoring, CI/CD, and any SaaS subscriptions needed to run operations.

**Marketing and acquisition:** Paid advertising, content production, event attendance, and partner programme costs. This spend should be tied directly to measurable acquisition outcomes.

**Legal and compliance:** Privacy policy and terms maintenance, regulatory review in each market, data protection compliance, and any future contract review.

**Payment processing:** Stripe fees and any local payment gateway costs. These scale with revenue and are a cost of doing business.

---

### Cash Flow Priorities

1. **People.** Salaries and contractor payments are the highest-priority outgoing commitment. Delaying people payments is not an acceptable response to cash pressure.
2. **Infrastructure.** The product must be available. Infrastructure costs are not negotiable.
3. **Customer data safety.** Any backup, security, or monitoring cost that protects merchant data takes priority over discretionary spend.
4. **Growth investment.** Marketing and sales spend is invested when cash allows, not before.

---

### Reinvestment Philosophy

OneMember is built to sustain itself. Revenue is reinvested into product improvement, merchant success, and team growth — in that order. We do not extract cash from the business at the expense of its ability to serve merchants well.

When revenue exceeds operating costs, the priority order for surplus is:
1. Infrastructure headroom (capacity for unexpected growth)
2. Product investment (features that serve existing merchants better)
3. Growth investment (acquiring more merchants)
4. Team compensation improvement
5. Founder compensation

---

### Profitability

The goal is a profitable, sustainable business — not a high-growth venture optimised for exit. Profitability means the business generates more than it spends. This is the primary financial objective. A business that achieves modest but consistent profitability with low churn is more valuable as an operating company than one that grows fast and burns fast.

---

### Pricing Philosophy

Pricing reflects value delivered to merchants — not cost plus margin. The standard against which pricing is calibrated: if OneMember brings back one additional regular customer per week for a merchant, the cost of the subscription should be clearly justified by that single outcome.

Pricing must:
- Be affordable for an independent merchant in Thailand
- Be transparent — no hidden fees, no per-transaction charges, no surprise billing
- Increase predictably with the size and complexity of the merchant's needs
- Not trap merchants — downgrade is always available, data is always safe

Specific plan pricing is set by the Product Owner based on market research and beta feedback. Pricing changes are made deliberately and communicated clearly to existing merchants before they take effect.

---

## 11. Partnership Strategy

### What We Want from Partnerships

A partnership is worth pursuing if it does at least one of the following:

- Brings OneMember to merchants who are otherwise hard to reach
- Reinforces trust with merchants who already know the partner
- Reduces the cost of acquiring qualified merchants
- Adds capability to OneMember's offering without requiring OneMember to build it

A partnership that requires significant ongoing resource without a clear, measurable benefit is not a good partnership.

---

### POS Vendors

**Why:** POS systems are used at the counter by almost every merchant OneMember serves. A POS vendor that recommends OneMember to their merchants creates warm, high-intent referrals at the point of merchant need.

**What we offer:** A complementary tool that does not compete with the POS. OneMember records loyalty events; the POS handles transactions. These can coexist without integration in V1.0 and can be integrated in future via the public API (V2.0+).

**Selection criteria:** POS vendor must serve the same merchant types (independent, SME, food and beverage, retail, services). Must not have a competing loyalty product. Must be actively used by merchants who are our ICP.

---

### Payment Providers

**Why:** Payment providers in Thailand, Singapore, Malaysia, and UAE interact with the same merchants we serve. A relationship with a payment provider can accelerate market entry and referral volume.

**What we offer:** A product that increases merchant retention, which increases merchant payment volume over time. This is a direct commercial benefit to a payment provider.

**Selection criteria:** Must already serve the merchant types we target. Must be willing to formalise a referral arrangement. Must not create dependency on a single gateway.

---

### Marketing Agencies Serving SMEs

**Why:** Agencies that help independent businesses with social media, advertising, and digital marketing frequently encounter merchants who need a retention tool but don't know what to look for. An agency that recommends OneMember to its clients becomes a recurring referral source.

**What we offer:** A straightforward recommendation they can make to any client with a repeat customer base, with referral recognition for conversions.

**Selection criteria:** Must serve the independent merchant market. Must not be recommending a competing product. Must have at least some clients in our target industries.

---

### Business Consultants and Coaches

**Why:** Consultants who work with small business owners have high trust and often help merchants identify operational improvements. OneMember is a natural recommendation in that context.

**Selection criteria:** Must have genuine relationships with business owners (not just aspirational coaches). Must understand customer retention as a concept worth investing in.

---

### Industry Associations

**Why:** Chamber of commerce networks, F&B trade groups, and SME associations in Thailand and other markets create access to large communities of qualifying merchants. Membership endorsement or event partnership creates trust through association.

**What we offer:** A product that helps their members' businesses grow — which reflects well on the association's value to members. Educational content, event presence, and potentially member pricing.

**Selection criteria:** Must represent our target merchant types. Must have genuine member engagement (not just a membership list). Must be willing to share content or co-present at events.

---

### Accountants and Bookkeepers

**Why:** Accountants and bookkeepers are among the most trusted advisors to small business owners. A referral from an accountant carries significant weight. They also often notice when a merchant's repeat revenue is declining — the exact moment OneMember becomes relevant.

**Selection criteria:** Must serve SME clients in our target markets. Must be willing to recommend retention tools as part of business health advice. Must understand that this is not a financial tool — it is a customer relationship tool.

---

### Partner Selection Criteria (Summary)

| Criterion | Why |
|-----------|-----|
| Serves our ICP merchants | Ensures partner referrals are qualified |
| Does not compete with OneMember | Avoids conflict of interest |
| Has genuine merchant relationships | Referrals only work if the partner has trust with the merchant |
| Can generate measurable referrals | Partnerships without accountability are not partnerships |
| Aligns with OneMember's values | A partner who high-pressures merchants damages our brand |
| Willing to formalise the arrangement | Handshake partnerships fade without documented terms |

---

## 12. Expansion Strategy

### Thailand (Primary Market)

**Why:** The development and validation market. Personal network, market knowledge, and direct feedback availability make Thailand the right first market. Large independent merchant base with limited access to affordable loyalty software. THB confirmed. English V1.0 with Thai UI planned for V1.x.

**Readiness requirements:** Full commercial readiness — billing, email notifications, support capacity, legal pages — before announcing public availability.

**Localisation:** Currency (THB), date format (DD/MM/YYYY), timezone (Asia/Bangkok). Thai-language UI is a V1.x consideration based on merchant demand, not a V1.0 requirement.

**Support:** English-language support in V1.0. Thai-language support to follow as demand warrants.

**Regulatory considerations:** Personal Data Protection Act (PDPA Thailand, 2022) governs collection and handling of member data. Privacy Policy must comply. Consent must be captured appropriately.

**Payment:** PromptPay and major Thai card acceptance via Stripe or a local gateway. Local payment integration must be tested before commercial launch.

---

### Singapore (Second Market)

**Why:** High digital adoption. Merchants are accustomed to SaaS pricing. English-first market. Regulatory clarity. Strong SME support ecosystem. Currency (SGD) and payment (Stripe) are straightforward.

**Readiness requirements:** Thailand operations stable and not requiring constant founder attention. SGD pricing determined. Stripe tested in Singapore mode. Singapore-specific legal review complete (PDPA Singapore / PDPC). Local partnership or distribution identified.

**Localisation:** SGD currency. Date format the same. English UI is sufficient. No language localisation required.

**Support:** English-language support is appropriate for Singapore.

**Regulatory considerations:** Personal Data Protection Act (Singapore, administered by PDPC). Data must be handled in compliance with Singapore's PDPA. Privacy Policy reviewed for Singapore.

**Payment:** Stripe is available and well-adopted in Singapore. PayNow integration may be valuable for merchants who use it as a payment method. Evaluate based on merchant feedback.

**Language:** English. No additional language requirement for V1.0 Singapore.

---

### Malaysia (Third Market)

**Why:** Large Malay and Chinese SME community. Cultural similarity to Thailand. Growing SaaS adoption. Bilingual English/Malay market reduces localisation burden vs. a non-English market.

**Readiness requirements:** Singapore operations stable. MYR pricing determined. Local payment integration (FPX, Maybank, or Stripe) tested. Malaysian legal review complete (PDPA Malaysia 2010). Local distribution or partnership identified.

**Localisation:** MYR currency. Date format the same. Malay language for UI is a future consideration if demand exists; English is acceptable at entry.

**Support:** English-language support is appropriate for Malaysia at market entry. Malay-language support to follow if demand warrants.

**Regulatory considerations:** Personal Data Protection Act 2010 (Malaysia). Data handling must comply. Privacy Policy reviewed for Malaysia.

**Payment:** Stripe is available. FPX (Financial Process Exchange) is widely used by Malaysian SMEs and should be evaluated for integration.

**Language:** English sufficient at entry. Malay localisation if merchant adoption warrants.

---

### UAE (Fourth Market)

**Why:** High-income market. Growing SME ecosystem in Dubai and Abu Dhabi. English-friendly for business. Appetite for modern SaaS tools among retail and F&B operators. Strong café culture, high-end salon and spa sector, premium retail.

**Readiness requirements:** At least two prior markets stable. AED pricing determined. Local payment gateway tested (Stripe is available). UAE data localisation requirements understood. Local partnership or distribution identified. Potential need for Arabic-language support assessed.

**Localisation:** AED currency. Date format the same. Calendar considerations (working week in UAE runs Sunday–Thursday in some contexts — check impact on dashboard date displays). Arabic UI is a significant localisation effort; English is appropriate at entry for most merchant contexts.

**Support:** English-language support is appropriate for UAE market entry. Arabic support is a future consideration for broader market penetration.

**Regulatory considerations:** UAE does not currently have a comprehensive federal data protection law equivalent to GDPR, though DIFC and ADGM (Dubai and Abu Dhabi free zones) have their own data protection frameworks. Any merchant in those free zones is subject to those rules. This requires legal review before UAE commercial launch.

**Payment:** Stripe is available in UAE. Local payment methods (Telr, PayFort/Amazon Payment Services, Checkout.com) are used by UAE merchants and should be evaluated.

**Language:** English at entry. Arabic for broader market access — a significant investment that should be deferred until UAE market size justifies it.

---

## 13. Product Evolution

### The Evolution Path

OneMember's product evolution follows a deliberate sequence. Each stage builds on the prior one. No stage is entered before the previous stage is stable, well-adopted, and commercially validated.

---

**Today: Loyalty Platform**

What it is: A digital loyalty programme for independent merchants. Points, stamps, rewards, member management. The merchant team runs everything. Customers need no app.

What it does well: The complete loyalty cycle in 10 minutes of setup. Simple. Reliable. Affordable.

What it intentionally does not do: Marketing automation, customer-facing experiences, multi-location management, POS integration.

---

**Next: Customer Retention Platform**

What changes: The platform expands beyond passive loyalty (earn and redeem) to active retention — automated re-engagement, birthday triggers, milestone communications. The merchant gains the ability to act on retention data, not just collect it.

New capabilities:
- Automated birthday reward delivery (the first automation, already planned for V1.0 completion)
- Re-engagement email sequences for members who haven't visited in a defined period
- Milestone celebrations ("You've earned your 10th stamp!")
- Basic segmentation: identify at-risk members (low recent activity) and top members (highest frequency)

What does not change: The simplicity of the core loyalty cycle. The merchant-managed model. The no-customer-app default.

---

**Future: Merchant Operating Platform**

What changes: OneMember becomes the primary interface through which a merchant understands and manages their customer relationships — not just their loyalty programme.

New capabilities:
- Customer-facing mobile experience (app or PWA) for members to check balances, receive notifications, and redeem rewards from their phone
- Multi-location management
- Public API for POS integration
- Advanced reporting — member lifetime value, campaign ROI, retention cohort analysis
- Digital gift cards
- Referral programmes (member-get-member)

What does not change: The merchant's experience remains simple. New capabilities are additions, not complications. A merchant who only wants to run a stamp card programme still has a simple stamp card programme.

---

**Aspiration: Business Growth Platform**

What changes: OneMember helps merchants not just retain customers but understand how to grow — which campaigns produce the best return, which customer segments matter most, which times of year create the most loyal customers.

New capabilities:
- AI-assisted campaign recommendations based on merchant's own data
- Predictive churn alerts for members who are at risk
- Industry benchmarking (how does this merchant compare to similar businesses)
- Integration with marketing channels (email provider, SMS)

What does not change: The founding commitment to simplicity. The merchant-first philosophy. The no-enterprise-complexity promise. These are not constrained by stage — they are permanent.

---

### What Should Never Change

Regardless of how the product evolves:

1. **Setup must always be achievable in under 15 minutes.** If adding a new feature requires extending the setup experience beyond this, it has been designed wrong.

2. **Customers must never be required to download an app to participate in basic loyalty.** A customer-facing app is an enhancement, not a requirement. The default experience — give your name at the counter — must always work.

3. **The core loyalty cycle must always be simple.** Add a member. Record a purchase. Issue a reward. This must always be explainable to a new staff member in under two minutes.

4. **Merchant data must always be portable.** A merchant must always be able to export their member data regardless of their plan or subscription status.

5. **OneMember must never become a POS, ERP, or full CRM.** Each of those products is its own category with its own specialist competitors. Entering those categories would dilute focus without meaningful competitive advantage.

---

## 14. Competitive Strategy

### How OneMember Competes

OneMember does not compete with enterprise loyalty platforms. It does not compete with Salesforce, Oracle, or any software designed for large retail chains. Those products are built for a different customer with a different budget and a different set of problems.

OneMember competes with:
- Paper stamp cards (the most common alternative)
- No loyalty programme at all (the default for most independent merchants)
- Generic app-based loyalty tools that require customers to download something
- Expensive or complex loyalty software that independent merchants start and abandon

In each of these comparisons, OneMember wins on: simplicity, ease of use, no customer app requirement, price relative to outcome, and the quality of the merchant experience.

---

### How OneMember Wins

**On simplicity:** 10 minutes to set up. 30 seconds to add a member. 15 seconds to record a purchase. These are facts, not claims. When a merchant compares this to the complexity of alternatives, the comparison is immediate and decisive.

**On the no-app promise:** The single most cited barrier to loyalty programme adoption among independent merchants is: "My customers won't download an app." OneMember removes this objection entirely. The merchant's team manages everything. This is a structural advantage that is very hard to replicate without rebuilding the product from the ground up.

**On price:** OneMember is priced for owner-operators, not IT departments. The cost should be clearly justified by a single retained regular per week. This is a value proposition that resonates with cost-conscious merchants.

**On trust:** We do not exaggerate. We do not pressure. We do not invent outcomes. In a market where many software vendors overpromise, the reputation for honesty and straightforwardness is a differentiator.

**On speed:** A merchant can be from knowing nothing about OneMember to having a live loyalty programme with their first member added on the same day. That speed of value delivery is a competitive advantage that most loyalty platforms cannot match.

---

### How OneMember Should Never Compete

**Not on feature count.** Adding features to match a competitor's list is the fastest way to make the product worse. OneMember's simplicity is not a limitation — it is the product. The moment we start adding features to compete on a feature checklist, we begin losing the thing that makes us worth using.

**Not on price alone.** Competing on price makes the product a commodity. We compete on value — the outcome delivered relative to cost. Price is set at the level where value is obvious. If we lower price without increasing value, we have just reduced our ability to invest in the product.

**Not on enterprise territory.** Do not try to win an enterprise deal by promising capabilities OneMember does not have. The wrong merchant in the product creates support cost, churn, and bad word of mouth. Qualify out rather than oversell.

**Not by criticising competitors.** We do not disparage other products in sales conversations. We describe what we do. We let merchants evaluate the difference themselves.

---

## 15. Risk Register

### Risk Assessment Scale

**Likelihood:** L (Low — unlikely in normal operations), M (Medium — plausible), H (High — likely without mitigation)
**Impact:** L (Low — manageable without operational disruption), M (Medium — significant but recoverable), H (High — major disruption or existential)

---

### Technology Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| T1 | Database corruption or data loss | L | H | Daily automated backups; tested restore procedures per Backup and DR Plan; immutable transaction ledger | Founder |
| T2 | Application downtime during peak hours | M | H | Uptime monitoring with alerting; Operations Runbook for response; managed hosting with auto-scaling | Founder |
| T3 | Security breach — member data exposed | L | H | OWASP Top 10 review before launch; regular security audits; no sensitive data in logs; DECISION-016 | Founder |
| T4 | Third-party dependency failure (Stripe, email provider, hosting) | M | M | Monitor all third-party services; have contingency plans for each critical dependency | Founder |
| T5 | Stripe billing failure at upgrade moment | M | H | Test billing end-to-end before launch; implement retry logic; alert on failed charges | Engineer |
| T6 | Performance degradation as merchant base grows | M | M | Load testing before public launch; query optimisation; caching; Performance Optimization doc | Engineer |
| T7 | Technical debt accumulation blocks new feature development | M | M | Quarterly debt review; debt logged in DECISION doc; maintenance cycles budgeted | Founder/Engineer |
| T8 | Laravel or PHP version end-of-life | L | M | Monitor Laravel LTS schedule; plan upgrade cycles in advance | Engineer |

---

### Competition Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| C1 | A well-funded competitor enters the Thai market | L | M | Compete on simplicity and trust, not features; deepen merchant relationships; focus on the independent merchant segment competitors tend to ignore | Founder |
| C2 | An existing competitor lowers price significantly | M | M | Price competition is manageable if merchants value the product — focus on activation and retention quality; do not match unsustainable pricing | Founder |
| C3 | A POS vendor adds a loyalty module that overlaps with OneMember | M | M | Differentiate on depth, simplicity, and merchant-first design; POS loyalty modules are typically shallow; maintain partnership relationships with POS vendors | Founder |
| C4 | A global platform (Shopify, Square, etc.) expands loyalty features | L | M | These platforms serve a different customer profile; their loyalty features are designed for e-commerce, not the counter-service independent merchant | Founder |

---

### Operations Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| O1 | Support volume exceeds capacity at launch | H | M | Build comprehensive onboarding and help documentation pre-launch; set realistic SLA expectations; hire Customer Success before becoming overwhelmed | Founder/CS |
| O2 | Inconsistent support quality as team grows | M | M | Document all support processes; use standard response templates; quality-review support tickets regularly | CS Hire |
| O3 | Critical process exists only in founder's head | H | M | Document every significant process before delegation; Operations Runbook is the foundation; no process should require the founder's specific knowledge to execute | Founder |
| O4 | Key vendor relationship fails without warning | M | M | No single-vendor dependency for critical infrastructure; maintain alternative options for email, hosting, and payment | Founder |
| O5 | Launch delayed by unresolved Milestone 3 or 4 items | M | M | Track remaining items explicitly; do not announce public availability before launch readiness is confirmed | Founder |

---

### Finance Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| F1 | Pricing is too high for the Thai market | M | H | Do not set final pricing until beta feedback is collected; start with a generous trial to reduce price sensitivity; validate price point with 10+ merchants before locking | Founder |
| F2 | Pricing is too low — business is unsustainable | M | H | Build cost model before setting pricing; factor in infrastructure, people, and marketing costs; model break-even scenario | Founder |
| F3 | Trial-to-paid conversion is lower than expected | M | H | Focus on activation (first redemption) as primary conversion driver; improve onboarding continuously; do not rely on conversion assumptions until real data exists | Founder |
| F4 | Merchant churn is higher than expected | M | H | Track churn by industry and by stage; conduct churn interviews; address root causes rather than discounting to retain; do not retain merchants who are not a good fit | Founder/CS |
| F5 | Payment gateway delays in a new market | M | M | Do not launch in a market without a tested, working payment gateway; identify alternatives before committing to a timeline | Founder |
| F6 | Cash flow gap due to annual subscription timing | L | M | Manage cash conservatively; understand cash flow timing by plan type; build a buffer before large spend commitments | Founder |

---

### Hiring Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| H1 | Wrong first hire sets back progress significantly | M | H | Define role and responsibilities clearly before hiring; use probationary contract where appropriate; hire for values first | Founder |
| H2 | Engineer hire introduces technical debt or quality issues | M | M | Establish code review process from day one; pair with founder initially; use automated testing as a gate | Founder |
| H3 | Premature hiring strains cash before revenue is sufficient | M | H | Hire in sequence aligned with commercial milestones; part-time or contract first; do not hire ahead of revenue | Founder |
| H4 | Sales hire deviates from consultative approach | M | M | Sales Playbook is non-negotiable onboarding material; founder joins early sales conversations; CRM review shows approach | Founder |
| H5 | Culture dilution as team grows | M | M | Culture Principles (Section 19) are documented and revisited at every team meeting; hire for culture fit explicitly | Founder |

---

### Customer Success Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| CS1 | Merchants sign up but do not activate | H | H | Day 3 and 7 check-ins per Trial Success Playbook; improve onboarding based on friction signals; identify the bottleneck stage and address it first | Founder/CS |
| CS2 | A merchant's data is lost due to a product error | L | H | Immutable transaction ledger; daily backups tested; audit log; no hard deletes on member data | Founder |
| CS3 | A well-known merchant leaves publicly | L | H | Early merchants receive more personal attention; identify churn risk before it becomes public; resolve issues before escalation | Founder |
| CS4 | Merchant success depends entirely on the founder | H | M | Document the Trial Success Playbook before the first merchant starts; hire Customer Success before being overwhelmed | Founder |
| CS5 | Merchants don't understand how to use the product without help | M | M | Improve onboarding documentation; in-app guidance; help articles; reduce support tickets by fixing product friction | Founder |

---

### Security Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| S1 | SQL injection or XSS in the application | L | H | Eloquent ORM parameterised queries; Blade template escaping; OWASP review before launch; DECISION-016 | Engineer |
| S2 | Credential stuffing attack on merchant accounts | M | M | Rate limiting on auth endpoints; password hashing; email verification; monitor for unusual login patterns; future: 2FA | Engineer |
| S3 | Secrets or credentials exposed in logs or code | M | H | No logging of passwords, tokens, API keys, or PII; .env never committed; Production Security Review | Engineer |
| S4 | Stripe webhook spoofing | M | H | Verify all Stripe webhooks using signature validation; never trust webhook payload without verification | Engineer |
| S5 | Unauthorised cross-merchant data access | L | H | Merchant isolation enforced in every controller; `abort_unless` ownership check; regular testing of tenant isolation | Engineer |

---

### Legal Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| L1 | PDPA Thailand non-compliance | M | H | Privacy Policy reviewed and published before launch; member data consent captured appropriately; data retention policy defined | Founder |
| L2 | PDPA Singapore or Malaysian equivalent non-compliance on expansion | M | M | Legal review before entering each market; Privacy Policy updated per jurisdiction; do not launch in a market without confirmed compliance | Founder |
| L3 | Merchant disputes about billing | M | M | Transparent billing; clear cancellation and downgrade process; support responsive; terms of service clear | Founder |
| L4 | Intellectual property infringement (unintentional) | L | M | Use only licenced libraries and assets; document all third-party dependencies and their licences | Engineer |

---

### Market Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| M1 | Product-market fit does not emerge as expected | M | H | Define PMF criteria before assuming it; conduct win/loss interviews; adjust ICP based on real data rather than assumption | Founder |
| M2 | Thai merchant market adoption is slower than expected | M | M | Extend beta period; focus on one high-density industry before broad launch; adjust acquisition strategy based on what actually works | Founder |
| M3 | Economic conditions reduce SME spending on SaaS | M | M | Price at a level where value is obvious even in constrained conditions; maintain a free tier that keeps merchants in the product | Founder |
| M4 | Cultural factors affect merchant reception in a new market | M | M | Do not expand to a market without either local knowledge or a local partner who has it | Founder |

---

### Partnership Risks

| # | Risk | Likelihood | Impact | Mitigation | Owner |
|---|------|-----------|--------|-----------|-------|
| P1 | A partner refers poor-fit merchants | M | M | Define ICP clearly for partners; review early partner referrals before accepting; do not accept every partner referral | Founder |
| P2 | A partner damages OneMember's reputation through their own conduct | L | H | Partner selection criteria include values alignment; exit terms in partnership agreements; monitor partner-referred merchant satisfaction | Founder |
| P3 | A partner integrates a competing product without notice | M | M | Monitor partner relationships; do not build deep dependency on any single partner's referral pipeline | Founder |
| P4 | POS integration creates technical debt or support burden | M | M | No POS integration in V1.0; any integration in future requires DECISION entry and engineering review; do not commit to custom integrations | Engineer |

---

## 16. Business KPIs

Targets are not defined in this document. Targets are set by the Product Owner after launch baselines are established.

---

### Growth

| KPI | What It Measures |
|-----|-----------------|
| Trial sign-ups per month | Rate of new merchant acquisition entering the trial |
| Trial sign-up source breakdown | Which channels are producing trials (organic, paid, referral, partner) |
| Qualified leads generated | Prospects that pass qualification criteria before demo |
| Demo-to-trial conversion rate | Efficiency of the sales process |
| Net new paying merchants per month | Commercial growth rate |
| Total paying merchants | Cumulative commercial scale |

---

### Retention

| KPI | What It Measures |
|-----|-----------------|
| Trial-to-paid conversion rate | Most important commercial KPI; signal of product-market fit |
| Monthly merchant churn rate | % of paying merchants who cancel in a given month |
| Average merchant tenure | Months from first payment to cancellation |
| Merchant retention at 90 days | Early signal of long-term retention |
| Merchant retention at 12 months | Indicator of sustainable commercial health |
| Churn reasons distribution | Qualitative breakdown of why merchants leave |

---

### Customer Success

| KPI | What It Measures |
|-----|-----------------|
| Onboarding completion rate | % of trial merchants who complete the setup wizard |
| Time to first member (hours) | Speed of initial activation |
| Time to first purchase recorded (hours) | Depth of initial activation |
| Time to first redemption (days) | Full activation — the key retention milestone |
| Full activation rate | % of trial merchants who reach first redemption |
| Members added per merchant per month | Merchant programme growth signal |
| Purchases recorded per merchant per month | Merchant programme usage signal |
| Rewards redeemed per merchant per month | Evidence of the loyalty cycle completing |
| Average members per active merchant | Depth of merchant adoption |

---

### Product

| KPI | What It Measures |
|-----|-----------------|
| Uptime percentage | Product reliability |
| Mean time to resolve critical bugs | Product quality response speed |
| Open P0/P1 bugs | Current product health |
| Sprint velocity (features shipped per sprint) | Development pace |
| Test coverage percentage | Code quality signal |
| Support tickets per merchant per month | Product friction indicator — should decrease over time |

---

### Support

| KPI | What It Measures |
|-----|-----------------|
| Median first response time | Support responsiveness |
| Median resolution time | Support effectiveness |
| Merchant satisfaction with support (qualitative) | Support quality signal |
| Ticket volume per merchant per month | Product clarity and onboarding quality |
| Most common ticket topics | Where to focus product and documentation improvement |
| Tickets opened per new merchant in first 30 days | Onboarding friction indicator |

---

### Operations

| KPI | What It Measures |
|-----|-----------------|
| Uptime per month | Infrastructure reliability |
| Time to restore from backup (last tested) | Disaster recovery readiness |
| Security review age (months since last review) | Security posture currency |
| Open Operations Runbook items | Operational health |
| Infrastructure cost per merchant | Unit economics of growth |

---

### Sales

| KPI | What It Measures |
|-----|-----------------|
| Lead-to-qualified rate | Prospecting efficiency |
| Discovery-to-demo conversion | Sales conversation quality |
| Demo-to-trial conversion | Demo effectiveness |
| Sales cycle length (days from first contact to trial) | Sales process efficiency |
| Referrals as percentage of new trials | Word-of-mouth and programme health |
| Referral conversion rate vs direct | Quality of referred trials |

---

### Marketing

| KPI | What It Measures |
|-----|-----------------|
| Website visitors per month | Top-of-funnel reach |
| Trial sign-up conversion rate (visitors to trials) | Website and landing page effectiveness |
| Cost per trial sign-up (paid channels) | Paid marketing efficiency |
| Cost per paying merchant | Blended acquisition cost |
| Content performance (organic search traffic, engagement) | SEO and content marketing effectiveness |
| Social media engagement | Brand presence and reach |

---

### Finance

| KPI | What It Measures |
|-----|-----------------|
| Monthly Recurring Revenue (MRR) | Core revenue signal |
| MRR growth rate month-over-month | Revenue growth momentum |
| Average Revenue Per Merchant (ARPM) | Revenue per customer quality |
| Plan distribution (Free / Starter / Professional / Enterprise) | Revenue concentration and upgrade opportunity |
| Monthly churn MRR | Revenue at risk from cancellations |
| Net Revenue Retention | Whether existing merchants are expanding or contracting spend |
| Cash balance | Business financial health |
| Monthly burn rate | Operational cost sustainability |

---

## 17. Quarterly Operating Rhythm

### Weekly (Founder Review — Solo Stage)

**Suggested weekly focus (30–60 minutes):**
- Review open support tickets — any pattern worth addressing in the product?
- Check merchant activity dashboard — any merchants who haven't logged in this week that warrant a check-in?
- Review this week's sales pipeline — any conversations stalled that need follow-up?
- Ship or confirm shipping a product update, bug fix, or improvement
- Write down any decision or question that came up during the week — does it belong in `docs/08-Product-Decisions.md`?

---

### Monthly (Operations Review)

**Suggested monthly review (2–3 hours):**

**Product:**
- What shipped this month?
- What is in progress?
- What were the top 3 support tickets — do any of them require a product change?
- Is any technical debt becoming urgent?

**Merchants:**
- How many new merchants started trials this month?
- How many converted to paid?
- How many churned? What reasons were given?
- Is activation rate improving or declining?
- Are there merchants who haven't logged in for 14+ days who need a check-in?

**Sales:**
- What was the source of new trials this month?
- Is the qualification rate improving?
- Which industry converted best this month?
- Are referrals occurring?

**Operations:**
- Was there any downtime or incident this month? What was the root cause and resolution?
- Is the backup process running as expected? When was the last successful test restore?
- Are all SaaS subscriptions and vendor bills current?

**Finance:**
- What is current MRR?
- What is current cash balance?
- Are there any unexpected cost increases?

---

### Quarterly (Strategy Review)

**Suggested quarterly review (half day):**

**Review this Business Operating Plan:**
- Is the current Growth Phase accurate?
- Are the Strategic Priorities still correctly ordered?
- Has anything in the Risk Register materialised that was not anticipated?
- Are the KPIs being measured? Are any of them missing baselines that need to be established?
- Does the Hiring Roadmap need updating based on what actually happened?

**Product:**
- Review the roadmap against actual merchant feedback
- Prioritise the next quarter's development focus
- Deprecate or defer any features that no longer belong in the near-term plan
- Update `docs/10-Version-1.0-Roadmap.md` to reflect current state

**Business:**
- What was the biggest commercial win this quarter? What made it happen?
- What was the biggest commercial loss or disappointment? What caused it?
- Is product-market fit closer, further, or clearer than at the start of the quarter?
- Are there any partnerships that should be formalised or discontinued?

**Team (when applicable):**
- Is the current team structure right for the next quarter?
- Is there a hire that should be initiated?
- Is anyone underperforming or in the wrong role?

**Sales Playbook review:**
- Are new objections appearing that need to be documented?
- Has the ICP changed based on what is actually converting?
- Are the email templates performing?

**Complete the Founder Checklist (Section 20).**

---

### Annual (Vision Review)

**Suggested annual review (full day, ideally with a trusted advisor):**

- Review the three-year and five-year visions against where the business actually is
- Update the expansion strategy based on Thailand performance and market opportunity
- Review and update this Business Operating Plan to reflect what has changed
- Review all key documents and note anything that is outdated or no longer accurate
- Set the priority focus for the next 12 months
- Assess the team — is it the right team for the next phase?
- Review the Risk Register — are there risks that have materialised? New risks not on the list?
- Conduct a personal review as founder: Is this the business you set out to build? Is the culture what you intended?

---

## 18. Decision Framework

### The Framework

Before approving any initiative — feature, hire, partnership, market expansion, or operational change — apply the following questions. The framework is not a checklist to be satisfied — it is a thinking tool to ensure that decisions are made consciously, not reactively.

---

**Question 1: Does this help merchants keep customers coming back?**

This is the primary filter. Everything OneMember does should connect to this outcome. If a feature, a partnership, or a programme cannot be connected to merchant retention improvement, it does not belong in OneMember's roadmap.

Acceptable answers: "Yes, directly." / "Yes, indirectly, because it enables X which helps with Y." / "No — and that's why we're declining it."

Unacceptable answer: "It might help somewhere." Vague connection is not a connection.

---

**Question 2: Will at least 20% of active merchants use it?**

A feature used by fewer than 20% of merchants is a niche feature. Niche features add complexity for the majority of merchants who never use them. They increase support burden. They make the codebase harder to maintain.

The 20% threshold is not a hard rule — it is a calibration tool. A feature that 15% of merchants would find transformative and 85% would never notice may still be worth building. One that 5% of merchants would use and 95% would see as clutter is not.

---

**Question 3: Does it improve acquisition, activation, retention, or referrals?**

OneMember's growth depends on four levers: getting merchants to try it (acquisition), getting them to their first redemption (activation), keeping them beyond 90 days (retention), and having them recommend it to others (referrals).

Any feature, initiative, or investment should improve at least one of these — and ideally more than one. If it does not contribute to any of the four, it is a distraction.

---

**Question 4: Can it be explained in one sentence?**

This is both a simplicity test and a communication test. If a feature requires a paragraph to explain what it does, it is probably too complex for OneMember's target audience. A merchant behind a counter should be able to understand a new feature in the time it takes for a customer to pay.

If you cannot write the one sentence, go back and simplify the feature.

---

**Question 5: Can we support it well?**

A feature that cannot be supported — because the team lacks capacity, because the documentation isn't written, because the UX is unclear — creates merchant frustration. Merchants who cannot get help with a feature they are trying to use become unhappy merchants.

If the team cannot currently support a feature well, either build the support capacity first or defer the feature.

---

**Question 6: What complexity does it add?**

Every feature adds some complexity — to the codebase, to the UI, to the documentation, to the support queue. The question is whether the value delivered justifies the complexity added. Features that add large complexity for small value should be declined.

Complexity compounds. A product that makes ten small complexity additions over a year is significantly more complex than it was at the start. This is how software becomes hard to use.

---

**Question 7: What must we say no to if we say yes?**

Every yes is a no to something else. Saying yes to a new feature means engineering time that does not go to another feature, documentation, bug fixes, or technical debt. Saying yes to a new market means attention that does not go to the primary market.

Make the trade-off explicit. If you cannot identify what is being traded away by this decision, you have not thought it through fully.

---

### Decision Record Requirement

Any decision that passes the framework and is approved must be recorded in `docs/08-Product-Decisions.md` before implementation begins. This applies to:

- New product features
- Technology stack changes
- Architecture decisions
- Market expansion decisions
- Major partner commitments
- Significant pricing changes

Decisions documented after implementation are less useful than decisions documented before. The purpose of the record is to enable conscious decision-making — not to create a post-hoc paper trail.

---

## 19. Culture Principles

Culture is what happens when no one is watching. It is not a mission statement on a wall — it is the accumulated effect of thousands of small decisions made every day. These principles describe the culture OneMember is building and must protect as it grows.

---

**Merchant-first.**

In every meeting, every product decision, every support conversation, and every hiring interview, the question "what is best for the merchant?" is the right starting point. Not "what is easiest for us?" Not "what is most impressive to investors?" Not "what does the competitor do?"

When we are uncertain, we ask a merchant. When we are making a trade-off, we favour the merchant. This is not altruism — it is the right commercial strategy. Merchants who are treated well stay, refer, and expand.

---

**Honesty.**

We say what we mean. We say what we don't know. We say when something went wrong and what we did about it. We do not exaggerate results. We do not bury problems. We do not tell merchants what they want to hear when it isn't true.

Honesty has a short-term cost and a long-term return. The short-term cost is discomfort. The long-term return is trust. We choose the long-term return.

---

**Curiosity.**

We ask why before we decide. We ask what merchants actually mean when they make a request. We ask what changed when a metric moves. We ask whether our assumptions are still true.

Curiosity is the antidote to arrogance. A team that asks good questions is a team that learns.

---

**Continuous improvement.**

The first version of everything is imperfect. This is not a failure — it is a starting point. We iterate. We improve. We do not accept the status quo when it could be better. We do not improve for its own sake when it is already good enough.

The standard is: better than last quarter. Not perfect. Better.

---

**Ownership.**

Everyone who works at or with OneMember takes responsibility for outcomes, not just tasks. "That's not my job" is not a complete answer in a small team. When something breaks or goes wrong, we focus first on the fix, then on the learning, then on the prevention. We do not spend time on blame.

---

**Simplicity.**

The most common failure mode in software companies is adding complexity that feels like progress. Simplicity requires active effort. It requires saying no. It requires doing less than what is technically possible. It requires trusting that a simple product used consistently is more valuable than a complex product used occasionally.

We protect simplicity in the product. We protect simplicity in our internal processes. We protect simplicity in our communication.

---

**Humility.**

We do not assume we are right. We do not assume our instincts are better than merchant feedback. We do not assume our model is proven before we have the data. We ship, we listen, we adjust.

Humility is not weakness. It is the foundation of learning.

---

**Long-term thinking.**

The questions we ask are: "Will we be proud of this decision in three years?" and "Does this make the business stronger over time?" Short-term shortcuts that compromise trust, quality, or merchant wellbeing are not shortcuts — they are detours.

We measure success in years, not quarters.

---

## 20. Founder Checklist

Review this checklist every quarter. It is designed to surface the questions that are easy to defer when the business is busy — and that matter most when they are ignored.

---

### Product

- [ ] Is the product stable? Any recurring bugs or reliability issues that have not been addressed?
- [ ] Is onboarding completing in under 15 minutes for a new merchant?
- [ ] Are there any features that were promised that have not shipped?
- [ ] Is the Decision Framework being applied before every feature is accepted?
- [ ] Is the `docs/08-Product-Decisions.md` file current and up to date?
- [ ] Is there any technical debt that is now urgent?
- [ ] Have the top 5 support ticket types this quarter generated any product changes?

---

### Customers

- [ ] Have you spoken directly with at least 5 merchants this quarter (not via support tickets)?
- [ ] What is the current activation rate? Is it improving?
- [ ] What is the current churn rate? Do you know why merchants are leaving?
- [ ] Are there any merchants at significant risk of churning that haven't been contacted?
- [ ] Have any merchants publicly praised or publicly criticised the product? Have you acknowledged both?
- [ ] Is there a merchant who is wildly successful and hasn't been asked for a referral?

---

### Sales

- [ ] Is the Sales Playbook current and being used consistently?
- [ ] What percentage of new trials came from referrals this quarter?
- [ ] Is the pipeline clean — every prospect has a current stage and a next action?
- [ ] Are there any stalled deals that should be disqualified?
- [ ] Has any new objection appeared that should be added to Section 8 of the Sales Playbook?
- [ ] Is the qualification rate improving — are we qualifying better prospects?

---

### Marketing

- [ ] Is the brand consistent across all channels?
- [ ] Is any paid advertising spend being measured against trial acquisition and conversion?
- [ ] Is SEO content being published at a consistent cadence?
- [ ] Is the Marketing Copy Library being used by the team — or being ignored for ad hoc copy?
- [ ] Has any campaign produced measurable results this quarter?

---

### Support

- [ ] What is the current median first response time?
- [ ] Are there any unresolved support tickets older than 5 business days?
- [ ] What is the most common support topic this quarter? Has it been addressed in the product or documentation?
- [ ] Is any merchant frustrated enough to represent a churn risk because of a support experience?

---

### Team

- [ ] Is the current team structure right for the next quarter?
- [ ] Is there a hire that should be initiated but hasn't been yet?
- [ ] Is every team member (if applicable) clear on their responsibilities and priorities?
- [ ] Has feedback been given to every person on the team this quarter?
- [ ] Is the culture what it should be — or has something crept in that doesn't belong?

---

### Operations

- [ ] Has a backup restore been tested this quarter?
- [ ] Is the Operations Runbook current?
- [ ] Are there any vendor contracts or SaaS subscriptions that need renewal or cancellation review?
- [ ] Has the security posture been reviewed in the last 6 months?
- [ ] Are all legal pages (Privacy Policy, Terms of Service) current for all active markets?

---

### Security

- [ ] Has any security issue been reported this quarter — by a user, a security researcher, or automated scanning?
- [ ] Are all secrets stored in environment variables — none in code, none in logs?
- [ ] Is rate limiting in place on all authentication endpoints?
- [ ] Is the Production Security Review checklist still fully satisfied?

---

### Finance

- [ ] What is current MRR?
- [ ] What is the current cash balance?
- [ ] Are there any unexpected costs this quarter?
- [ ] Is the pricing strategy still appropriate for the market?
- [ ] Is any partner relationship generating measurable commercial value?

---

### Documentation

- [ ] Is this Business Operating Plan still accurate? Any sections that need updating?
- [ ] Are there decisions that have been implemented without a corresponding `docs/08-Product-Decisions.md` entry?
- [ ] Is the Merchant Experience Blueprint still an accurate description of the merchant journey?
- [ ] Has the Sales Playbook been updated based on this quarter's learnings?

---

### Personal Development

- [ ] Have you read anything relevant to SaaS operations, product management, or merchant markets this quarter?
- [ ] Have you spoken with another founder or SaaS operator who can give you an outside perspective?
- [ ] Is the time you are spending on the business reflecting the priorities in Section 6?
- [ ] Are you doing work that only you can do — or are you doing work that should be delegated?
- [ ] What would you do differently if you were starting today? Is there anything to act on from that answer?

---

### Strategic Focus

- [ ] What is the single most important thing for the business this quarter?
- [ ] Is that the thing you are spending most of your time on?
- [ ] Are there commitments on the calendar that should not be there?
- [ ] What should you stop doing that you are currently doing?
- [ ] What decision have you been deferring that needs to be made?

---

*This document is the founder's operating guide. It describes where OneMember is going, how decisions are made, and what the business should never compromise. Review it every quarter. Update it when the business changes. Trust it when the business is moving fast and the clarity it was written with is needed most.*

*Last updated: 2026-06-29. Version 1.0.*
