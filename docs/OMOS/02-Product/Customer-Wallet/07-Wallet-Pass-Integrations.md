# 07 — Apple Wallet & Google Wallet Integration Design

| Field | Value |
|---|---|
| **Status** | Review — blocked by BD-04 (Bible amendment + accounts/budget) |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

> ⚠️ Native passes are **not currently in the Product Bible**. This design is complete but implementation is gated on BD-04.

---

## 1. Shared Concepts

- One pass = one `customer_member_link` per platform (`wallet_passes` table, Doc 03).
- Pass front: merchant logo + brand colours (existing `MerchantBrandingService` assets), balance field, next-reward progress, member QR (same payload as on-screen wallet QR, Doc 05 §4).
- Update trigger: `WalletBalanceChanged` event → queued `RefreshWalletPass` job (per platform). Retries via `wallet:refresh-passes` hourly sweep.
- Both integrations live behind `PassKitService` / `GoogleWalletService` interfaces so providers are swappable and testable with fixtures.

## 2. Apple Wallet (PKPass)

### Requirements
- Apple Developer Program (USD 99/yr) — BD-04
- Pass Type ID certificate (`pass.co.onemember.wallet`), WWDR intermediate cert
- APNs key for pass update pushes

### Pass structure (`storeCard` style)
```
pass.json
  passTypeIdentifier: pass.co.onemember.wallet
  serialNumber:       wallet_passes.serial_number (uuid)
  teamIdentifier:     <Apple team>
  webServiceURL:      https://wallet.onemember.co/passkit/v1
  authenticationToken: per-pass random (hashed at rest)
  storeCard:
    primaryFields:   balance ("125 แต้ม")
    secondaryFields: merchant name, next reward progress
    barcode:         PKBarcodeFormatQR → OM1 payload
  backFields:        membership since, portal link, consent notice
logo.png / icon.png / strip.png  ← merchant branding, resized server-side
```

### Generation
- PHP-native signing (openssl PKCS#7 detached signature over manifest.json); packaged zip → `.pkpass` response. No third-party SaaS — certificates on server, path from `.env`.
- Generated on demand (POST /passes/apple), cached on disk keyed by serial + balance version.

### Update flow (Apple-dictated)
1. Balance changes → job marks pass stale → APNs push (empty payload) to registered devices.
2. Device calls `GET /passkit/v1/.../registrations?passesUpdatedSince` → serials.
3. Device fetches `GET /passkit/v1/passes/{type}/{serial}` with auth token → fresh `.pkpass`.
- Endpoints + `apple_pass_registrations` table specified in Docs 03/04.

## 3. Google Wallet

### Requirements
- Google Wallet API issuer account + service account JSON — BD-04
- One **LoyaltyClass** per merchant (created lazily on first pass): programme name, logo, hex colours.
- One **LoyaltyObject** per link: `{issuerId}.{serial}`, loyaltyPoints.balance, barcode (QR, OM1 payload), heroImage optional.

### Issue flow
- "Add to Google Wallet" button → server builds signed JWT (`savetowallet` claim with the object) → `https://pay.google.com/gp/v/save/{jwt}`.
- No device registry needed: updates are server → Google REST `PATCH loyaltyobject` on balance change (same `RefreshWalletPass` job).

### Class/object lifecycle
| Event | Action |
|---|---|
| Merchant rebrands | PATCH LoyaltyClass |
| Link unlinked / consent withdrawn | Object state → `INACTIVE` |
| Merchant leaves platform | Class + objects expired |

## 4. Failure & Edge Handling

| Case | Behaviour |
|---|---|
| APNs/Google API down | Job retried with backoff; pass shows stale balance (accept — QR still valid because balance is authoritative server-side at scan) |
| Certificate expiry | `wallet:refresh-passes` emits ops alert 30 days before cert `notAfter` |
| Customer deletes account | Passes revoked (`revoked_at`), Apple devices get 401 on next fetch → pass invalidated; Google objects set INACTIVE |
| Merchant branding missing | Fallback to OneMember default pass art (brand guidelines DECISION set) |

## 5. Explicitly Out of Scope

- NFC/contactless passes (requires Apple NFC entitlement — enterprise negotiation, Phase 4 at earliest)
- Location-triggered pass notifications (privacy review first)
- Samsung Wallet (revisit with Phase 4 regional expansion)
