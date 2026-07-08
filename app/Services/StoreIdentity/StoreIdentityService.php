<?php

namespace App\Services\StoreIdentity;

use App\Models\Merchant;
use Illuminate\Support\Str;

/**
 * OMEGA-001E — Store Identity & Public URL Foundation.
 *
 * A merchant has two distinct identities: the **Business Name** (brand,
 * shown to customers exactly as entered — see Merchant::displayName() for
 * presentation-only casing, which is unrelated) and the **Store URL** (the
 * `merchants.slug` column — a technical, ASCII-safe path segment). This
 * service is the single place that generates, validates, and resolves the
 * Store URL, so no other module hand-rolls slug logic or reads
 * `Merchant::$slug` directly — see docs/OMOS/12-ADR/ADR-015.
 *
 * No behavioural change from this sprint: the algorithm here is exactly
 * what `Merchant::uniqueSlugFor()` already did, relocated so it has one
 * home. Existing merchants' stored slugs are never touched.
 */
class StoreIdentityService
{
    /**
     * Sanitize free-typed input into a safe Store URL: lowercase,
     * ASCII-transliterated, hyphen-separated, punctuation stripped.
     * Same transformation Str::slug() performs — exposed here so the
     * merchant-editable "Store URL" field and auto-generation share
     * identical rules.
     */
    public function sanitize(string $input): string
    {
        return Str::slug($input);
    }

    /** Reserved words a Store URL may never be — see config/store_identity.php. */
    public function reservedWords(): array
    {
        return config('store_identity.reserved_words', []);
    }

    public function isReserved(string $slug): bool
    {
        return in_array($slug, $this->reservedWords(), true);
    }

    /**
     * Whether `$slug` could be assigned as a Store URL right now: not
     * reserved, not blank, and not already taken by another merchant
     * (including soft-deleted ones, since the unique index isn't
     * soft-delete-aware).
     */
    public function isAvailable(string $slug, ?int $excludeMerchantId = null): bool
    {
        if ($slug === '' || $this->isReserved($slug)) {
            return false;
        }

        return ! Merchant::withTrashed()
            ->where('slug', $slug)
            ->when($excludeMerchantId, fn ($query) => $query->whereKeyNot($excludeMerchantId))
            ->exists();
    }

    /**
     * Generate a collision-safe, non-reserved Store URL from a business
     * name. Used at merchant creation — see Merchant::booted().
     */
    public function uniqueSlugFor(string $name, ?int $excludeMerchantId = null): string
    {
        $base = $this->sanitize($name);
        if ($base === '' || $this->isReserved($base)) {
            $base = 'merchant';
        }

        $slug = $base;
        $suffix = 2;
        while (! $this->isAvailable($slug, $excludeMerchantId)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /** The merchant's public storefront path segment — "Store URL" in the UI. */
    public function storeUrl(Merchant $merchant): string
    {
        return $merchant->slug;
    }

    /** Full public storefront URL, for display/copy in Settings. */
    public function publicStoreUrl(Merchant $merchant): string
    {
        return route('storefront.show', $merchant->slug, absolute: true);
    }
}
