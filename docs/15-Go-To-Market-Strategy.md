# 15 — Go-To-Market Strategy

> **Last updated:** 2026-06-28  
> **Owner:** Product Owner (Huahin)  
> **Status:** Living document — assumptions are clearly marked  
> **Cross-reference:** [docs/13-Product-Vision.md](13-Product-Vision.md) · [docs/11-Pricing-Strategy.md](11-Pricing-Strategy.md) · [docs/10-Version-1.0-Roadmap.md](10-Version-1.0-Roadmap.md)

---

> **Notation used in this document:**
> - **[CONFIRMED]** — a documented decision already made
> - **[ASSUMPTION]** — a working assumption not yet formally decided
> - **[TBD]** — to be determined before commercial launch

---

## 1. Executive Summary

OneMember is an affordable, self-service loyalty SaaS platform for independent merchants and small businesses. It enables non-technical business owners to launch a professional loyalty programme in minutes — without developers, without enterprise contracts, and without replacing their existing systems.

**Who it serves:** Independent merchants across the food and beverage, retail, health and wellness, and services sectors — particularly in Southeast Asia and the Middle East, where loyalty programmes are largely paper-based or absent.

**Why it exists:** Independent merchants are underserved by loyalty technology. Enterprise platforms are too expensive and complex. Paper stamp cards are easily lost and generate no data. OneMember fills the gap with a product that is simple enough for a café owner to operate between serving customers, yet powerful enough to drive measurable repeat business.

**Primary business objective for Version 1.0:** Achieve commercial launch with a working subscription model. Acquire the first cohort of paying merchants. Validate product-market fit through real merchant usage and retention data before expanding.

---

## 2. Ideal Customer Profile (ICP)

The following industries represent the highest-priority merchant segments for OneMember. Each segment has demonstrated loyalty programme adoption in comparable markets, and has a clear need for affordable, easy-to-use software.

### Hair Salons

| Attribute | Detail |
|-----------|--------|
| Typical size | 1–3 stylists, single location |
| Customer behaviour | Regular repeat visits (every 4–6 weeks), strong stylist-client relationships |
| Why loyalty matters | Clients have many choices; a reward for the 10th visit creates a tangible reason to return |
| Loyalty use cases | Stamps (every 10th haircut free or discounted), birthday reward (free treatment or discount on birthday month), points for retail product purchases |

### Nail Salons

| Attribute | Detail |
|-----------|--------|
| Typical size | 2–5 technicians, single location |
| Customer behaviour | High-frequency visits (every 2–4 weeks), price-sensitive, influenced by social proof |
| Why loyalty matters | Switching cost is low; loyalty programmes create stickiness without competing solely on price |
| Loyalty use cases | Stamps (every 8th visit free nail art upgrade), points per spend (redeem for nail services), birthday reward |

### Massage & Spa

| Attribute | Detail |
|-----------|--------|
| Typical size | 3–15 therapists, 1–2 locations |
| Customer behaviour | Occasional to regular visits (monthly or bi-weekly), wellness-oriented, value relationship with therapist |
| Why loyalty matters | High-value transactions make points-based programmes compelling; members can accumulate quickly |
| Loyalty use cases | Points per 100 THB/SGD/MYR spent (redeem for free sessions or upgrades), member-only pricing, birthday bonus session |

### Restaurants & Cafés

| Attribute | Detail |
|-----------|--------|
| Typical size | 2–20 staff, 1–3 locations |
| Customer behaviour | High visit frequency for regular customers (multiple times per week for cafés), transaction-driven |
| Why loyalty matters | Breakfast and lunch regulars form the core revenue base; a loyalty programme reinforces daily habit |
| Loyalty use cases | Stamps (every 10th coffee free), points per spend (redeem for free meals or drinks), birthday free item |

### Hotels

