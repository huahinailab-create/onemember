# 06 — Coding Standards

> **Last updated:** 2026-06-27

## PHP / Laravel

- **Style:** PSR-12, enforced by [Laravel Pint](https://laravel.com/docs/pint) (`composer pint`)
- **PHP version:** 8.3+ — use named arguments, readonly properties, enums, and match expressions where they improve clarity
- **Naming:**
  - Classes: `PascalCase`
  - Methods & variables: `camelCase`
  - Database columns & config keys: `snake_case`
  - Constants & enum cases: `SCREAMING_SNAKE_CASE`
- **Controllers** are thin — validate input, call a service, return a response
- **Services** hold business logic and are injected via constructor
- **No raw SQL** — use Eloquent or the Query Builder
- **No `DB::statement` in migrations** unless absolutely necessary

## Blade Templates

- Use `@section` / `@yield` for page-level content
- Use `<x-component />` Blade components for reusable UI
- No PHP logic in views — move it to the controller or a view model
- Flash messages are rendered by the layout; views do not render their own alerts

## JavaScript

- ES modules only (`type: module` in `package.json`)
- Bootstrap JS is imported once in `resources/js/app.js`
- No inline `<script>` blocks in Blade templates — use `@push('scripts')`

## CSS

- Bootstrap utility classes first
- Custom styles go in `resources/css/app.css`
- CSS custom properties (`--bs-*`) for theme overrides
- No `!important` except where overriding Bootstrap specificity intentionally

## Git

- Branch naming: `feature/short-description`, `fix/short-description`, `chore/short-description`
- Commit messages: conventional commits format — `feat:`, `fix:`, `chore:`, `docs:`, `refactor:`, `test:`
- PRs require at least one approval before merging
- `main` branch is always deployable

## Testing

- Feature tests for all HTTP endpoints
- Unit tests for service-layer logic
- Run the suite before every commit: `composer test`
- Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
