# Backlog — Future Vision

These are long-horizon goals that define where OneMember is going over the next 3–5 years. They inform architecture decisions today so we don't build ourselves into a corner.

---

## The Big Picture

OneMember's ultimate goal is to become the **default loyalty and commerce infrastructure for small and medium businesses across Southeast Asia** — starting in Thailand and expanding to Malaysia, Vietnam, and Singapore.

The platform grows in three distinct phases:

1. **Merchant Tools** — Loyalty programmes, membership management, and basic commerce for SMBs.
2. **Customer Wallet** — A universal wallet where customers carry all their memberships in one place.
3. **Regional Commerce Network** — An open network connecting merchants and customers across Southeast Asia, with privacy-first data sharing and direct commerce reducing dependence on high-fee platforms.

---

## Long-Term Goals

### 1. Small-Business Loyalty & Membership (Phase 1 — Now)
The foundation. Every coffee shop, restaurant, salon, and gym in Thailand can run a professional loyalty programme without technical knowledge or expensive enterprise software.

**Success metric:** 1,000+ active merchants, 100,000+ loyalty members.

### 2. Enterprise Membership Bridge (Phase 2)
Large brands (hotel chains, airlines, retail groups) have existing membership systems. OneMember becomes the bridge — members scan one QR and points flow into the brand's native system. No app replacement required for the brand; just a seamless connector.

**Success metric:** 5+ enterprise clients using the bridge API.

### 3. OneMember Customer Wallet (Phase 2–3)
One scan, one approval, all your memberships. The customer wallet lets users join any participating merchant with a single QR scan and explicit data consent. The wallet shows points, rewards, and promotions across all brands in one view.

**Key differentiator:** Customer controls what data each merchant sees. Privacy is not a feature — it is the architecture.

**Success metric:** 50,000+ wallet users carrying 3+ brand memberships each.

### 4. Privacy-First Purchase Data (Phase 2–3)
With customer consent, purchase data powers analytics for merchants without exposing individual PII. Aggregate insights: popular products, peak hours, customer segments. Individual data is anonymized or withheld based on consent level.

**Key principle:** Data that customers did not consent to share is never visible to merchants, even in aggregate.

### 5. Direct Merchant Commerce (Phase 3)
Merchants list products and menus. Customers order directly. No 15–30% platform fee going to Grab, TikTok, or Lazada. OneMember charges a small flat fee or low percentage — always less than the incumbent platforms.

**Supported order types:**
- Dine-in (scan to order)
- Pickup (pre-order and collect)
- Delivery (radius-based, merchant-defined fee)
- Shipping (for physical goods)

**PromptPay support:** Thailand-first. QR payment → automatic point award → no staff required.

**Success metric:** Merchant GMV through OneMember exceeds platform fee savings vs incumbent platforms.

### 6. Receipt QR Purchase Linking
Merchant prints a QR on the physical receipt. Customer scans it to claim the purchase, earn points, and link the transaction to their profile — without the merchant needing staff involvement.

Works offline: QR is pre-generated, scanning is async. Points are credited when the customer's phone is online.

### 7. POS-Lite
For merchants without a full point-of-sale system, OneMember becomes the POS. Record sales, calculate points, issue digital receipts, track inventory basics. Designed for Thai street food, market stalls, and small shops.

**Target:** No training required. A merchant can onboard in under 10 minutes.

### 8. Future Native App
The current web-first approach is a deliberate choice: ship fast, learn from users, keep maintenance simple. When the wallet reaches critical mass, a native iOS + Android app will be built.

**Timeline:** After regional expansion begins (Phase 3).

**Architecture consideration:** The web API must be designed as a proper REST API before the native app sprint. Today's Blade views should not carry business logic — it belongs in the service layer.

### 9. Regional Expansion

| Market | Priority | Key considerations |
|---|---|---|
| Thailand | Now | Primary market, PromptPay, Thai language, Buddhist calendar |
| Malaysia | Next | Malay + English, QR pay (DuitNow), Islamic finance compliance for promotions |
| Vietnam | Phase 3 | Vietnamese, VNPay, high mobile-first usage |
| Singapore | Phase 3 | English, PayNow, higher enterprise deal potential |

**Architecture principle:** Multi-currency and multi-language must be designed in from the start. No Thai-specific hardcoding.

---

## Architectural Implications of the Vision

These future goals have immediate implications for how we build today:

| Future Goal | Implication for Today |
|---|---|
| Customer Wallet | API layer must be clean and versioned (no view logic in controllers) |
| Enterprise Bridge | OAuth 2.0 support must be planned, even if not implemented yet |
| Direct Commerce | Orders model must be designed as a separate domain, not bolted onto loyalty |
| Native App | REST API responses must be consistent — no HTML-in-JSON |
| Regional Expansion | No hardcoded Thai strings, currencies, or date formats |
| Privacy-First Data | `consent` table must be designed before analytics features are built |
| Receipt QR | Async job architecture already in place — extend it |
| PromptPay | Payment provider abstraction layer (so Stripe and PromptPay share interfaces) |