| Attribute | Detail |
|-----------|--------|
| Typical size | Boutique hotels, 10–80 rooms |
| Customer behaviour | Lower visit frequency but high transaction value; repeat stays and F&B spend |
| Why loyalty matters | Direct booking loyalty reduces dependence on OTA commissions; rewards encourage direct repeat stays |
| Loyalty use cases | Points per stay or per night, points for in-hotel F&B and spa spend, birthday upgrade, points redeemable for room upgrades or free nights |

### Fashion Retail

| Attribute | Detail |
|-----------|--------|
| Typical size | Independent boutiques, 1–5 staff |
| Customer behaviour | Seasonal purchase patterns; strong preference for stores that make them feel recognised |
| Why loyalty matters | Fashion customers who feel valued spend more per visit and return for new collections |
| Loyalty use cases | Points per spend (redeem for discounts), VIP tier for high spenders, birthday discount, seasonal double-points events |

### Beauty & Cosmetics

| Attribute | Detail |
|-----------|--------|
| Typical size | Beauty counters, standalone cosmetic shops, 1–8 staff |
| Customer behaviour | Mix of habitual repurchase (skincare) and impulse (makeup); strong brand loyalty once established |
| Why loyalty matters | Repurchase cycles are predictable; loyalty programmes accelerate the next purchase decision |
| Loyalty use cases | Points per product purchased, milestone rewards (50th product purchased), birthday gift with purchase |

### Grocery Stores

| Attribute | Detail |
|-----------|--------|
| Typical size | Independent neighbourhood grocers, 3–15 staff |
| Customer behaviour | High-frequency, low-basket visits; price-sensitive; convenience-driven |
| Why loyalty matters | Grocery switching is common; a points programme that accumulates quickly creates a reason to prefer one store |
| Loyalty use cases | Points per 50 THB/SGD spent (redeem for vouchers or free products), monthly bonus for fresh produce purchases |

### Pet Shops

| Attribute | Detail |
|-----------|--------|
| Typical size | Independent pet stores and grooming parlours, 2–8 staff |
| Customer behaviour | Loyal to a trusted source; purchases driven by pet care needs on regular cycles |
| Why loyalty matters | Pet owners who trust a shop are very loyal — a reward programme reinforces that trust |
| Loyalty use cases | Stamps (every 6th grooming session free), points for pet food purchases (redeem for accessories or grooming), birthday reward for the pet's birthday |

### Wholesale Businesses

| Attribute | Detail |
|-----------|--------|
| Typical size | Small-to-mid wholesale operators, regular B2B buyers |
| Customer behaviour | Bulk purchase cycles, relationship-driven, sensitive to payment terms |
| Why loyalty matters | Wholesale buyers can easily switch suppliers; volume-based rewards increase order size and frequency |
| Loyalty use cases | Points per order value (redeem for product discounts or delivery credits), milestone rewards for annual volume targets |

---

## 3. Target Markets

OneMember's initial launch markets are selected based on SME density, digital payment adoption, and readiness for SaaS-based business tools.

### Launch Priority

| Priority | Market | Rationale |
|----------|--------|-----------|
| 1 | **Thailand** | Initial development market; confirmed first market [CONFIRMED — docs/11-Pricing-Strategy.md]; large independent merchant base; growing digital adoption among SMEs; personal network and market knowledge |
| 2 | **Singapore** | High digital adoption; merchants accustomed to SaaS pricing; English-first market; regulatory clarity; strong SME support ecosystem |
| 3 | **Malaysia** | Large Malay and Chinese SME community; cultural similarity to Thailand; Ringgit-priced plans viable; bilingual English/Malay market |
| 4 | **UAE** | High-income market; growing SME ecosystem in Dubai and Abu Dhabi; English-friendly; appetite for modern SaaS tools among retail and F&B |

### Localisation Considerations

