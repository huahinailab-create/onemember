# Backlog — Must Have

These features are required for OneMember to be a viable product. They must be completed before the platform is considered production-ready for its initial target market (Thailand SMB).

---

## Loyalty & Membership Core

- [x] Merchant registration and onboarding
- [x] Email verification
- [x] Loyalty programme creation (points-based, stamp-based)
- [x] Member management (add, view, archive)
- [x] Points earning via purchase recording
- [x] Stamp earning
- [x] Reward creation and management
- [x] Reward redemption
- [x] Member loyalty card (customer portal)
- [x] QR code for member identification
- [x] Basic transaction history
- [x] Dashboard with key metrics
- [x] Settings (profile, preferences, branding)

## Subscription & Access Control

- [x] Subscription tiers (Free, Starter, Professional)
- [x] Stripe billing integration
- [x] Trial period management
- [x] Usage limits per plan (member count, campaign count)

## Communication

- [x] Transactional emails via Resend
- [x] Email verification queue (production-safe)

## Data Management

- [x] CSV member import
- [x] Data export (members, transactions)

## Developer & Operations

- [x] Developer Tools (dev environment only)
- [x] Health check endpoint
- [x] Queue-based job processing
- [x] Audit logging for developer actions

---

## Remaining Must-Haves (Not Yet Implemented)

### Birthday Bonus
- Auto-detect members with birthdays today/this week
- Birthday points multiplier or flat bonus
- Automated birthday notification email to member

### Member Notification System
- Email/notification when points are earned
- Email/notification when reward is redeemed
- Low-balance or expiry warnings

### Point Expiry
- Configurable expiry period per loyalty programme
- Auto-expire points on schedule (queued job)
- Notification before expiry

### Campaign Analytics
- Points issued per period
- Redemption rate per reward
- Top members by points / lifetime spend
- Export analytics data

### Receipt QR Purchase Linking
- Merchant generates a QR code on receipt
- Customer scans QR to claim the purchase and earn points
- No merchant staff required for point award
- Links purchase to member account

### PromptPay Integration (Thailand)
- Merchant can display a PromptPay QR
- Payment confirmation triggers point award
- Supports both phone number and national ID linked accounts
