# ADR-013 — Unified Media Foundation

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-07) |
| **Date** | 2026-07-07 |
| **Author** | Claude Fable 5 (sprint OMEGA-001C) |
| **Related Documents** | [DECISION-094](../../08-Product-Decisions.md) (BETA-008A / OMEGA-001A — Product Images), [DECISION-095](../../08-Product-Decisions.md) (BETA-008B / OMEGA-001B — Global Localization), [ADR-009](./ADR-009-Scale-Infrastructure.md) (Scale Infrastructure), [ADR-011](./ADR-011-Commerce-Principles-Phase-4.md) (Commerce Principles) |

---

## Context

Product images (BETA-008A / OMEGA-001A) added upload/replace/remove logic directly inside `ProductController`, with hardcoded mime/size validation and a hand-built `products/{merchant_id}` storage path — the same pattern already duplicated once for the Commerce payment-QR upload. Every future module that needs an image (merchant logos, staff photos, customer avatars, booking images, knowledge-center images, marketplace screenshots, marketing assets, documents, galleries) would otherwise re-invent this by copy-paste, each with slightly different validation and no shared path to a future non-local disk.

This sprint (OMEGA-001C) builds on the approved OMEGA-001A/B foundation to extract that logic into one reusable system before a second and third caller make the duplication expensive to unwind. It is architecture only: Commerce's merchant-facing behaviour is required to remain byte-identical.

## Decision (approved)

**MediaService** (`App\Services\Media\MediaService`) is the single entry point for storing, replacing, deleting, validating, and resolving URLs for uploaded media. `ProductController` now calls it instead of touching `Storage`/`UploadedFile` directly; no other controller was migrated in this sprint (see Migration Strategy).

**Responsibilities**

| Method | Behaviour |
|---|---|
| `store()` | Save an upload under `{collection}/{ownerId}`, run it through the bound `ImagePipeline::optimize()` |
| `replace()` | Delete the prior path (if any), then `store()` |
| `delete()` | Delete a path; safe to call with `null` |
| `url()` | Resolve a disk-relative path to a public URL; `null` in, `null` out |
| `validationRules()` | Return the `image`/`mimes`/`max` rule set from `config/media.php` |
| `optimize()` / `variant()` | Delegate to the bound `ImagePipeline` — see Variant Strategy |

**Configuration** (`config/media.php`) centralizes mime types, max upload size, default disk, WebP quality, named variant sizes, and per-collection storage-path prefixes. Controllers no longer hardcode `mimes:jpg,jpeg,png,webp` or `max:2048` — DECISION-094's values moved here unchanged.

### Storage Strategy

`MediaService` resolves the disk from `config('media.default_disk')` (today: Laravel's `public` disk) and is the only place that calls `Storage::disk()` for media it owns. Business logic — controllers, models, views — never references a disk name. Moving to S3, Cloudflare R2, DigitalOcean Spaces, Azure, or Backblaze later is a one-line config + Laravel filesystem-driver change, not a code change, because nothing outside `MediaService` knows where a file lives.

### Variant Strategy

`ImagePipeline` (`App\Services\Media\Contracts\ImagePipeline`) is the processing seam: `optimize()` and `variant()`. The bound implementation today is `NullImagePipeline` — a deliberate no-op, so `MediaService::optimize()`/`variant()` calls do nothing and stored files are byte-identical to pre-OMEGA-001C behaviour. `config('media.variants')` declares `thumbnail` (150×150 cover), `medium` (600×600 contain), and `large` (1600×1600 contain) sizes now so a future sprint can bind a real Intervention/Imagick-backed `ImagePipeline` and start generating them **without changing MediaService's public API or any caller**. No image-processing library was added in this sprint — none is needed until variants actually generate.

### Future Gallery Roadmap

`MediaItem` (path, url, altText, caption, displayOrder, isPrimary) and `MediaCollection` (ordered set + `primary()`) are plain DTOs, not Eloquent models — **no migration was added**, per the sprint's Part 7 instruction ("no database migration required unless absolutely necessary"). They exist so a future multi-image gallery (product galleries, staff photos, marketing asset libraries) has a settled shape to target: a module can start returning `MediaCollection` from its own query today and gain a real `media_items` table later by mapping rows to the same DTO, with no shape change visible to views.

### Migration Strategy

- **Migrated this sprint:** Commerce product images (`ProductController`), because it's the one caller that already existed.
- **Not migrated:** the Commerce payment-QR upload (`CommerceSettingsController`) still calls `Storage`/`UploadedFile` directly. It was left untouched to keep this sprint's diff to the one module the spec named and to respect "must not change merchant-facing behaviour" — touching a second controller widens the behavioural surface under test for no architectural gain this sprint. Migrating it is a straightforward follow-up (swap three lines for `MediaService` calls) and is noted as a known future enhancement, not a gap in the foundation.
- **Product.image_path** is unchanged: still a plain string column, `Product::imageUrl()` still resolves it via `Storage::disk('public')->url()` directly rather than through `MediaService::url()`, since making an Eloquent model depend on an app service was judged more invasive than the sprint's architecture-only scope warranted. A future pass can route it through `MediaService` for disk-abstraction consistency.
- **No data migration** was performed or required. No route changed. No merchant-visible UI changed.

## Rationale

Extracting storage/validation/URL-resolution now — while there is exactly one real caller — costs one small refactor and a config file. Waiting until three or four modules have each hardcoded their own upload logic would cost a much larger, riskier consolidation later, and each of those modules would ship with independently-drifted validation rules in the meantime.

## Consequences

- **Positive:** one config file for all future upload validation; swapping storage providers touches config, not code; a declared (if not yet implemented) path to responsive image variants; a settled DTO shape for galleries with zero schema risk today.
- **Negative / accepted:** `NullImagePipeline` means no actual optimization or variant generation happens yet — this is intentional (Part 4 of the sprint spec: "Do NOT generate every size today"). The payment-QR upload and `Product::imageUrl()` are not yet routed through the new abstraction; tracked as follow-up, not a defect.
- **Reversibility:** high. `MediaService` and its config can be deleted and `ProductController` reverted to inline `Storage` calls with no data-shape impact, since `image_path` never changed meaning.