| Consideration | Thailand | Singapore | Malaysia | UAE |
|--------------|---------|----------|---------|-----|
| **Currency** | THB (Thai Baht) | SGD (Singapore Dollar) | MYR (Malaysian Ringgit) | AED (UAE Dirham) |
| **Primary language** | Thai | English | English / Malay | English / Arabic |
| **Timezone** | Asia/Bangkok (UTC+7) | Asia/Singapore (UTC+8) | Asia/Kuala_Lumpur (UTC+8) | Asia/Dubai (UTC+4) |
| **Payment gateway** | [TBD] — PromptPay, local cards, KBank | Stripe, PayNow, PayLah | Stripe, FPX, Maybank | Stripe, local gateways |
| **Tax considerations** | VAT 7% (Thailand) | GST 9% (Singapore) | SST 8% (Malaysia) | VAT 5% (UAE) |
| **UI language** | English V1.0; Thai V1.x [ASSUMPTION] | English | English | English |
| **Date format** | DD/MM/YYYY | DD/MM/YYYY | DD/MM/YYYY | DD/MM/YYYY |

> **Note:** Multi-language UI is not planned for Version 1.0. The product launches in English. Thai-language support is a Version 1.x consideration pending merchant feedback. [ASSUMPTION]

---

## 4. Competitive Positioning

OneMember does not compete with enterprise loyalty platforms. It competes with the status quo: paper stamp cards, spreadsheets, WhatsApp-based manual tracking, and no loyalty programme at all.

Where software competitors exist in these markets, they tend to be:
- Built primarily for large chains or franchise networks
- Expensive to set up and maintain
- Requiring technical staff or IT support
- Delivered through mobile apps that require the merchant's customers to download something

OneMember differentiates on the following dimensions:

| Dimension | OneMember Approach |
|-----------|--------------------|
| **Simplicity** | Any merchant can complete onboarding in under 10 minutes. No training required. No consultant needed. |
| **Affordability** | Priced for independent merchants, not corporate IT budgets. Free trial with automatic Free-tier fallback removes financial risk from adoption. |
| **Fast onboarding** | 6-step wizard with starter campaign auto-creation means a merchant can go from sign-up to a running loyalty programme in the same session. |
| **Easy staff training** | The interface uses plain merchant language — Members, Campaigns, Rewards, Activity. A new staff member can record a purchase or redeem a reward in minutes. |
| **Customer retention focus** | Every feature is designed to bring customers back. OneMember does not try to be a POS, a CRM, or an accounting tool. It does one thing well. |
| **Clean modern interface** | Bootstrap 5 design, no clutter, no enterprise UX. Feels like a modern SaaS tool, not a legacy retail management system. |
| **No app required for customers** | Members are managed by the merchant. Customers do not need to download an app to participate in a loyalty programme. [ASSUMPTION — customer-facing app is Version 2.0] |

---

## 5. Pricing Strategy

OneMember uses a freemium SaaS model with a 30-day Professional trial on sign-up. All pricing details are confirmed in [docs/11-Pricing-Strategy.md](11-Pricing-Strategy.md).

### Plans

| Plan | Target Audience | Status |
|------|----------------|--------|
| **Free** | Sole traders and micro-businesses testing the product or running a minimal programme | [CONFIRMED] |
| **Starter** | Small businesses with a growing member base who need core loyalty features | [CONFIRMED] |
| **Professional** | Established businesses that need the full feature set, automation, and reporting | [CONFIRMED] |
| **Enterprise** | Businesses with multiple locations, large member bases, or custom integration needs | [CONFIRMED] |

### Professional Trial

- Every new merchant receives a **free 30-day Professional trial** on registration [CONFIRMED]
- No credit card required to start the trial [CONFIRMED]
- Full Professional-tier feature access during the trial period [CONFIRMED]
- At trial end, merchant is automatically moved to the Free plan if no paid plan is selected [CONFIRMED]
- Customer data is **never deleted** regardless of plan downgrade [CONFIRMED]

### Pricing Amounts

Specific prices and plan limits are **not yet set**. They will be determined by the Product Owner after beta testing and real merchant feedback, before commercial launch. [TBD — see docs/11-Pricing-Strategy.md]

### Why This Model Supports Merchant Acquisition

