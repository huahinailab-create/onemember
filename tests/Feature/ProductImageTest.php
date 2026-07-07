<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** BETA-008A — one main image per Commerce product, merchant-scoped storage. */
class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'settings'                => ['installed_apps' => ['commerce']],
        ]);
    }

    private function productPayload(array $overrides = []): array
    {
        return array_merge([
            'name'   => 'Iced Latte',
            'price'  => 65,
            'status' => 'active',
        ], $overrides);
    }

    public function test_product_can_be_created_with_image_under_merchant_scoped_path(): void
    {
        $this->actingAs($this->user)->post(route('commerce.products.store'), $this->productPayload([
            'image' => UploadedFile::fake()->image('latte.jpg', 600, 400),
        ]))->assertRedirect(route('commerce.products.index', absolute: false));

        $product = Product::firstWhere('name', 'Iced Latte');
        $this->assertNotNull($product->image_path);
        $this->assertStringStartsWith("products/{$this->merchant->id}/", $product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_image_can_be_replaced_and_old_file_is_deleted(): void
    {
        $old = UploadedFile::fake()->image('old.png')->store("products/{$this->merchant->id}", 'public');
        $product = Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'image_path'  => $old,
        ]);

        $this->actingAs($this->user)->put(route('commerce.products.update', $product), $this->productPayload([
            'name'  => $product->name,
            'image' => UploadedFile::fake()->image('new.webp'),
        ]))->assertRedirect(route('commerce.products.index', absolute: false));

        $product->refresh();
        $this->assertNotSame($old, $product->image_path);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_image_can_be_removed(): void
    {
        $path = UploadedFile::fake()->image('gone.jpg')->store("products/{$this->merchant->id}", 'public');
        $product = Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'image_path'  => $path,
        ]);

        $this->actingAs($this->user)->put(route('commerce.products.update', $product), $this->productPayload([
            'name'         => $product->name,
            'remove_image' => '1',
        ]));

        $this->assertNull($product->refresh()->image_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_non_image_file_is_rejected(): void
    {
        $this->actingAs($this->user)->post(route('commerce.products.store'), $this->productPayload([
            'image' => UploadedFile::fake()->create('malware.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors(['image']);

        $this->assertSame(0, Product::count());
    }

    public function test_oversized_image_is_rejected(): void
    {
        $this->actingAs($this->user)->post(route('commerce.products.store'), $this->productPayload([
            'image' => UploadedFile::fake()->image('huge.jpg')->size(3000), // > 2048 KB
        ]))->assertSessionHasErrors(['image']);
    }

    public function test_merchant_cannot_change_image_of_another_merchants_product(): void
    {
        $otherOwner = User::factory()->create(['email_verified_at' => now()]);
        $other = Merchant::factory()->create([
            'user_id'  => $otherOwner->id,
            'settings' => ['installed_apps' => ['commerce']],
        ]);
        $foreign = Product::factory()->create(['merchant_id' => $other->id]);

        $this->actingAs($this->user)->put(route('commerce.products.update', $foreign), $this->productPayload([
            'image' => UploadedFile::fake()->image('hijack.jpg'),
        ]))->assertForbidden();

        $this->assertNull($foreign->refresh()->image_path);
    }

    public function test_storefront_displays_product_image_and_placeholder(): void
    {
        $withImage = Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'name'        => 'Photo Latte',
            'image_path'  => UploadedFile::fake()->image('latte.jpg')->store("products/{$this->merchant->id}", 'public'),
        ]);
        Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'name'        => 'No Photo Tea',
        ]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee($withImage->imageUrl())
            ->assertSee('storefront-product-media')
            ->assertSee('bi-image'); // placeholder for the imageless product
    }

    public function test_product_list_shows_thumbnail_and_placeholder(): void
    {
        Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'image_path'  => UploadedFile::fake()->image('t.jpg')->store("products/{$this->merchant->id}", 'public'),
        ]);

        $this->actingAs($this->user)
            ->get(route('commerce.products.index', absolute: false))
            ->assertOk()
            ->assertSee('commerce-product-thumb');
    }
}
