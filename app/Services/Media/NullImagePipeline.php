<?php

namespace App\Services\Media;

use App\Services\Media\Contracts\ImagePipeline;

/**
 * OMEGA-001C default binding — preserves today's exact behaviour (files are
 * stored as uploaded, no resize/re-encode/variant generation). Replace the
 * container binding with a real pipeline to turn processing on without
 * changing MediaService or any caller.
 */
class NullImagePipeline implements ImagePipeline
{
    public function optimize(string $path): string
    {
        return $path; // Intentionally inert — see class docblock.
    }

    public function variant(string $sourcePath, string $variantName): ?string
    {
        return null;
    }
}