1. **Zero-friction entry:** No credit card on sign-up removes the primary barrier to trial adoption for cost-sensitive SME owners.
2. **Full-feature trial:** Merchants experience the Professional plan at its best before being asked to pay — increasing the perceived value of upgrading.
3. **Soft downgrade:** Automatic move to Free (not cancellation) keeps merchants in the product after trial expiry. They continue using OneMember on a limited basis, which extends the conversion window.
4. **Data retention guarantee:** Merchants never fear losing their member history by downgrading — reducing anxiety about commitment.

---

## 6. Customer Acquisition Strategy

### Organic Channels

| Channel | Approach |
|---------|---------|
| **SEO** | Long-tail content targeting merchant search queries: "loyalty programme for café", "stamp card app Thailand", "how to retain restaurant customers". Blog content in English; Thai-language content in Version 1.x. [ASSUMPTION] |
| **Content marketing** | Educational articles and guides: "How to start a loyalty programme for your small business", "Points vs. stamps: which loyalty model is right for you?". Positions OneMember as a knowledgeable, trustworthy partner. |
| **Google Business** | OneMember listed as a local business in target markets. Reviews from early merchants. |
| **Social media** | Facebook and Instagram focus for Thailand and Malaysia. LinkedIn for Singapore and UAE. TikTok for younger merchant audiences in Thailand. Content: success stories, tips, before-and-after loyalty programme results. |
| **Referral programme** | Merchants who refer other merchants receive an account credit or extended trial. Word-of-mouth is the most trusted acquisition channel in the SME segment. [ASSUMPTION — programme design TBD] |

### Paid Channels

| Channel | Use Case |
|---------|---------|
| **Facebook & Instagram Ads** | Targeted campaigns to small business owners in Thailand, Singapore, Malaysia. Lookalike audiences based on early merchant sign-ups. |
| **Google Ads** | Search ads for high-intent queries: "loyalty app for small business", "stamp card software", "customer rewards programme". |
| **TikTok Ads** | Brand awareness for younger merchants in Thailand. Short-form content showing how quick OneMember is to set up. |

### Strategic Channels

| Channel | Approach |
|---------|---------|
| **Business associations** | Partnering with chamber of commerce organisations, SME associations, and F&B trade groups in target markets. Offer member discounts or extended trials. |
| **POS vendors** | Referral partnerships with popular POS providers used by independent merchants. OneMember complements a POS system — it does not replace it. |
| **Marketing agencies** | Agencies serving SME clients can recommend OneMember as part of a retention marketing package. Reseller/affiliate programme. [TBD] |
| **Franchise consultants** | Franchise networks often want a loyalty programme for their franchisees. OneMember could be recommended as the standard loyalty platform. |
| **Accountants & bookkeepers** | Trusted advisors to SMEs. Referral incentives for accountants who recommend OneMember to their clients. |
| **Local business communities** | Facebook groups, Line groups (Thailand), WhatsApp communities of local business owners. Organic presence through helpful engagement, not advertising. |

---

## 7. Marketing Messaging

### Primary Tagline

> **Reward loyalty. Grow your business.**

### Core Promise

> Keep customers coming back.

### Primary Value Proposition

> OneMember is the easiest and most affordable customer retention platform for small and medium-sized businesses.

### Marketing Messages

The following messages are prioritised for marketing copy, ads, and website content. Each leads with a business outcome, not a software feature.

| Message | Context |
|---------|---------|
| "Turn first-time visitors into regulars." | Acquisition-focused; speaks to the most common merchant pain |
| "Keep customers coming back — without expensive advertising." | Retention-vs-acquisition cost framing |
| "Replace your paper stamp cards with something your customers will actually use." | Direct comparison to the status quo |
| "Your best customers deserve to be rewarded." | Emotional appeal; merchant pride in recognising regulars |
| "Set up your loyalty programme today. It takes 10 minutes." | Speed and simplicity; removes "it's complicated" objection |
| "Know who your best customers are — and keep them." | Data-awareness message for data-minded owners |
| "No app download needed. No developer required. No contract." | Objection removal — three common barriers addressed at once |
| "Stop losing customers to the business next door." | Competitive urgency without naming competitors |
| "More repeat visits. Less marketing spend." | Financial outcome; appeals to cost-conscious owner-operators |

