<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * OMEGA-001A — commerce-grade product image pipeline.
 *
 * Server-authoritative optimization (the client may pre-crop/rotate, but
 * every stored image passes through here):
 *  - longest edge capped at MAX_EDGE px (aspect ratio preserved, never
 *    upscaled)
 *  - converted to WebP (transparency preserved for PNG/WebP sources)
 *  - stored merchant-scoped on the public disk (BETA-008A convention)
 *
 * Requires GD with WebP (checked at runtime); when unavailable the original
 * upload is stored unchanged so the feature degrades, never breaks.
 */
class ProductImageService
{
    public const MAX_EDGE = 1200;
    public const WEBP_QUALITY = 85;

    /** Store an upload optimized; returns the disk-relative path. */
    public function store(UploadedFile $file, Merchant $merchant): string
    {
        $directory = "products/{$merchant->id}";

        $optimized = $this->optimize($file);

        if ($optimized === null) {
            return $file->store($directory, 'public');
        }

        $path = $directory . '/' . Str::random(40) . '.webp';
        Storage::disk('public')->put($path, $optimized);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Resize (longest edge) + WebP-encode. Returns null when the runtime
     * can't optimize (missing GD/WebP or unreadable image) — caller falls
     * back to storing the original.
     */
    public function optimize(UploadedFile $file): ?string
    {
        if (! function_exists('imagewebp')) {
            return null;
        }

        $source = $this->createImage($file);
        if ($source === null) {
            return null;
        }

        $width  = imagesx($source);
        $height = imagesy($source);
        $edge   = max($width, $height);

        if ($edge > self::MAX_EDGE) {
            $scale     = self::MAX_EDGE / $edge;
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
        $ok   = imagewebp($source, null, self::WEBP_QUALITY);
        $blob = ob_get_clean();
        imagedestroy($source);

        return ($ok && $blob !== false && $blob !== '') ? $blob : null;
    }

    /** @return \GdImage|null */
    private function createImage(UploadedFile $file)
    {
        return match ($file->getMimeType()) {
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()) ?: null,
            'image/png'  => @imagecreatefrompng($file->getRealPath()) ?: null,
            'image/webp' => @imagecreatefromwebp($file->getRealPath()) ?: null,
            default      => null,
        };
    }
}
