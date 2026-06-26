# 03 — Database

> **Status:** Draft  
> **Last updated:** 2026-06-27

## 1. Engine

- **Development:** SQLite (`database/database.sqlite`)
- **Production:** MySQL 8+ or PostgreSQL 15+

## 2. Conventions

- All table names are plural snake_case (`membership_plans`).
- All primary keys are `id` (unsigned big integer, auto-increment).
- All timestamps use Laravel defaults: `created_at`, `updated_at`.
- Soft deletes (`deleted_at`) are added only where historical records must be preserved.
- Foreign keys follow the pattern `{table_singular}_id`.

## 3. Migration Strategy

- Every schema change is a new migration file — never edit existing migrations in production.
- Migrations are reversible (`down()` method must be implemented).
- Seed data lives in `database/seeders/` and is safe to run in development only.

## 4. Entity Overview

_Tables will be documented here as they are created._

| Table | Description | Key Relations |
|-------|-------------|---------------|
| users | Application users | — |

## 5. Indexes & Performance

- Index all foreign key columns.
- Add composite indexes for columns frequently used together in `WHERE` clauses.
- Review `EXPLAIN` output before deploying queries that scan large tables.
