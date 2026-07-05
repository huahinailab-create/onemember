# 09 — Customer Journeys, Sequence & Architecture Diagrams

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

Diagrams are Mermaid (render in GitHub/most viewers).

---

## 1. Customer Journey — First Contact to Multi-Merchant

```mermaid
journey
    title New consumer → wallet power user
    section At the counter (Merchant A)
      Sees QR poster at till: 3: Consumer
      Scans with phone camera: 4: Consumer
      Enters phone, gets OTP: 3: Consumer
      Grants consent, joins: 4: Consumer
      Earns first points same visit: 5: Consumer, Staff
    section Same week
      Email: points earned: 4: Customer
      Opens wallet, sees reward progress: 5: Customer
      Adds pass to phone wallet: 4: Customer
    section Next month (Merchant B)
      Scans B's poster: 4: Customer
      One-tap join (already verified): 5: Customer
      Two cards in one wallet: 5: Customer
```

## 2. Customer Journey — Existing Member Claims Record

```mermaid
journey
    title Phase 1 member discovers the wallet
    section Discovery
      Staff mentions wallet at till: 3: Staff
      Scans universal QR: 4: Customer
    section Claim
      OTP verifies phone: 4: Customer
      Wallet finds existing 125-point record: 5: Customer
      Claims membership + consents: 5: Customer
    section Result
      Full history visible immediately: 5: Customer
      Merchant sees "in wallet" badge: 4: Merchant
```

## 3. Sequence — Universal QR Join (new customer)

```mermaid
sequenceDiagram
    actor C as Consumer
    participant W as Wallet (Blade/PWA)
    participant Auth as WalletAuthController
    participant OTP as OtpService
    participant SMS as SMS Provider (BD-09)
    participant Link as WalletLinkService
    participant Cons as ConsentService
    participant DB as Database

    C->>W: GET /join/{slug}?sig
    W->>W: WalletQrService verifies HMAC sig
    W-->>C: Landing (merchant brand, join CTA)
    C->>Auth: POST otp/request {phone}
    Auth->>OTP: issue(phone)
    OTP->>DB: store code_hash (5 min TTL)
    OTP->>SMS: send 6-digit code
    C->>Auth: POST otp/verify {phone, code}
    Auth->>DB: create/find customer, phone_verified_at
    Auth-->>C: session + consent screen
    C->>Link: POST /memberships {slug, consents}
    Link->>Cons: append consent rows (versioned)
    Link->>DB: match members.phone within merchant?
    alt existing member found
        Link-->>C: offer claim (BD-05)
    else none
        Link->>DB: create Member (Phase 1 rules) + link (qr_join)
    end
    Link-->>C: membership card
    Note over Link: MembershipLinked event → email + merchant counter
```

## 4. Sequence — Balance Change Propagates to Native Pass

```mermaid
sequenceDiagram
    participant PC as PurchaseController (Phase 1, unchanged)
    participant Ev as MemberPointsEarned event
    participant Sub as MemberEmailSubscriber
    participant WB as WalletBalanceChanged
    participant Job as RefreshWalletPass (queue)
    participant AP as APNs
    participant Dev as iPhone
    participant G as Google Wallet API

    PC->>Ev: dispatch (existing MVP-006 path)
    Ev->>Sub: handle
    Sub->>Sub: member wallet-linked? → suppress member email, use wallet stream
    Sub->>WB: dispatch
    WB->>Job: queue per platform
    Job->>AP: empty push (pass stale)
    AP->>Dev: wake pass
    Dev->>Job: GET /passkit/v1/passes/{serial} (auth token)
    Job-->>Dev: fresh .pkpass (new balance)
    Job->>G: PATCH LoyaltyObject balance
```

## 5. Sequence — Consent Withdrawal

```mermaid
sequenceDiagram
    actor Cu as Customer
    participant PC as ConsentController
    participant CS as ConsentService
    participant DB as consents (append-only)
    participant Sup as Suppression job (≤24h)
    participant M as Merchant email streams

    Cu->>PC: PUT /consents/{merchant} {marketing:false}
    PC->>CS: withdraw(marketing)
    CS->>DB: append row (granted=false, version, source)
    CS-->>Cu: confirmation + email
    Sup->>M: winback/birthday/campaign emails now skip this member
    Note over M: MemberEmailSubscriber checks ConsentService before queueing
```

## 6. Architecture — Phase 2 Target State

```mermaid
flowchart TB
    subgraph Domains
      Corp[onemember.co<br/>corporate TH/EN]
      App[app.onemember.co<br/>merchant app + /admin]
      Wal[wallet.onemember.co<br/>customer wallet + /api/wallet/v1 + /passkit/v1]
    end

    subgraph Laravel monolith
      R[Domain router] --> MC[Merchant controllers<br/>Phase 1 unchanged]
      R --> WC[Wallet controllers]
      WC --> LS[WalletLinkService]
      WC --> CS2[ConsentService]
      WC --> OS[OtpService]
      LS --> PS[PassKitService / GoogleWalletService]
      MC --> EV[(Events)]
      EV --> Q[(DB queue)]
      Q --> Mail[Queued mailables]
      Q --> PJ[RefreshWalletPass]
    end

    subgraph Single MySQL
      T1[(merchants / members /<br/>loyalty_programs / transactions<br/>— untouched)]
      T2[(customers / links /<br/>consents / passes)]
    end

    subgraph External
      SMS[SMS OTP provider BD-09]
      APNS[APNs]
      GW[Google Wallet API]
    end

    Corp --> R
    App --> R
    Wal --> R
    MC --> T1
    WC --> T2
    LS --> T1
    OS --> SMS
    PJ --> APNS
    PJ --> GW
```

## 7. Data Boundary Diagram (privacy view)

```mermaid
flowchart LR
    subgraph Customer-controlled
      CP[Wallet profile<br/>phone, name, birthday, email]
      CO[Consents per merchant]
    end
    subgraph Bridge
      L[customer_member_links]
    end
    subgraph Merchant-controlled - Phase 1
      M1[Member record A]
      M2[Member record B]
      TX[Transactions]
    end
    CP -- profile/birthday consent only --> M1
    CP -- profile/birthday consent only --> M2
    CO -. gates .-> L
    L --- M1
    L --- M2
    M1 --- TX
    M2 --- TX
    M1 x--x M2
```
(`x--x` = merchants never see each other's data; no cross-merchant edge exists.)
