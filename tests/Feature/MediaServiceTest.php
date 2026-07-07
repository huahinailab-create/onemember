<?php

namespace Tests\Feature;

use App\Services\Media\MediaCollection;
use App\Services\Media\MediaItem;
use App\Services\Media\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * OMEGA-001C — the Media Foundation is architecture with no merchant-facing
 * behaviour of its own; these tests pin the service contract directly
 * rather than any screen. Product-facing behaviour stays covered by
 * ProductImageTest.
 */
class MediaServiceTest extends TestCase
{
    public function test_store_puts_the_file_under_the_configured_collection_path(): void
    {
        Storage::fake('public');
        $media = app(MediaService::class);

        $path = $media->store(UploadedFile::fake()->image('photo.jpg'), 'products', 42);

        $this->assertStringStartsWith('products/42/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_replace_deletes_the_old_file_and_stores_the_new_one(): void
    {
        Storage::fake('public');
        $media = app(MediaService::class);

        $original = $media->store(UploadedFile::fake()->image('old.jpg'), 'products', 1);
        $replaced = $media->replace($original, UploadedFile::fake()->image('new.jpg'), 'products', 1);

        Storage::disk('public')->assertMissing($original);
        Storage::disk('public')->assertExists($replaced);
    }

    public function test_delete_is_safe_on_a_null_path(): void
    {
        $this->expectNotToPerformAssertions();
        app(MediaService::class)->delete(null);
    }

    public function test_url_resolves_through_the_configured_disk(): void
    {
        Storage::fake('public');
        $media = app(MediaService::class);
        $path = $media->store(UploadedFile::fake()->image('photo.jpg'), 'products', 7);

        $this->assertSame(Storage::disk('public')->url($path), $media->url($path));
        $this->assertNull($media->url(null));
    }

    public function test_validation_rules_are_sourced_from_config_not_hardcoded(): void
    {
        config(['media.image_mimes' => ['png'], 'media.max_image_kb' => 512]);

        $rules = app(MediaService::class)->validationRules('products');

        $this->assertContains('mimes:png', $rules);
        $this->assertContains('max:512', $rules);
    }

    public function test_null_pipeline_leaves_optimize_and_variant_inert(): void
    {
        Storage::fake('public');
        $media = app(MediaService::class);
        $path = $media->store(UploadedFile::fake()->image('photo.jpg'), 'products', 3);

        // Default binding is NullImagePipeline — this must not throw and
        // must not change stored behaviour today.
        $media->optimize($path);
        $this->assertNull($media->variant($path, 'thumbnail'));
    }

    public function test_media_collection_resolves_its_primary_item(): void
    {
        $collection = new MediaCollection([
            new MediaItem(path: 'a.jpg', displayOrder: 1),
            new MediaItem(path: 'b.jpg', displayOrder: 0, isPrimary: true),
        ]);

        $this->assertSame('b.jpg', $collection->primary()->path);
        $this->assertFalse($collection->isEmpty());
    }
}
