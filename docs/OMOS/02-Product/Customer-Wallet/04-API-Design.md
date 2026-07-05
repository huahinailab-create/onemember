# 04 — API Design (Wallet API v1)

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Principles

- REST, JSON, versioned path prefix `/api/wallet/v1` on `wallet.onemember.co`.
- Auth: Laravel Sanctum bearer tokens (issued at OTP verification). Web wallet uses session cookies; the API serves the PWA, pass web services, and the future native app (Phase 4) without redesign.
- All identifiers are UUIDs (`public_uuid`); numeric DB ids never leave the server.
- The **Enterprise Bridge API is a separate spec (PH2-003)** — it is *not* this API. This API is customer-credentialed only; no merchant or server-to-server access.

## 2. Endpoints

### Auth
| Method | Path | Purpose |
|---|---|---|
| POST | /auth/otp/request | Body: phone, purpose. Rate-limited (Doc 05 §3). 202 always (no phone enumeration) |
| POST | /auth/otp/verify | Body: phone, code. → 200 {token, customer} or 422 |
| POST | /auth/logout | Revoke current token |

### Profile & settings
| Method | Path | Purpose |
|---|---|---|
| GET | /me | Customer profile + preference flags |
| PATCH | /me | name, email, birthday, locale |
| POST | /me/export | Queues PDPA export; emailed link (Doc 06 §6) |
| DELETE | /me | Account deletion flow (Doc 06 §5) |

### Memberships
| Method | Path | Purpose |
|---|---|---|
| GET | /memberships | Card list: merchant branding, balance, next reward progress |
| GET | /memberships/{uuid} | Detail: balance, history (paginated), rewards, member QR payload |
| POST | /memberships | Join: body {merchant_slug, consents{...}} from universal QR landing |
| POST | /memberships/claim | Claim existing Member by verified phone match (BD-05) |
| DELETE | /memberships/{uuid} | Unlink (soft; Member record untouched) |

### Consent
| Method | Path | Purpose |
|---|---|---|
| GET | /consents | Current state per merchant per data_type |
| PUT | /consents/{merchant_uuid} | Body: {data_type: granted} map → appends consent rows |

### Passes (pending BD-04)
| Method | Path | Purpose |
|---|---|---|
| POST | /memberships/{uuid}/passes/apple | Returns .pkpass download |
| POST | /memberships/{uuid}/passes/google | Returns "Save to Google Wallet" JWT link |

### Apple PassKit web service (Apple-dictated contract, unauthenticated paths + pass token)
```
POST   /passkit/v1/devices/{deviceLibraryId}/registrations/{passTypeId}/{serial}
DELETE /passkit/v1/devices/{deviceLibraryId}/registrations/{passTypeId}/{serial}
GET    /passkit/v1/devices/{deviceLibraryId}/registrations/{passTypeId}?passesUpdatedSince=
GET    /passkit/v1/passes/{passTypeId}/{serial}
POST   /passkit/v1/log
```

## 3. Response Envelope & Errors

```json
{ "data": { ... } }
{ "error": { "code": "consent_required", "message": "…localised…" } }
```

| HTTP | Codes used |
|---|---|
| 401 | unauthenticated |
| 403 | not_your_resource (scoping) |
| 404 | unknown uuid (never distinguish "exists but not yours") |
| 409 | already_linked, duplicate_join |
| 422 | validation, otp_invalid, otp_expired |
| 429 | otp_rate_limited, api_rate_limited |

## 4. Rate Limits

| Scope | Limit |
|---|---|
| OTP request per phone | 3 / 15 min, 10 / day |
| OTP verify per challenge | 5 attempts |
| Authenticated API | 60 req/min per token |
| Join endpoint | 10 / hour per customer |

## 5. Versioning Policy

- Breaking changes → `/v2`; v1 supported ≥ 12 months after v2 ships.
- Additive fields are non-breaking; clients must ignore unknown fields.
