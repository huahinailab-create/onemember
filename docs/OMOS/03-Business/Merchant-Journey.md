# Merchant Journey

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Merchant-Personas.md](./Merchant-Personas.md), [Customer-Journey.md](./Customer-Journey.md), [02-Product/Product-Bible.md](../02-Product/Product-Bible.md), [00-Executive/North-Star-Metric.md](../00-Executive/North-Star-Metric.md) |

---

## Purpose

The Merchant Journey documents the full lifecycle of a merchant from first awareness of OneMember to becoming a long-term, successful power user. Understanding this journey allows the product team to identify friction points, gaps, and opportunities at each stage.

Every feature should improve at least one stage of this journey.

---

## Stage 0 — Awareness

**What's happening:** The merchant has never heard of OneMember, or has heard of it but not investigated. They may be frustrated with their current loyalty solution (paper cards, LineOA, nothing) or may not yet recognise the problem.

**Triggers:**
- A competitor starts running a professional loyalty programme and their customers notice
- A friend who owns a similar business recommends OneMember
- They see an OneMember QR code at another merchant and scan it
- They search for "loyalty programme Thailand" or "แอปสะสมแต้ม"

**OneMember's role at this stage:** Be discoverable. Be credible. Make the problem clear and the solution obvious.

**Success:** Merchant visits the website or app and understands what OneMember does within 60 seconds.

---

## Stage 1 — Registration and Onboarding

**What's happening:** The merchant signs up for OneMember. They are evaluating whether the platform delivers on its promise.

**Journey steps:**
1. Register with email + password (or Google)
2. Verify email (queued, reliable, fast — BUG-001 fixed this)
3. Complete onboarding: business name, type, currency, timezone, logo
4. Create their first Campaign (Points or Stamps)
5. Customise their rewards
6. Get their merchant QR code
7. **Go live** — place the QR code at their counter

**Key friction points:**
- Email verification that doesn't arrive or is confusing → Fixed in BUG-001
- Onboarding with too many required fields → Must be simplified
- Creating a Campaign that feels complex → Simplification required

**Success metric:** Merchant completes onboarding and receives their first member within 24 hours of registration.

**Target time:** < 4 hours from registration to first member

---

## Stage 2 — First Member Acquisition

**What's happening:** The merchant has their QR code and has started asking customers to scan it. The first few members join.

**Journey steps:**
1. Merchant places QR code at counter
2. Staff explain the loyalty programme to customers
3. First customer scans QR → joins in < 30 seconds
4. Merchant sees their first member appear in the dashboard
5. Merchant feels the programme is working

**Key friction points:**
- Customer cannot complete join flow on mobile → Must be optimised
- Merchant cannot find where to see new members → Dashboard clarity required
- Staff do not know how to explain the programme → Merchant training materials needed

**Success metric:** Merchant has at least 10 members within 7 days of going live.

---

## Stage 3 — Early Adoption (Months 1–3)

**What's happening:** The merchant has an active loyalty programme with growing membership. They are learning how to use the platform and discovering what works.

**Journey steps:**
1. Merchants check the dashboard regularly to see member growth
2. First points transactions and redemptions occur
3. Merchant explores campaign settings to adjust rules
4. Merchant Intelligence provides first health score and insights
5. Merchant begins to see repeat visits from members

**Key friction points:**
- Dashboard does not clearly show the most important metrics → Dashboard design priority
- Merchant cannot easily identify their best customers → Member analytics priority
- First redemption is confusing (what counts as a valid redemption?) → Redemption UX priority
- Merchant does not know what action to take next → Merchant Intelligence guidance critical

**Success metric:** At least 30% of members make a second transaction within 90 days.

---

## Stage 4 — Activation (Month 3–6)

**What's happening:** The merchant is using OneMember consistently. They are seeing measurable results. They are starting to use advanced features.

**Journey steps:**
1. Merchant runs their first birthday campaign
2. Merchant uses a win-back campaign for members who haven't visited in 60 days
3. Merchant compares months: "We have 20% more repeat visits than 3 months ago"
4. Merchant tells other business owners about OneMember
5. Merchant is confident enough to promote their loyalty programme on social media

**Key friction points:**
- Birthday campaign setup is complex → Simplification priority
- Win-back campaign requires manual action → Automation required
- ROI of loyalty programme is not clearly visible → Analytics priority

**Success metric:** Merchant is using at least 3 features (campaign, member management, analytics) monthly.

---

## Stage 5 — Retention and Growth (Month 6+)

**What's happening:** The merchant is a loyal, paying customer of OneMember. They are generating significant loyalty activity. They are open to expanding to more features.

**Journey steps:**
1. Merchant explores upgrading their subscription for more features
2. Merchant opens additional locations and adds them to OneMember
3. Merchant participates in OneMember features that increase their visibility (Customer Wallet, Phase 2)
4. Merchant becomes a reference customer or case study
5. Merchant integrates OneMember with their POS or e-commerce (Phase 3+)

**Success metric:** Merchant is retained at month 12. Net Promoter Score > 8.

---

## Stage 6 — Advocacy

**What's happening:** The merchant is a champion of OneMember. They recommend it actively.

**Journey steps:**
1. Merchant refers other business owners (referral programme, Phase 2)
2. Merchant participates in a case study or testimonial
3. Merchant joins a merchant community or OneMember event
4. Merchant provides product feedback that shapes future features

**Success metric:** Merchant refers at least 1 new merchant per year.

---

## Journey Gaps and Priorities

| Gap | Current State | Priority |
|---|---|---|
| Registration to first member < 4 hours | Not measured | **High** |
| Onboarding simplification | Too many fields | **High** |
| Dashboard clarity | Requires improvement | **High** |
| Birthday campaign UX | Complex setup | **Medium** |
| Win-back automation | Manual only | **Medium** |
| ROI visibility | Not explicitly shown | **Medium** |
| Multi-location management | Phase 3+ | **Low** |
