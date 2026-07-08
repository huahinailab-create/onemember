<?php

namespace App\Services;

use App\Models\Merchant;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\Storage;

class MerchantBrandingService
{
    private const LOGO_DISK = 'public';
    private const FALLBACK_COLOR_PRIMARY   = '#2563EB';
    private const FALLBACK_COLOR_SECONDARY = '#1E293B';

    public function __construct(private readonly ?Merchant $merchant) {}

    public function logo(): ?string
    {
        if ($this->merchant?->logo_path && Storage::disk(self::LOGO_DISK)->exists($this->merchant->logo_path)) {
            return app(MediaService::class)->url($this->merchant->logo_path);
        }

        return null;
    }

    public function primaryColor(): string
    {
        return $this->merchant?->brand_color ?: self::FALLBACK_COLOR_PRIMARY;
    }

    public function secondaryColor(): string
    {
        return $this->merchant?->secondary_color ?: self::FALLBACK_COLOR_SECONDARY;
    }

    public function displayName(): string
    {
        return $this->merchant?->name ?? config('app.name');
    }

    public function tagline(): ?string
    {
        return $this->merchant?->business_tagline ?: null;
    }

    public function receiptFooter(): ?string
    {
        return $this->merchant?->receipt_footer ?: null;
    }

    /** @return array{facebook: ?string, instagram: ?string, line: ?string, website: ?string} */
    public function socialLinks(): array
    {
        return [
            'facebook'  => $this->merchant?->facebook_url ?: null,
            'instagram' => $this->merchant?->instagram_url ?: null,
            'line'      => $this->merchant?->line_url ?: null,
            'website'   => $this->merchant?->website ?: null,
        ];
    }

    public function hasSocialLinks(): bool
    {
        return ! empty(array_filter($this->socialLinks()));
    }

    /**
     * Store a new logo, replacing the old one. Returns the stored path.
     * Delegates to MediaService (OMEGA-001C) — merchant-scoped storage
     * path and validation now come from config/media.php instead of being
     * hand-built here, same as Commerce product images.
     */
    public function storeLogo(\Illuminate\Http\UploadedFile $file): string
    {
        return app(MediaService::class)->replace(
            $this->merchant?->logo_path, $file, 'merchant_logos', $this->merchant->id,
        );
    }

    /**
     * Delete the current logo and clear logo_path on the merchant.
     */
    public function deleteLogo(): void
    {
        if ($this->merchant?->logo_path) {
            app(MediaService::class)->delete($this->merchant->logo_path);
            $this->merchant->update(['logo_path' => null]);
        }
    }
}
