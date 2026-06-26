# 02 — Architecture

> **Status:** Draft  
> **Last updated:** 2026-06-27

## 1. Stack

| Layer      | Technology              | Version |
|------------|-------------------------|---------|
| Language   | PHP                     | 8.3+    |
| Framework  | Laravel                 | 13.x    |
| Frontend   | Bootstrap 5             | 5.3.x   |
| Icons      | Bootstrap Icons         | 1.x     |
| Bundler    | Vite                    | 8.x     |
| Database   | SQLite (dev) / MySQL (prod) | —   |
| Queue      | Laravel Queue (sync/redis) | —    |
| Cache      | File (dev) / Redis (prod)  | —    |

## 2. Directory Structure

```
app/
  Http/
    Controllers/      # Request handlers, thin — delegate to services
    Middleware/       # HTTP middleware
    Requests/         # Form request validation classes
  Models/             # Eloquent models
  Services/           # Business logic layer
  Actions/            # Single-responsibility action classes
  Enums/              # PHP 8.1+ enums
  Exceptions/         # Custom exception classes
resources/
  views/
    layouts/          # Base layout templates (app.blade.php, guest.blade.php)
    components/       # Reusable Blade components
    pages/            # Page-level views organised by feature
docs/                 # Project documentation
```

## 3. Request Lifecycle

```
Browser → routes/web.php → Middleware → Controller → Service → Model → Response
```

## 4. Key Design Decisions

- **Thin controllers**: controllers only validate input and call services.
- **Service layer**: all business logic lives in `app/Services/`.
- **Action classes**: complex one-off operations (e.g. `CreateSubscription`) are isolated in `app/Actions/`.
- **Form Requests**: all validation is done in dedicated `FormRequest` classes.

## 5. External Services

| Service | Purpose | Notes |
|---------|---------|-------|
| TBD     |         |       |
