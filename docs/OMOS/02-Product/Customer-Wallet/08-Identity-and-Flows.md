# 08 — Universal Identity, QR Sharing & Member Onboarding Flows

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Universal OneMember Identity

**One customer, one verified phone, many memberships.**

- Identity anchor: `customers.phone` (OTP-verified, E.164). Per Glossary: "In Phase 2 (Customer Wallet), a single consumer identity may link to multiple Merchant memberships."
- The wallet identity **references** Member records via `customer_member_links`; it never absorbs them. Merchants keep full Phase 1 control of their Member data.
- Identity states:

| State | Meaning |
|---|---|
| Guest | Scanned a QR, no account |
| Verified | OTP passed, account exists |
| Linked(m) | Has a live link at merchant m |
| Anonymised | Retention/erasure applied; links severed |

- One Member ↔ at most one customer (DB unique on `member_id`). One customer ↔ at most one live link per merchant.

## 2. Universal QR Join Flow (new consumer)

```
Scan poster QR → /join/{slug}?sig
 → Landing: merchant name, logo, programme summary, "Join in 15 seconds"
 → Enter phone → OTP → verified (account auto-created if new)
 → Consent screen (profile ✓ default; birthday/marketing/analytics opt-in)
 → WalletLinkService:
      Member exists with this phone at merchant?  → CLAIM path (§4)
      else → create Member (Phase 1 write-path: StoreMemberRequest rules,
             merchant-scoped, member_code generated as today), linked_via=qr_join
 → Membership card shown; prompt: add to home screen / native pass (BD-04)
```

Design constraints: p50 ≤ 15 s; works in LINE/Facebook in-app browsers (no popups, no third-party cookies); Thai default copy.

## 3. QR Sharing Between Merchants (cross-merchant growth loop)

The Bible's flywheel ("More customers scan QR → join wallet → consumers choose OneMember merchants"), expressed as product mechanics:

1. **In-wallet discovery:** after joining merchant A, the wallet may show "Other OneMember programmes near you" — **only** merchants with `wallet_visible = true` (BD-06). No customer data flows between merchants; discovery is one-directional browsing.
2. **One-tap subsequent join:** joining merchant B reuses verified identity — single tap + B-specific consent screen. No new OTP.
3. **Merchant cross-promo QR (optional, both-sides opt-in):** merchant A may print a "Discover more OneMember shops" QR (generic wallet directory link). It never encodes customer identity — it is the same public directory URL for everyone.
4. **Hard rule (Doc 06 §8):** merchant B never learns the customer came from merchant A; merchants never see each other's member lists or the customer's other memberships.

## 4. Merchant Onboarding of Existing Members (claim flow)

Merchants already hold Members created in Phase 1 (manual entry/CSV). Bringing them into the wallet:

### Customer-initiated (primary, BD-05)
```
Customer joins via QR → phone OTP-verified
 → WalletLinkService finds Member(s) at this merchant with matching phone
 → exactly 1 active match  → "We found your existing membership (125 แต้ม). Claim it?"
       → claim → linked_via=claim_existing → history + balance appear instantly
 → 2+ matches (E-02)       → link most recently active; flag duplicates to merchant
 → 0 matches               → fresh Member created
```
Phone ownership is proven by OTP, so auto-offer is safe (BD-05 recommendation).

### Merchant-initiated invites (secondary)
- Merchant members list → "Invite to Wallet" (per member or bulk for members with phone numbers).
- Sends email (if member has one) / prints per-member QR slip containing a signed one-time claim link: `/join/{slug}?claim={signed member ref}`.
- Customer still must OTP-verify the phone on the Member record — the invite shortcuts discovery, never verification.
- `linked_via=merchant_invite` for funnel analytics.

### What merchants see after linking
- Member list badge "in wallet"; dashboard count of linked members.
- Synced profile/birthday **only** under the respective consents.
- No wallet contact details beyond what their Member record already holds.

## 5. Unlink & Re-link

- Customer unlink (DELETE membership): link soft-closed (`unlinked_at`), consents auto-withdrawn, Member record untouched, passes revoked.
- Re-join later: new link row (fresh provenance), history preserved because history lives on the Member.
- Merchant archives Member: card goes read-only "archived" (E-03); link stays for history until customer unlinks.
