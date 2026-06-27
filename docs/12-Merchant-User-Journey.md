# 12 — Merchant User Journey

This document describes the complete journey a merchant takes from first discovering OneMember through to active daily use.

It is the reference for UX decisions, onboarding flow design, and sprint prioritisation.

Each step documents the purpose, the expected user action, the expected system outcome, and ideas for future improvement.

---

## Journey Overview

```
Merchant Registration
        ↓
Email Verification
        ↓
Login
        ↓
Complete Business Profile
        ↓
Choose Business Type
        ↓
Create First Campaign
        ↓
Create First Reward
        ↓
Add First Member
        ↓
Record First Transaction
        ↓
Member Earns Reward
        ↓
Merchant Dashboard
```

---

## Step 1 — Merchant Registration

**Purpose:**
Create a OneMember account. This is the merchant's first interaction with the product.

**Expected user action:**
- The merchant visits the OneMember website or landing page.
- They click "Get Started" or "Start Free Trial".
- They enter their name, email address, and a password.
- They submit the registration form.

**Expected outcome:**
- A new user account is created.
- A 30-day Professional trial begins automatically.
- A verification email is sent to the provided email address.
- The merchant is redirected to a "Check your inbox" confirmation page.

**Potential future improvements:**
- Social login (Google, Facebook) to reduce friction.
- Phone number registration as an alternative to email.
- Referral code field to support affiliate programmes.

---

## Step 2 — Email Verification

**Purpose:**
Confirm that the merchant owns the email address they registered with. Protects against spam accounts and ensures communications reach them.

**Expected user action:**
- The merchant opens the verification email.
- They click the verification link.

**Expected outcome:**
- The merchant's email is marked as verified.
- The merchant is redirected to the login page with a confirmation message.

**Potential future improvements:**
- SMS verification as an alternative.
- Resend verification email link on the holding page.
- Auto-login after verification (skip the login step).

---

## Step 3 — Login

**Purpose:**
Authenticate the merchant and grant access to their workspace.

**Expected user action:**
- The merchant enters their email address and password.
- They click "Log In".

**Expected outcome:**
- The merchant is authenticated.
- They are redirected to the admin dashboard.
- If their email is not yet verified, they see a prompt to verify first.

**Potential future improvements:**
- Remember me / persistent session option.
- Two-factor authentication for security-conscious merchants.
- "Forgot password" flow (already implemented via Laravel Breeze).

---

## Step 4 — Complete Business Profile

**Purpose:**
Capture the essential business information that personalises the platform for the merchant and is used on member-facing communications and receipts.

**Expected user action:**
- The merchant is prompted (or navigates) to the Merchant Profile page.
- They fill in their business name, contact person, email, mobile number, address, currency, and time zone.
- They save the profile.

**Expected outcome:**
- The merchant's business profile is saved.
- The platform is now personalised with the merchant's business name.
- Currency and time zone are applied to all transactions and reports.

**Potential future improvements:**
- Onboarding checklist or progress bar showing profile completion.
- Business logo upload (currently placeholder — coming in a future sprint).
- Auto-detection of time zone based on browser or IP.

---

## Step 5 — Choose Business Type

**Purpose:**
Help the merchant select the industry that best describes their business. This allows OneMember to suggest relevant default campaigns, rewards, and settings.

**Expected user action:**
- The merchant selects their industry from a list of supported business types.
- They confirm their selection.

**Expected outcome:**
- The merchant's business type is recorded.
- The platform may suggest pre-built templates relevant to their industry (future feature).

**Current status:** Business type selection is not yet implemented. This step is planned for a future sprint.

**Potential future improvements:**
- Pre-built campaign and reward templates per industry.
- Industry-specific onboarding tips and examples.
- Ability to change business type later.

---

## Step 6 — Create First Campaign (Loyalty Program)

**Purpose:**
Set up the mechanism by which customers earn points or stamps. This is the foundation of the merchant's loyalty offering.

**Expected user action:**
- The merchant navigates to Loyalty Programs.
- They click "Create Program".
- They choose a program type (Points or Stamps).
- They configure the program name, earn rate (or stamps required), and start date.
- They save the program.

**Expected outcome:**
- A new loyalty program is created with status `active`.
- The merchant can now record transactions against this program.

**Current status:** Loyalty Program creation is not yet implemented. Planned for Sprint 3.

