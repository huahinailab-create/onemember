# Backlog — Should Have

These features significantly improve the product and merchant experience. They are not blockers for initial launch but should be delivered in the months following MVP.

---

## Merchant Experience

### POS-Lite
- Simple point-of-sale interface for small businesses without a full POS system
- Record a sale by amount, auto-calculate points
- Staff-facing mode (Counter Mode extended)
- Optional product/SKU entry
- Print or SMS digital receipt

### Merchant-Defined Point Value
- Merchant sets the monetary value of 1 point (e.g., 1 point = 1 THB discount)
- Used to calculate discount at redemption
- Displayed on member portal ("Your points are worth X THB")

### Product / Menu Listing
- Merchant can list products or menu items with price, photo, description
- Used for direct commerce and receipt QR matching
- Category and availability toggle (in-stock / sold-out)

### Counter Mode Enhancement
- Dedicated staff view for the point-of-sale screen
- Large, touch-friendly buttons
- Member lookup by phone, QR scan, or name
- Show current balance before and after transaction

### Merchant Branding per Campaign
- Different campaign cards show different brand imagery
- Stamper card with custom stamp icon

---

## Member Experience

### Member Self-Service Portal Enhancements
- Transaction history (what was earned, what was redeemed, when)
- Expiry warnings on points
- Birthday display on profile

### SMS Notifications (Thailand)
- OTP and point confirmation via SMS
- Thai SMS gateway integration (e.g., Twilio, DTAC Business)

---

## Analytics & Reporting

### Merchant Dashboard Analytics Expansion
- Revenue from redeemed rewards (estimated)
- Members acquired per month chart
- Cohort retention (returning vs one-time members)
- Birthday campaign performance

### Campaign Performance Page
- Per-campaign: points issued, redemptions, unique members, revenue estimate
- Reward breakdown: most redeemed rewards

---

## Operational

### Soft-Delete Member Restoration
- Admin view to restore archived members
- Audit log of who archived and when

### Bulk Member Points Adjustment
- Upload CSV to credit/debit points in bulk
- Preview before applying

### Webhook Outbound
- Merchant can register a webhook URL
- Events: member_joined, points_earned, reward_redeemed
- Useful for CRM integrations

---

## Localization

### Thai Language Completion
- All remaining `lang/th/` translations
- Thai date format (Buddhist calendar option)
- Thai currency display (THB with ฿ symbol)
- Thai phone number validation

### Multi-Currency Display
- Display points in local currency equivalent
- Support THB, USD, SGD, MYR for future markets
