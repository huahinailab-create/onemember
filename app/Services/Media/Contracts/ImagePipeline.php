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
     * Optimize a stored image in place (re-encode / strip metadata / convert
     * to WebP). Today's default implementation is a no-op — see
     * NullImagePipeline — so behaviour is unchanged until a real pipeline
     * is bound in a future sprint.
     */
    public function optimize(string $absolutePath): void;

    /**
     * Generate a named variant (thumbnail/medium/large, see config/media.php
     * `variants`) for a stored image and return its disk-relative path, or
     * null if variant generation isn't enabled yet.
     */
    public function variant(string $sourcePath, string $variantName): ?string;
}