---

## 8. Merchant Journey

The desired end-to-end experience for a new merchant:

```
Visitor
  └─ Discovers OneMember through search, social, referral, or partner
       ↓
  Website
  └─ Clear value proposition, pricing, and "Start Free" CTA
       ↓
  Free Sign-up
  └─ Email + password registration. No credit card required.
       ↓
  30-Day Professional Trial begins automatically
       ↓
  Business Onboarding (Wizard)
  └─ 6 steps: Business Info → Settings → Loyalty Type → Quick Start → Done
  └─ Starter campaign created automatically
       ↓
  First Campaign active
       ↓
  First Member added
  └─ Merchant adds their first real customer at the counter
       ↓
  First Purchase recorded
  └─ Points or stamps issued. Merchant sees it work in real time.
       ↓
  First Reward Redemption
  └─ A member earns enough to redeem. Merchant fulfils the reward.
  └─ Value of loyalty is now tangible.
       ↓
  Subscription Decision (Day 28–30)
  └─ Merchant receives trial expiry reminder
  └─ Chooses Free, Starter, Professional, or Enterprise
  └─ If no action: automatically moved to Free plan
       ↓
  Long-term Merchant
  └─ Monthly active use, growing member base, increasing retention
  └─ Upsell opportunity: higher plan, additional features
```

**Key moment:** The "First Reward Redemption" is the most important activation event. When a merchant sees a real customer redeem a real reward, the loyalty cycle becomes tangible and the product's value is proven. All onboarding and early activation efforts should be designed to reach this moment as quickly as possible.

---

## 9. Launch Strategy

### Phase 1 — Private Alpha

**Objective:** Validate the core product with known, trusted users. Identify critical bugs and UX friction before exposing the product to the public.

| Item | Detail |
|------|--------|
| Access | Invite-only — founder network and known merchants |
| Target participants | 3–10 merchants across 2–3 industry types |
| Duration | 4–6 weeks [ASSUMPTION] |
| Focus | Core loyalty cycle: onboarding → member → purchase → redemption |
| Success criteria | All participants complete onboarding; at least one reward redemption per merchant; no critical bugs |
| Exit criteria | No P0/P1 bugs open; Launch Checklist Section 1 complete; merchant feedback collected and reviewed |

### Phase 2 — Closed Beta

**Objective:** Validate with a broader set of real merchants. Test billing, notifications, and edge cases. Begin gathering testimonials.

| Item | Detail |
|------|--------|
| Access | Application or referral — target 20–50 merchants |
| Duration | 6–8 weeks [ASSUMPTION] |
| Focus | Full product including subscription trial, email notifications, and settings |
| Billing | Stripe integration live in test mode; merchants on free Professional trial |
| Success criteria | > 70% of beta merchants complete onboarding; > 50% add at least 10 members; no P0/P1 billing bugs |
| Exit criteria | Launch Checklist fully signed off; billing live in production mode; legal pages published; infrastructure reviewed |

### Phase 3 — Public Launch

**Objective:** Open sign-up to all merchants. Begin paid customer acquisition. Establish the conversion funnel baseline.

| Item | Detail |
|------|--------|
| Access | Public sign-up at onemember.com [ASSUMPTION — domain TBD] |
| Marketing | Organic SEO content live; paid campaigns on Facebook/Instagram/Google active |
| Billing | Stripe live; 30-day trial auto-starts on registration |
| Support | Email support channel active; response SLA defined |
| Success criteria | First 10 paying merchants; MRR > $0; Trial → Paid conversion rate tracked |
| Exit criteria | N/A — continuous operation begins |

### Phase 4 — Growth

**Objective:** Scale merchant acquisition and optimise the conversion funnel. Expand to additional markets.

