# 14 — Version 2.0 Ideas

> **Last updated:** 2026-06-28  
> **Status:** Ideas only — NOT committed to any sprint  
> **Owner:** Product Owner (Huahin) + CTO (Solution Architect)  
> **Process:** Ideas here require full review and DECISION entries in docs/08-Product-Decisions.md before any implementation begins.

---

## Purpose

This document captures post-V1.0 product ideas for future planning. None of these features should be built until V1.0 is commercially launched and generating revenue. Ideas are organised by theme, not priority.

---

## 1. Customer-Facing Mobile App

The most impactful V2.0 investment. Today, loyalty is entirely merchant-managed — customers have no self-service view of their balance.

### Ideas
- iOS and Android app (or Progressive Web App) for members
- Members check their own point balance and stamp progress
- Members redeem rewards from their phone (QR code scan)
- Push notifications: "You're 2 stamps away from your reward!"
- Birthday reward notification

### Considerations
- Requires merchant branding / white-label per merchant
- Deep link from loyalty programme card to merchant's campaign
- Member authentication (email OTP or social login — no password required)

---

## 2. QR Code & Digital Member Card

### Ideas
- Member QR code displayed in the app (or printable card)
- Merchant scans QR to look up member at POS instantly
- QR codes on physical loyalty cards (print-at-home PDF)
- QR code on receipt or sticker for new member self-enrolment

### Considerations
- Currently members are looked up by name/email (manual). QR speeds up high-volume POS use.
- Requires camera access on merchant device or a QR scanner peripheral

---

## 3. Marketing Automation

### Ideas
- Targeted email campaigns ("Members who haven't visited in 30 days")
- Automated re-engagement emails ("We miss you — here's a bonus reward")
- Milestone emails ("You've reached 500 points!")
- SMS notifications (opt-in, per-country compliance)
- Birthday email (separate from birthday reward trigger)

### Considerations
- Requires opt-in consent management (GDPR, PDPA)
- Requires email/SMS sending infrastructure
- Could integrate with Mailchimp, ActiveCampaign, or custom queue

---

## 4. Multi-Location Support

### Ideas
- One merchant account manages multiple outlet locations
- Members accumulate points/stamps across all locations
- Per-location reporting ("Which branch has the most redemptions?")
- Staff log-in tied to a specific location

### Considerations
- Significant schema change — `locations` table required, FK added to transactions
- Pricing model: per-location pricing tier?
- DECISION required before implementation

---

## 5. Staff Accounts & Roles

Basic staff accounts are planned for V1.0, but V2.0 would expand:

### Ideas
- Role-based access: Owner, Manager, Staff
- Staff: record purchases, redeem rewards only
- Manager: add members, view reports
- Owner: full access including billing and settings
- Activity log shows which staff member recorded each transaction

---

## 6. Advanced Analytics & Reports

### Ideas
- Revenue attributed to loyalty (estimate based on repeat visits)
- Member cohort analysis ("Members who joined in January — retention curve")
- Campaign performance comparison ("Stamps vs Points — which retains better?")
- Top rewards by redemption count
- Churn prediction ("Members at risk of leaving")
- Export reports to CSV and PDF
- Scheduled email reports (weekly/monthly digest)

---

## 7. Referral & Social Loyalty

### Ideas
- Refer-a-friend: member gets bonus points when their referral joins
- Social sharing: member shares a review/post and earns a reward
- Tiered loyalty (Bronze / Silver / Gold) based on lifetime spend or visit count

---

## 8. Integrations

### Ideas
- **POS integrations:** Square, Lightspeed, Shopify POS — auto-record purchases without manual entry
- **Accounting:** Xero, QuickBooks — sync loyalty data as a revenue line
- **WhatsApp Business API:** send redemption confirmations and birthday rewards via WhatsApp
- **Zapier / Make (Integromat):** webhook triggers for third-party automation
- **E-commerce:** WooCommerce, Shopify online store — award points for online purchases

---

## 9. White-Label / Reseller Programme

### Ideas
- Agencies and POS resellers can rebrand OneMember for their merchant clients
- Custom domain per reseller (e.g., loyalty.agencyname.com)
- Reseller dashboard shows all their merchant accounts
- Reseller earns commission on each merchant subscription

---

## 10. Marketplace / Shared Loyalty Network

Long-term vision — not near-term:

### Ideas
- Members earn points at multiple participating merchants
- "Shop local" network in a city or region
- Members use a single card/app across the network
- Merchant pays to join the network; receives network marketing benefits

---

## 11. AI-Powered Features

### Ideas
- Smart campaign recommendations ("Your best customers are food & beverage regulars — a stamps campaign works better for them")
- Optimal reward threshold suggestions based on historical redemption rates
- Churn risk alerts ("Member X hasn't visited in 45 days — consider sending a re-engagement reward")
- Natural language member search ("Find members who spent over $500 last month")

---

## 12. Customer Self-Enrolment

### Ideas
- Merchant shares a sign-up link or QR code
- Customer fills out their own details (name, email, phone, birthday)
- Merchant approves or auto-approves new members
- Reduces merchant staff time for new member registration

---

## Prioritisation Framework

When V1.0 is launched and the team begins V2.0 planning, evaluate each idea against:

| Criterion | Weight | Questions |
|-----------|--------|----------|
| Merchant value | High | Does this solve a real pain merchants report? |
| Member value | Medium | Does this improve the member experience? |
| Revenue impact | High | Does this increase conversion, retention, or ARPU? |
| Build complexity | Medium | How long would this take? What are the schema implications? |
| Market differentiation | Medium | Do competitors have this? Does it matter? |

---

## Ideas NOT to Build

To stay focused, the following ideas should be explicitly rejected unless a compelling case is made:

| Idea | Reason to Reject |
|------|-----------------|
| Full POS system | Out of scope — OneMember is loyalty, not POS |
| Product catalogue / inventory | Commoditised; POS handles this |
| Customer mobile app without merchant portal maturity | Put the merchant first; build the customer app when merchants are sticky |
| Gamification (badges, leaderboards) | Complexity without proven ROI for the target segment |
