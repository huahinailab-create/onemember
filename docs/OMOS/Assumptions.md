# Assumptions

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Known-Constraints.md](./Known-Constraints.md), [03-Business/Market-Opportunity.md](./03-Business/Market-Opportunity.md), [09-Roadmap/Long-term-Roadmap.md](./09-Roadmap/Long-term-Roadmap.md) |

---

## Purpose

This document records the assumptions OneMember is currently operating under. Unlike Known Constraints (which are fixed), assumptions are beliefs about the world that are probably true right now but may change. When an assumption changes, it must trigger a product review and potentially a roadmap or architectural update.

Each assumption includes: what we believe, why we believe it, and what we will do if it turns out to be wrong.

---

## Market Assumptions

### A-001 — Thai SMEs Will Pay for Loyalty Software

**Assumption:** Small and medium businesses in Thailand will pay 500–3,000 THB/month for a professional loyalty platform if it demonstrably increases repeat customer visits.

**Evidence for this assumption:**
- Thai SMEs already pay for LINE Official Accounts (1,200–12,000 THB/month for premium)
- They pay for accounting software, POS software, and delivery platform commissions
- The ask (1,500 THB/month) is equivalent to approximately 15–30 cups of coffee in revenue

**Risk if wrong:** If merchants are unwilling to pay, the SaaS model fails. Fallback would be a freemium model with very limited free tier, or a commission-based model (we earn when they earn).

**Trigger to revisit:** Merchant trial-to-paid conversion rate below 15% after 6 months of active marketing.

---

### A-002 — QR Codes Are Sufficient for Consumer Onboarding

**Assumption:** Thai consumers will scan a QR code and complete a web-based join flow without requiring a native app download.

**Evidence for this assumption:**
- PromptPay (QR payment) is ubiquitous — QR scanning is a trained behaviour in Thailand
- COVID-era QR check-in apps normalised scanning for new experiences
- App download adds 60+ seconds of friction; QR web flow targets < 30 seconds

**Risk if wrong:** If consumers resist the web-based flow and demand a native app, the Phase 1 customer acquisition model fails and a native app would need to be accelerated to Phase 2.

**Trigger to revisit:** Customer join flow completion rate below 70% after 90 days of monitoring. Or if more than 3 merchants report that "customers don't want to scan."

---

### A-003 — PromptPay Will Be the Primary Commerce Payment

**Assumption:** When Commerce launches (Phase 3), PromptPay will be the preferred payment method for Thai consumers placing orders through OneMember.

**Evidence for this assumption:**
- PromptPay has over 70 million registered accounts in Thailand (as of 2023)
- Bank of Thailand actively promotes QR payment adoption
- Most Thai consumers have banking apps that support PromptPay payment

**Risk if wrong:** If credit card or digital wallet (TrueMoney, LINE Pay) adoption is higher than PromptPay for commerce transactions, the payment integration priority must be re-evaluated.

**Trigger to revisit:** Review payment method preferences when Commerce is in sprint planning (Phase 3). Do not assume PromptPay without validating at that time.

---

### A-004 — Enterprise Clients Will Pay for API Integration

**Assumption:** Enterprise merchants (20+ locations) will pay a meaningful fee for the Enterprise Bridge API that connects their existing systems to OneMember's customer wallet.

**Evidence for this assumption:**
- Enterprise loyalty software currently costs 50,000–200,000+ THB/month
- Any API that replaces part of that stack delivers clear ROI
- Enterprise clients buying SaaS tools are accustomed to API access fees

**Risk if wrong:** If enterprise clients expect the API to be included in their subscription (as a feature, not a separate product), the enterprise revenue stream model needs revision.

**Trigger to revisit:** First 3 enterprise discussions about Bridge pricing. Update this assumption based on actual negotiation feedback.

---

## Consumer Behaviour Assumptions

### A-005 — Consumers Will Actively Use a Loyalty Wallet

**Assumption:** When the Customer Wallet (Phase 2) launches, consumers who are already members of OneMember merchants will download or use the wallet actively — not just when prompted by a merchant.

