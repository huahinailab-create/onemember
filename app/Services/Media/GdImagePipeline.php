<?php

namespace App\Services\Media;

use App\Services\Media\Contracts\ImagePipeline;
use Illuminate\Support\Facades\Storage;

/**
 * Merge of OMEGA-001A (fable-dev ProductImageService) into the OMEGA-001C
 * pipeline architecture: the GD-backed real pipeline.
 *
 * optimize(): resizes the stored image to config('media.max_edge') on the
 * longest side (aspect preserved, never upscaled), re-encodes as WebP at
 * config('media.webp_quality') with transparency preserved, and returns the
 * new disk-relative path (the .webp sibling; the original is removed).
 * Degrades safely: when GD/WebP is unavailable or the file is unreadable,
 * the original path is returned untouched.
 *
 * variant(): still future work (ADR-013 Part 4) — returns null.
 */
class GdImagePipeline implements ImagePipeline
{
    public function optimize(string $path): string
    {
        if (! function_exists('imagewebp')) {
            return $path;
        }

        $disk = Storage::disk(config('media.default_disk'));

        $contents = $disk->get($path);
        if ($contents === null) {
            return $path;
        }

        $source = @imagecreatefromstring($contents);
        if ($source === false) {
            return $path;
        }

        imagepalettetotruecolor($source);

        $width   = imagesx($source);
        $height  = imagesy($source);
        $edge    = max($width, $height);
        $maxEdge = (int) config('media.max_edge', 1200);

        if ($edge > $maxEdge) {
            $scale     = $maxEdge / $edge;
            $newWidth  = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            // Preserve transparency through the resample.
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        } else {
            imagealphablending($source, false);
            imagesavealpha($source, true);
        }

        ob_start();
        $ok   = imagewebp($source, null, (int) config('media.webp_quality', 82));
        $blob = ob_get_clean();
        imagedestroy($source);

        if (! $ok || $blob === false || $blob === '') {
            return $path;
        }

        $webpPath = preg_replace('/\.[a-zA-Z0-9]+$/', '', $path) . '.webp';
        $disk->put($webpPath, $blob);

        if ($webpPath !== $path) {
            $disk->delete($path);
        }

        return $webpPath;
    }

    public function variant(string $sourcePath, string $variantName): ?string
    {
        return null; // ADR-013 Part 4 — variants land with the gallery work.
    }
}