| Item | Detail |
|------|--------|
| Focus | Increasing trial sign-ups; improving trial → paid conversion; reducing churn |
| Markets | Singapore, Malaysia, UAE expansion [ASSUMPTION — timing TBD] |
| Product | Version 1.x features based on merchant feedback and usage data |
| Partnerships | POS vendor partnerships; business association referrals active |
| Success criteria | [TBD — MRR targets to be set by Product Owner before launch] |

---

## 10. Key Performance Indicators

KPIs are grouped by funnel stage. Targets are [TBD] until beta data establishes baselines.

### Acquisition

| KPI | Measurement | Target |
|-----|------------|--------|
| Website visitors | Monthly unique visitors | [TBD] |
| Trial sign-ups | New registrations per month | [TBD] |
| Trial sign-up conversion rate | Sign-ups / visitors | [TBD] |
| Cost per trial sign-up | Total ad spend / sign-ups | [TBD] |

### Activation

| KPI | Measurement | Target |
|-----|------------|--------|
| Onboarding completion rate | % who complete wizard | > 70% [ASSUMPTION] |
| Time to first member | Hours from sign-up to first member added | < 48 hours [ASSUMPTION] |
| Time to first purchase | Hours from first member to first purchase recorded | [TBD] |
| Time to first redemption | Days from sign-up to first reward redeemed | [TBD] |
| Campaigns created | % of merchants who create at least 1 campaign | [TBD] |

### Retention

| KPI | Measurement | Target |
|-----|------------|--------|
| Trial → Paid conversion rate | % of trial merchants who subscribe | > 20% [ASSUMPTION] |
| Daily active merchants | Merchants who log in daily | [TBD] |
| Weekly active merchants | Merchants active at least once per week | [TBD] |
| Monthly merchant churn | % of paying merchants who cancel | < 5% [ASSUMPTION] |
| Average merchant lifetime | Months from first payment to cancellation | [TBD] |

### Revenue

| KPI | Measurement | Target |
|-----|------------|--------|
| Monthly Recurring Revenue (MRR) | Sum of all active subscriptions | [TBD] |
| Average Revenue Per Merchant (ARPM) | MRR / paying merchants | [TBD] |
| Plan distribution | % on Free / Starter / Professional / Enterprise | [TBD] |

### Loyalty Programme Health

| KPI | Measurement | Target |
|-----|------------|--------|
| Members added (across platform) | Total members added per month | [TBD] |
| Purchases recorded per merchant | Average transactions per merchant per month | [TBD] |
| Rewards redeemed per merchant | Average redemptions per merchant per month | [TBD] |
| Member retention rate | % of members who transact again within 60 days | [TBD] |

---

## 11. Risks and Mitigations

| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|-----------|
| **Merchant adoption is slower than expected** | Medium | High | Extend beta period. Focus on 1–2 high-density industries before broad launch. Improve onboarding based on alpha/beta feedback. |
| **Pricing is too high for the Thai market** | Medium | High | Pricing is not set until after beta. Beta feedback will directly inform final price points. Start with a generous trial to reduce price sensitivity. |
| **Pricing is too low — unsustainable** | Low | High | Pricing is not set until after beta. Build cost model before setting prices. |
| **Competition from a well-funded entrant** | Low | Medium | Compete on simplicity and affordability, not features. Don't try to match enterprise feature sets. Focus on the underserved independent merchant segment. |
| **Support workload exceeds capacity** | Medium | Medium | Build comprehensive onboarding wizard and help text to reduce support tickets. Launch with email support only. Establish clear SLA expectations. |
| **Feature creep delays launch** | High | High | Apply the Decision Framework (docs/13-Product-Vision.md) to every feature request. If it doesn't pass the 5-question test, it goes to Version 1.1 or 2.0. |
| **Infrastructure instability under load** | Low | High | Conduct load testing before public launch. Use managed hosting with auto-scaling. Monitor with uptime tools. Maintain daily database backups. |
| **Payment gateway unavailability or delays** | Medium | High | Evaluate multiple payment gateway options for each market. Do not go live in a market without a working, tested payment gateway. |
| **Data privacy regulation non-compliance** | Low | Very High | Legal pages (Privacy Policy, Terms) required before launch. Review PDPA (Thailand), PDPC (Singapore), PDPA (Malaysia), and local UAE data regulations before each market launch. |
| **Merchant data breach** | Very Low | Very High | OWASP Top 10 review before public launch (Launch Checklist Section 2). No payment card data stored in OneMember. Annual security review post-launch. |