**Evidence for this assumption:**
- Loyalty Enthusiast persona (see Customer-Personas.md) is an early adopter who actively manages points
- The promise of seeing all loyalty in one place addresses a real pain point (too many apps/cards)
- Reference: multi-brand loyalty apps in other markets (Shopkick, Stocard) have achieved meaningful adoption

**Risk if wrong:** If consumers treat the wallet as "yet another app they never open," the Phase 2 network effect never materialises. The merchant value of the wallet depends entirely on consumer adoption.

**Trigger to revisit:** Wallet monthly active rate below 20% of registered wallet users at 6 months post-launch.

---

### A-006 — Consumers Value Privacy Transparency

**Assumption:** A significant portion of Thai consumers will choose OneMember-powered merchants over competitors partly because of OneMember's privacy transparency features (consent controls, data visibility).

**Evidence for this assumption:**
- Thailand PDPA awareness is growing
- International surveys show younger consumers (18–35) are increasingly privacy-aware
- Privacy-forward positioning is a differentiator in a market where "your data is free" is the norm

**Risk if wrong:** If privacy controls are seen as a burden rather than a benefit (too many consent screens, too much friction), adoption suffers. Privacy must be implemented gracefully — not as a compliance lecture.

**Trigger to revisit:** User testing of the consent flow before Wallet launch. If > 30% of test users abandon the wallet during the consent screen, the UX needs redesign.

---

## Technical Assumptions

### A-007 — Laravel Handles Scale for Phase 1 and Phase 2

**Assumption:** Laravel on a mid-range DigitalOcean Droplet (or equivalent) can handle the transaction volume for Phase 1 and Phase 2 without significant architectural changes.

**Evidence for this assumption:**
- At 5,000 merchants with 500 active members each, peak concurrent users are likely < 500
- Laravel handles thousands of requests per second on adequate hardware
- Database bottlenecks are the typical limiting factor, addressable with proper indexing and caching

**Risk if wrong:** If a viral growth event or Enterprise client integration creates unexpected volume, the current infrastructure may need rapid scaling. Redis caching and horizontal scaling become necessary.

**Trigger to revisit:** If response times exceed 500ms p95 under normal load, an infrastructure review sprint is required.

---

### A-008 — Single Database Scales to Phase 2

**Assumption:** The single-database multi-tenant architecture will scale adequately through Phase 2 (Customer Wallet with potentially millions of member records).

**Evidence for this assumption:**
- Proper indexing on `merchant_id` and frequently queried columns makes single-database multi-tenancy scalable to millions of rows
- PostgreSQL (or MySQL) handles this pattern at this scale routinely

**Risk if wrong:** If Phase 2 wallet adoption creates a volume of member records and transaction logs that degrades performance, database partitioning or a read replica becomes necessary.

**Trigger to revisit:** At 1 million total member records or if query response times begin degrading.

---

## Regulatory Assumptions

### A-009 — Thailand PDPA Compliance Is Achievable with Current Architecture

**Assumption:** OneMember's consent-based data collection and merchant data sovereignty model satisfies Thailand PDPA requirements without requiring a dedicated compliance sprint beyond what is already implemented.

**Evidence for this assumption:**
- OneMember collects only necessary data (name, phone, date of birth — all opt-in)
- Merchants have full visibility into member data
- Members can (in Phase 2) revoke consent and request deletion

**Risk if wrong:** PDPA enforcement or interpretation may require additional data handling procedures (formal consent records, mandatory DPO, data processing agreements with merchants). This would require a dedicated legal/compliance sprint.

**Trigger to revisit:** First formal PDPA enforcement action in Thailand (expected to increase in 2026). Engage a Thai data protection lawyer before launching the Customer Wallet.

---

## Assumptions Review Schedule

This document is reviewed:
- Quarterly by the Product Owner and CTO
- When a major product milestone is reached (Phase 1 → 2 transition)
- When an assumption is contradicted by market or user feedback

When an assumption is updated, add a note with the date and what changed. Do not delete old assumptions — the history of what we believed and when we changed our minds is valuable.
