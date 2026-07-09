<?php

namespace App\Services\Media;

use App\Services\Media\Contracts\ImagePipeline;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * OMEGA-001C — Unified Media Foundation.
 *
 * The single place that stores, replaces, deletes, validates, and resolves
 * URLs for uploaded files. No controller should touch Storage/UploadedFile
 * directly for anything MediaService covers — see
 * docs/OMOS/12-ADR/ADR-013-Unified-Media-Foundation.md.
 *
 * Today this only backs Commerce product images (DECISION-094); every other
 * `collections` entry in config/media.php is declared for future modules
 * (merchant logos, staff photos, customer avatars, ...) to adopt without
 * inventing their own upload/validation code.
 */
class MediaService
{
    public function __construct(
        private readonly ImagePipeline $pipeline,
    ) {
    }

    /**
     * Store a new upload for the given collection, merchant/owner-scoped.
     * Returns the disk-relative path (what callers persist to their own
     * `*_path` column — no schema change required).
     */
    public function store(UploadedFile $file, string $collection, int|string $ownerId): string
    {
        $path = $file->store($this->collectionPath($collection, $ownerId), $this->diskName());

        // The pipeline may convert/rename (e.g. → .webp); persist ITS path.
        return $this->pipeline->optimize($path);
    }

    /**
     * Replace an existing file: delete the old one (if any) and store the
     * new upload. Same net effect as the current Product-image controller
     * logic, centralized so every future module gets it for free.
     */
    public function replace(?string $existingPath, UploadedFile $file, string $collection, int|string $ownerId): string
    {
        $this->delete($existingPath);

        return $this->store($file, $collection, $ownerId);
    }

    public function delete(?string $path): void
    {
        if ($path) {
            $this->disk()->delete($path);
        }
    }

    public function url(?string $path): ?string
    {
        return $path ? $this->disk()->url($path) : null;
    }

    /**
     * Validation rules for a given collection's upload field. Centralizes
     * mime/size limits from config/media.php so they're declared once
     * instead of copy-pasted per controller.
     */
    public function validationRules(string $collection = 'default'): array
    {
        return [
            'nullable',
            'image',
            'mimes:' . implode(',', config('media.image_mimes')),
            'max:' . config('media.max_image_kb'),
        ];
    }

    /**
     * Re-run optimization (re-encode/strip metadata/WebP) on an already
     * stored file. A no-op until a real ImagePipeline is bound — see
     * NullImagePipeline — so calling this today changes nothing.
     */
    public function optimize(string $path): string
    {
        return $this->pipeline->optimize($path);
    }

    /**
     * Resolve (generating if needed) a named variant — thumbnail/medium/
     * large, per config/media.php `variants`. Returns null until variant
     * generation is enabled; callers can start requesting variants now
     * without waiting for that work to land.
     */
    public function variant(string $path, string $variantName): ?string
    {
        return $this->pipeline->variant($path, $variantName);
    }

    /**
     * The disk files for this collection live on today. Business logic
     * should never call Storage::disk() directly — going through here means
     * switching to S3/R2/Spaces/Azure/Backblaze later is a config change,
     * not a code change (Part 5 of ADR-013).
     */
    public function disk(): Filesystem
    {
        return Storage::disk($this->diskName());
    }

    private function diskName(): string
    {
        return config('media.default_disk');
    }

    private function collectionPath(string $collection, int|string $ownerId): string
    {
        $prefix = config("media.collections.{$collection}", $collection);

        return "{$prefix}/{$ownerId}";
    }
}