---

## 12. 90-Day Launch Plan

The following is a high-level plan focused on commercial outcomes. Engineering sprint planning is separate.

### Month 1 — Private Alpha & Product Completion

**Objective:** Close remaining V1.0 product gaps and validate the core experience with known merchants.

| Week | Focus | Outcome |
|------|-------|---------|
| Week 1–2 | Complete V1.0 product hardening (see docs/10-Version-1.0-Roadmap.md Milestone 3) | Member search, reward catalogue, birthday delivery, notifications |
| Week 3–4 | Private Alpha launch with 3–10 invited merchants | Feedback collected; critical bugs resolved; onboarding validated |

**Exit criteria for Month 1:** No P0/P1 bugs; all alpha merchants have completed onboarding and recorded at least one transaction.

---

### Month 2 — Closed Beta & Commercial Readiness

**Objective:** Expand to 20–50 merchants; complete billing integration; finalise pricing.

| Week | Focus | Outcome |
|------|-------|---------|
| Week 5–6 | Stripe billing integration; plan limits enforcement; billing portal | Merchants can subscribe and pay |
| Week 7 | Legal pages live; pricing page live; support channel established | Commercially ready |
| Week 8 | Closed beta opens; paid campaigns on standby; testimonials collected from alpha merchants | Beta pipeline active |

**Exit criteria for Month 2:** Stripe billing working end-to-end; at least 20 beta merchants active; Launch Checklist fully signed off.

---

### Month 3 — Public Launch & First Revenue

**Objective:** Open to the public; acquire first paying merchants; establish funnel baselines.

| Week | Focus | Outcome |
|------|-------|---------|
| Week 9–10 | Public launch; paid acquisition campaigns live; SEO content published | First public sign-ups |
| Week 11 | First trial-to-paid conversion window opens (beta merchants from Week 8) | First MRR |
| Week 12 | Funnel review; conversion rate analysis; Month 4 plan | Data-driven decisions for growth phase |

**Exit criteria for Month 3:** At least 10 paying merchants; MRR > $0; conversion rate baseline established; churn rate tracked.

---

## 13. Long-Term Growth

Version 1.0 focuses entirely on the core loyalty cycle: points, stamps, rewards, and member management.

After achieving product-market fit and sustainable MRR, OneMember may expand into the following areas — each evaluated against the Decision Framework in [docs/13-Product-Vision.md](13-Product-Vision.md) before any sprint is committed:

| Expansion Area | Description | Priority [ASSUMPTION] |
|---------------|-------------|----------------------|
| **Mobile apps** | Customer-facing iOS and Android apps for balance checking and digital loyalty cards | High — most-requested post-V1 feature |
| **Marketing automation** | Automated re-engagement emails, milestone messages, birthday campaigns | High — directly supports merchant retention goals |
| **Gift cards** | Digital gift cards redeemable in-store; sold through OneMember | Medium |
| **Digital memberships** | Monthly or annual member club subscriptions (e.g., coffee club, VIP member) | Medium |
| **Referral programmes** | Member-get-member with bonus points for successful referrals | Medium |
| **AI recommendations** | Campaign suggestions, optimal reward thresholds, churn risk alerts | Medium — requires usage data first |
| **Multi-location support** | Single merchant account managing multiple outlet locations | Medium — required for small chain expansion |
| **Public API** | REST API for POS integration and third-party automation | Low for V1; High for Enterprise tier |
| **White-label platform** | Reseller-branded OneMember for POS vendors and marketing agencies | Low — post-revenue opportunity |

> All expansion decisions require a documented entry in `docs/08-Product-Decisions.md` before implementation begins. No feature may be built without Product Owner approval.
