# API Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Coding-Standards.md](./Coding-Standards.md), [Security-Standards.md](./Security-Standards.md), [](./) |

---

## Purpose

Standards for any API endpoints in OneMember — current internal routes and future public API.

---

## Standards

### Current State
OneMember does not currently expose a public REST API. All routes serve Blade views.
The `/verify-email/status` endpoint (added in BUG-001) is the first JSON endpoint.

### Internal JSON Endpoints
For AJAX/polling endpoints like the verification status check:
- Always return `Content-Type: application/json`
- Use `response()->json(['key' => 'value'])`
- Always require `auth` middleware
- Return consistent error shapes: `{'error': 'message', 'code': 'ERROR_CODE'}`

### Future Public API (Phase 2+)
When the customer wallet and enterprise bridge are built, a versioned public API will be required.
Standards for that API (to be defined in a dedicated ADR and RFC):
- URL prefix: `/api/v1/`
- Authentication: Laravel Sanctum (API tokens)
- Response format: JSON:API or a documented custom format
- Versioning: URL-based (`/v1/`, `/v2/`)
- Rate limiting: `throttle` middleware per API key
- Always return HTTP status codes that mean what they say

### What NOT to Do Now
- Do not build the public API before it is specified in a product requirement
- Do not mix web and API routes in the same controller
- Do not return HTML from a route that is intended to be consumed by an API client