**Potential future improvements:**
- Pre-built program templates (e.g. "10 visits = 1 free service").
- Program preview showing a simulated member experience.
- Duplicate existing program.

---

## Step 7 — Create First Reward

**Purpose:**
Define what customers receive when they redeem their points or complete a stamp card. Rewards give the loyalty programme its value to the customer.

**Expected user action:**
- The merchant navigates to Rewards.
- They click "Add Reward".
- They choose the reward type (Discount, Free Item, Gift, or Cashback).
- They set the points required, reward name, and value.
- They set availability and validity dates (optional).
- They save the reward.

**Expected outcome:**
- A new reward is created and linked to the merchant's loyalty program.
- Members can redeem points for this reward once they accumulate enough.

**Current status:** Reward creation is not yet implemented. Planned for Sprint 3.

**Potential future improvements:**
- Reward image upload for visual display.
- Popular reward templates per industry.
- Limited-time or event-based rewards.

---

## Step 8 — Add First Member

**Purpose:**
Enrol the merchant's first customer into the loyalty programme. This is the first moment the product becomes personally useful to the merchant.

**Expected user action:**
- The merchant navigates to Members.
- They click "Add Member".
- They enter the customer's full name, mobile number, and date of birth (required).
- They optionally enter a nickname, email, and notes.
- They save the member.

**Expected outcome:**
- A new member record is created with status `active`.
- A unique member code is generated automatically.
- The member appears in the Members List.
- The merchant is redirected to the Members List with a success message.

**Current status:** ✅ Implemented in Sprint 2 (Task 2.4).

**Potential future improvements:**
- CSV bulk import for merchants migrating from another system.
- Member self-registration via a QR code or web form.
- Duplicate detection beyond phone number (e.g. fuzzy name match).

---

## Step 9 — Record First Transaction

**Purpose:**
Award points or a stamp to the member for their first qualifying purchase or visit. This is the moment the loyalty engine activates.

**Expected user action:**
- The merchant navigates to the member's workspace.
- They select the loyalty program.
- They enter the purchase amount (for points) or click "Award Stamp" (for stamp cards).
- They confirm the transaction.

**Expected outcome:**
- A transaction record of type `earn` is written.
- The member's `total_points` and `lifetime_points` are updated.
- The transaction appears in the member's Points History tab.
- The merchant sees the updated balance immediately.

**Current status:** Not yet implemented. Planned for Sprint 3.

**Potential future improvements:**
- POS integration so transactions are recorded automatically.
- QR code scan to identify the member at point of sale.
- Batch transaction entry for merchants who record purchases at end of day.

---

## Step 10 — Member Earns Reward

**Purpose:**
The member accumulates enough points to become eligible for a reward. The merchant redeems the reward on the member's behalf.

**Expected user action:**
- The merchant views a member's workspace and sees they have sufficient points.
- They click "Redeem Reward".
- They select the reward the member wants to claim.
- They confirm the redemption.
- They deliver the reward to the member (physically or digitally).
- They mark the redemption as "Used".

**Expected outcome:**
- A redemption record is created with status `pending` and a unique code.
- A transaction of type `redeem` is written with a negative point value.
- The member's `total_points` is decreased by the reward's `points_required`.
- The redemption is marked `used` when the merchant confirms delivery.

**Current status:** Not yet implemented. Planned for Sprint 3.

**Potential future improvements:**
- One-click redemption from a QR scan at point of sale.
- Member self-redemption via member-facing portal (future).
- Automatic reward issuance when stamp card is completed.

---

## Step 11 — Merchant Dashboard

**Purpose:**
Give the merchant an at-a-glance summary of their loyalty programme's performance. This becomes the merchant's daily home screen after setup is complete.

**Expected user action:**
- The merchant logs in and views the dashboard.
- They review key metrics: active members, transactions today, points awarded this month, top rewards.

**Expected outcome:**
- The merchant has instant visibility into programme activity.
- They can identify members who are close to earning a reward.
- They can spot inactive members and take action.

**Current status:** Dashboard displays "Coming soon" placeholder. Planned for a future sprint after core loyalty engine is implemented.

**Potential future improvements:**
- Birthday alerts for members with upcoming birthdays.
- Low-stock reward alerts.
- Export dashboard data to CSV.
- Comparison with previous period (e.g. this month vs. last month).

---

*This document must be updated whenever a new onboarding step is added, removed, or significantly changed.*
