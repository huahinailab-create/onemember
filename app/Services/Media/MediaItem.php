<?php

namespace App\Services\Media;

/**
 * OMEGA-001C Part 7 — the shape a future gallery item will take. Plain DTO,
 * not an Eloquent model: no table exists yet, and none is needed until a
 * module actually ships multi-image galleries. MediaService can start
 * returning these from a `store()` call site without any schema change.
 */
final class MediaItem
{
    public function __construct(
        public readonly string $path,
        public readonly ?string $url = null,
        public readonly ?string $altText = null,
        public readonly ?string $caption = null,
        public readonly int $displayOrder = 0,
        public readonly bool $isPrimary = false,
    ) {
    }
}
