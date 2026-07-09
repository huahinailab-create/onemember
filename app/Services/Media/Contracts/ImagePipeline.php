<?php

namespace App\Services\Media\Contracts;

/**
 * OMEGA-001C — the processing step MediaService delegates to. Swapping the
 * bound implementation (e.g. to an Intervention/Imagick-backed pipeline)
 * changes what happens to a file on disk without touching MediaService or
 * any controller that calls it.
 */
interface ImagePipeline
{
    /**
     * Optimize a stored image (re-encode / resize / convert to WebP) and
     * return the FINAL disk-relative path — implementations may rename the
     * file (e.g. .jpg → .webp). NullImagePipeline returns the path
     * unchanged; GdImagePipeline (the production binding since the OMEGA
     * merge) converts to WebP at ≤ config(media.max_edge).
     */
    public function optimize(string $path): string;

    /**
     * Generate a named variant (thumbnail/medium/large, see config/media.php
     * `variants`) for a stored image and return its disk-relative path, or
     * null if variant generation isn't enabled yet.
     */
    public function variant(string $sourcePath, string $variantName): ?string;
}
