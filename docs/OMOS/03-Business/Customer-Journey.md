# Customer Journey

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Merchant-Journey.md](./Merchant-Journey.md), [Customer-Personas.md](./Customer-Personas.md), [00-Executive/North-Star-Metric.md](../00-Executive/North-Star-Metric.md) |

---

## Purpose

The Customer Journey documents the complete experience of a consumer from first QR scan to long-term loyalty engagement. The quality of this journey directly determines the North Star Metric — customers joining merchants in under 30 seconds — and the long-term value of the Customer Wallet.

---

## Phase 1 Customer Journey (Current)

### Step 1 — Encounter (0 seconds)

The customer sees a QR code at a merchant counter, on a table card, or in a window display. The QR code may include a brief prompt: "Scan to earn points" or "Join our loyalty programme."

**Success:** Customer picks up their phone and scans.

**Friction to minimise:**
- QR code must be large enough to scan comfortably
- Label text must be bilingual (Thai + English) in tourist areas
- No instructions required — the action should be self-evident

---

### Step 2 — Join Flow (0–30 seconds)

Customer scans QR code. Browser opens (no app download required). Join form appears.

**Required fields (Phase 1):**
- Name (first name minimum)
- Phone number OR email
- Date of birth (optional — enables birthday bonus)

**Process:**
1. Form submission → member record created
2. Confirmation screen: "You joined [Merchant Name]! You have 0 points."
3. Optionally: OTP verification for phone number (configurable by merchant)

**Target:** Complete in under 30 seconds.

**Friction to eliminate:**
- Form fields the customer does not understand
- Required fields that are not necessary (address, full name, ID number)
- Error messages that do not tell the customer how to fix the problem
- Slow page load on mobile data
- Any step that requires an app download

**North Star target:** ≥ 90% of customers who start the join flow complete it. ≥ 80% of completions take under 30 seconds.

---

### Step 3 — First Transaction

Customer makes a purchase. Staff record the transaction (manual in Phase 1, POS-integrated in Phase 3). Points or stamps are awarded.

**Success:** Customer sees their balance update and understands what they earned.

**Friction to minimise:**
- Staff must know how to record a transaction
- Customer must receive clear confirmation (SMS, LINE, or in-app)
- Points calculation must be transparent

---

### Step 4 — Ongoing Engagement

Customer receives relevant communications from the merchant:
- Points balance update after each transaction
- Birthday bonus notification
- Win-back message if they have not visited in 45–60 days
- Reward available notification when enough points are accumulated

**What makes this work:**
- Communication is timely and relevant — not spam
- Messages are personal: "Hi [Name], you have 240 points at [Merchant Name]"
- Redemption is simple: customer shows their QR code or phone number at the counter

---

### Step 5 — First Redemption

Customer earns enough points for a reward and redeems it.

This is the highest-value moment in the customer journey. A successful first redemption converts a transactional member into a loyal advocate.

**Success criteria:**
- Customer knows they have enough points for a reward
- Redemption process at the counter is clear to both staff and customer
- Customer receives confirmation of redemption
- Customer feels the reward was worth the effort

---

## Phase 2 Customer Journey (Customer Wallet)

When the Customer Wallet launches, the customer journey expands significantly.

### Universal Identity
A customer creates one OneMember account that links to all their merchant memberships. They do not need to join each merchant separately using their phone number — they scan the QR code and their wallet identity is linked automatically.

### Wallet Dashboard
Customer opens the wallet and sees:
- All their active merchant memberships in one list
- Points balance per merchant
- Upcoming reward milestones ("3 more stamps at [Bakery Name] for a free bread")
- Recent transaction history

### Privacy Controls
Customer can see, per merchant:
- What information the merchant can see
- When they joined
- Transaction history
- Option to revoke membership (removes their data from the merchant's view)

### Discovery
Customer can discover other OneMember merchants near them. This is the network effect: a customer who joins 3 merchants may discover a 4th through the wallet.

---

## Customer Journey Success Metrics

| Stage | Metric | Phase 1 Target |
|---|---|---|
| Join flow start | % of QR scans that reach the form | > 95% |
| Join flow completion | % of form starts that complete | > 90% |
| Join time | Median seconds, scan to confirmation | < 20 seconds |
| First transaction | % of members who transact within 7 days | > 60% |
| 90-day retention | % of members who transact again within 90 days | > 60% |
| First redemption | % of members who ever redeem a reward | > 40% |
| NPS | Customer Net Promoter Score | > 60 |
