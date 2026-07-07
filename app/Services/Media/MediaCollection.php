<?php

namespace App\Services\Media;

use Illuminate\Support\Collection;

/**
 * OMEGA-001C Part 7 — an ordered set of MediaItem with one designated
 * primary, the shape a future "Product gallery" / "Staff photos" / etc.
 * feature will hand to a view. Not persisted; not wired into any current
 * screen. Single-image callers (today's Product::imageUrl()) are
 * unaffected and don't need to know this class exists.
 */
final class MediaCollection
{
    /** @var Collection<int, MediaItem> */
    private Collection $items;

    /** @param MediaItem[] $items */
    public function __construct(array $items = [])
    {
        $this->items = collect($items)->sortBy('displayOrder')->values();
    }

    public function primary(): ?MediaItem
    {
        return $this->items->first(fn (MediaItem $item) => $item->isPrimary) ?? $this->items->first();
    }

    /** @return Collection<int, MediaItem> */
    public function items(): Collection
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }
}
