# Security Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [](./), [Coding-Standards.md](./Coding-Standards.md), [Deployment-Standards.md](./Deployment-Standards.md) |

---

## Purpose

Security requirements that apply to every sprint and every line of code in OneMember.

---

## Standards

### Authentication
- All merchant routes: `['auth', 'verified']` middleware
- Email verification is mandatory — never disable
- Password hashing: Laravel's default bcrypt via `'password' => 'hashed'` cast
- Remember me: permitted, uses secure cookie
- Session driver: `database` in production

### Input Validation
- All form input validated at the Form Request level
- Never trust `$request->all()` without validation
- Validate file uploads: MIME type, extension, and file size
- Sanitise all output with Blade's `{{ }}` (auto-escaped) — justify every `{!! !!}`

### CSRF Protection
- CSRF token on every POST, PUT, PATCH, DELETE form
- Never disable CSRF globally
- API routes that bypass web middleware must use Sanctum token auth instead

### Secrets and Credentials
- No hardcoded API keys, passwords, or secrets anywhere in the codebase
- All secrets loaded from `.env`
- `.env` never committed to git (verified by `.gitignore`)
- Production secrets managed in Laravel Forge environment variables only

### Developer Tools
- `/dev/*` routes return 404 in production regardless of auth state
- `DevToolsAccess` middleware checks `APP_ENV !== 'production'` AND `DEV_TOOLS_ENABLED=true`
- Every developer action is logged to `developer_actions` table

### Rate Limiting
- Auth endpoints: `throttle:6,1` (6 attempts per minute)
- Verification notification: `throttle:6,1`
- Future API endpoints: rate limit per API key, defined in sprint spec

### Production Checklist
- `APP_DEBUG=false`
- `APP_ENV=production`
- No `DEV_TOOLS_ENABLED=true`
- All secrets in Forge, not in code
- HTTPS enforced (Forge handles via Let's Encrypt)
