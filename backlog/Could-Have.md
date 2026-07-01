# Backlog — Could Have

These features would make OneMember a stronger product but are not required for initial growth. They become relevant after the core platform is stable and merchant adoption is growing.

---

## Direct Commerce (Reducing Platform Dependency)

### Merchant Storefront
- Simple product/menu listing page within the member portal
- Customers can browse and order directly from the merchant
- Eliminates need for Grab, TikTok, or Lazada for basic orders
- Pickup and delivery options
- Delivery radius configuration (km from merchant location)
- Delivery fee configuration (flat, by distance, or free above threshold)
- Shipping support for physical products

### Order Management
- Merchant receives and manages orders
- Order status: received, preparing, ready, delivered
- Staff notification for new orders

### PromptPay Checkout (Thailand)
- Embedded PromptPay QR at checkout
- Poll for payment confirmation
- Auto-award points on confirmed payment

---

## OneMember Customer Wallet

### Universal Wallet App (Web + Future Native)
- Customer joins any participating merchant by scanning a single QR
- One profile, one approval, multiple brands
- Customer controls what profile data each merchant can see
  - Name: always shared
  - Birthday: opt-in per brand
  - Phone: opt-in per brand
  - Purchase history: opt-in per brand
- Wallet dashboard shows:
  - Points balance per brand
  - Rewards available per brand
  - Active promotions
  - Recent activity

### Cross-Brand Discovery
- Customers can browse participating merchants near them
- Filter by category, location, active campaign
- "Earn points here" call-to-action

---

## Enterprise Membership Bridge

### Enterprise Integration Layer
- Large brands (hotel chains, airlines, retail groups) often have existing membership systems
- OneMember acts as a bridge: member scans OneMember QR → points flow to the brand's own system via API
- Supports OAuth 2.0 and API key authentication
- Configurable point conversion rates between systems
- Audit trail for all cross-system transactions

### White-Label Mode
- Enterprise merchants can use a white-label version of the customer portal under their own domain
- Configurable branding (logo, colors) per enterprise account

---

## Privacy & Purchase Data Analytics

### Consent Management
- Members explicitly consent to purchase data tracking per brand
- Consent levels: none / aggregate only / full purchase detail
- Consent dashboard in wallet app
- Consent withdrawal removes personal association (data becomes anonymized)

### Anonymized Analytics for Merchants
- Merchants see trend data without individual customer PII
- Cohort analysis: age group, spend range, frequency (all anonymized)
- Useful for product decisions without privacy violation

---

## Platform Integrations

### POS System Integrations
- Lightweight API connector for popular Thai POS systems
- Trigger point award on receipt from POS
- Webhook-based, no SDK required from merchant

### LINE OA Integration
- Thailand-specific: send point notifications via LINE
- Member links their LINE account to their OneMember wallet
- Rich message format: balance, recent transaction, available rewards

---

## Operational Tools

### Merchant Impersonation (Admin)
- Super-admin can view the app as a specific merchant for support purposes
- Full audit trail of all impersonation sessions
- Cannot make changes — read-only during impersonation

### Automated Backup
- Daily database snapshot to S3
- 30-day retention
- One-click restore for disaster recovery
