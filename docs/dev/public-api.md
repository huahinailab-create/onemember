# Public API Foundation (PLATFORM-002 P5)

Contract of record: [api/openapi.yaml](api/openapi.yaml) — endpoints must be
described there before release.

- **Versioning**: URL — `/api/v1/...`; breaking changes open `/api/v2`.
  Resource fields are append-only within a version.
- **Auth**: merchant API key as Bearer token (`api.key` middleware, optional
  ability parameter, e.g. `api.key:members:read`). Keys: `om_live_` +
  40 random chars, sha256 at rest (`api_keys`), plaintext shown once
  (`ApiKey::generate`), revocable, `last_used_at` tracked.
- **Rate limiting**: named `api` limiter — 60/min per key (per IP when
  unauthenticated). 429 + standard headers.
- **Pagination**: Laravel resource collections (`data` / `links` / `meta`);
  `per_page` capped at 100.
- **Errors**: `{ "error": { "code": string, "message": string } }` —
  `unauthenticated`, `forbidden`, `not_found`, plus framework 422 for
  validation.
- **Tenancy**: the key resolves the merchant; every query is scoped to it.
  Reference implementation: `Api\V1\MemberApiController` (read-only).
