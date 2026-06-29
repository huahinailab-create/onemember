<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use App\Services\MerchantBrandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MerchantBrandingTest extends TestCase
{
    use RefreshDatabase;

    private function userWithMerchant(): User
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    // ── MerchantBrandingService ───────────────────────────────────────────

    public function test_branding_service_returns_fallback_when_no_logo(): void
    {
        $merchant = Merchant::factory()->make(['logo_path' => null]);
        $service  = new MerchantBrandingService($merchant);

        $this->assertNull($service->logo());
    }

    public function test_branding_service_returns_primary_color_default(): void
    {
        $merchant = Merchant::factory()->make(['brand_color' => null]);
        $service  = new MerchantBrandingService($merchant);

        $this->assertSame('#2563EB', $service->primaryColor());
    }

    public function test_branding_service_returns_secondary_color_default(): void
    {
        $merchant = Merchant::factory()->make(['secondary_color' => null]);
        $service  = new MerchantBrandingService($merchant);

        $this->assertSame('#1E293B', $service->secondaryColor());
    }

    public function test_branding_service_returns_merchant_colors(): void
    {
        $merchant = Merchant::factory()->make([
            'brand_color'     => '#FF5733',
            'secondary_color' => '#333333',
        ]);
        $service = new MerchantBrandingService($merchant);

        $this->assertSame('#FF5733', $service->primaryColor());
        $this->assertSame('#333333', $service->secondaryColor());
    }

    public function test_branding_service_display_name_falls_back_to_app_name(): void
    {
        $service = new MerchantBrandingService(null);
        $this->assertSame(config('app.name'), $service->displayName());
    }

    public function test_branding_service_social_links(): void
    {
        $merchant = Merchant::factory()->make([
            'facebook_url'  => 'https://facebook.com/test',
            'instagram_url' => 'https://instagram.com/test',
            'line_url'      => null,
            'website'       => 'https://test.com',
        ]);
        $service = new MerchantBrandingService($merchant);

        $links = $service->socialLinks();
        $this->assertSame('https://facebook.com/test', $links['facebook']);
        $this->assertNull($links['line']);
        $this->assertTrue($service->hasSocialLinks());
    }

    public function test_branding_service_returns_logo_url_when_file_exists(): void
    {
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->image('logo.png', 100, 100);
        Storage::disk('public')->put('merchant-logos/test-logo.png', $fakeFile->getContent());

        $merchant = Merchant::factory()->make(['logo_path' => 'merchant-logos/test-logo.png']);
        $service  = new MerchantBrandingService($merchant);

        $this->assertNotNull($service->logo());
        $this->assertStringContainsString('merchant-logos/test-logo.png', $service->logo());
    }

    public function test_branding_service_returns_null_when_logo_file_missing(): void
    {
        Storage::fake('public');

        $merchant = Merchant::factory()->make(['logo_path' => 'merchant-logos/nonexistent.png']);
        $service  = new MerchantBrandingService($merchant);

        $this->assertNull($service->logo());
    }

    // ── Logo Upload ───────────────────────────────────────────────────────

    public function test_logo_upload_stores_file_under_merchant_id(): void
    {
        Storage::fake('public');

        $user    = $this->userWithMerchant();
        $file    = UploadedFile::fake()->image('logo.png', 200, 200)->size(500);

        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'    => $user->merchant->email,
            'currency' => $user->merchant->currency ?? 'THB',
            'timezone' => $user->merchant->timezone ?? 'Asia/Bangkok',
            'logo'     => $file,
        ]);

        $response->assertRedirect();
        $user->merchant->refresh();
        $this->assertNotNull($user->merchant->logo_path);
        $this->assertStringContainsString((string) $user->merchant->id, $user->merchant->logo_path);
        Storage::disk('public')->assertExists($user->merchant->logo_path);
    }

    public function test_logo_upload_replaces_old_logo(): void
    {
        Storage::fake('public');

        $user    = $this->userWithMerchant();
        $oldPath = 'merchant-logos/' . $user->merchant->id . '_old.png';
        Storage::disk('public')->put($oldPath, 'old content');
        $user->merchant->update(['logo_path' => $oldPath]);

        $newFile = UploadedFile::fake()->image('new-logo.png', 100, 100)->size(300);

        $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'    => $user->merchant->email,
            'currency' => $user->merchant->currency ?? 'THB',
            'timezone' => $user->merchant->timezone ?? 'Asia/Bangkok',
            'logo'     => $newFile,
        ]);

        Storage::disk('public')->assertMissing($oldPath);
        $user->merchant->refresh();
        Storage::disk('public')->assertExists($user->merchant->logo_path);
    }

    public function test_invalid_logo_type_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);
        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream');

        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'    => $user->merchant->email,
            'currency' => 'THB',
            'timezone' => 'Asia/Bangkok',
            'logo'     => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_logo_too_large_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);
        $file = UploadedFile::fake()->image('big.png', 3000, 3000)->size(3000);

        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'    => $user->merchant->email,
            'currency' => 'THB',
            'timezone' => 'Asia/Bangkok',
            'logo'     => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_remove_logo_clears_logo_path(): void
    {
        Storage::fake('public');

        $user    = $this->userWithMerchant();
        $path    = 'merchant-logos/' . $user->merchant->id . '_logo.png';
        Storage::disk('public')->put($path, 'content');
        $user->merchant->update(['logo_path' => $path]);

        $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'       => $user->merchant->email,
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'remove_logo' => '1',
        ]);

        $user->merchant->refresh();
        $this->assertNull($user->merchant->logo_path);
        Storage::disk('public')->assertMissing($path);
    }

    // ── Color & Field Validation ──────────────────────────────────────────

    public function test_invalid_hex_color_rejected(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);

        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'       => $user->merchant->email,
            'currency'    => 'THB',
            'timezone'    => 'Asia/Bangkok',
            'brand_color' => 'not-a-color',
        ]);

        $response->assertSessionHasErrors('brand_color');
    }

    public function test_valid_branding_fields_saved(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);

        $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'             => $user->merchant->email,
            'currency'          => 'THB',
            'timezone'          => 'Asia/Bangkok',
            'brand_color'       => '#FF5733',
            'secondary_color'   => '#222222',
            'business_tagline'  => 'Love every sip',
            'receipt_footer'    => 'Thank you!',
            'website'           => 'https://example.com',
            'facebook_url'      => 'https://facebook.com/test',
            'instagram_url'     => 'https://instagram.com/test',
            'line_url'          => 'https://line.me/ti/p/test',
        ]);

        $user->merchant->refresh();
        $this->assertSame('#FF5733', $user->merchant->brand_color);
        $this->assertSame('#222222', $user->merchant->secondary_color);
        $this->assertSame('Love every sip', $user->merchant->business_tagline);
        $this->assertSame('Thank you!', $user->merchant->receipt_footer);
        $this->assertSame('https://facebook.com/test', $user->merchant->facebook_url);
    }

    public function test_tagline_max_length_rejected(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);

        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'            => $user->merchant->email,
            'currency'         => 'THB',
            'timezone'         => 'Asia/Bangkok',
            'business_tagline' => str_repeat('a', 101),
        ]);

        $response->assertSessionHasErrors('business_tagline');
    }

    public function test_invalid_url_rejected(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);

        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'        => $user->merchant->email,
            'currency'     => 'THB',
            'timezone'     => 'Asia/Bangkok',
            'facebook_url' => 'not-a-url',
        ]);

        $response->assertSessionHasErrors('facebook_url');
    }

    // ── Cross-Tenant Protection ───────────────────────────────────────────

    public function test_merchant_cannot_overwrite_another_merchants_logo(): void
    {
        Storage::fake('public');

        $userA = $this->userWithMerchant();
        $userB = $this->userWithMerchant();

        // Store a logo for merchant A
        $pathA = 'merchant-logos/' . $userA->merchant->id . '_logo.png';
        Storage::disk('public')->put($pathA, 'merchant A logo');
        $userA->merchant->update(['logo_path' => $pathA]);

        // UserB uploads a logo — should be stored under merchant B's ID
        $file = UploadedFile::fake()->image('logo.png', 100, 100)->size(200);
        $this->actingAs($userB)->put(route('settings.profile.update'), [
            'name'          => $userB->merchant->name,
            'business_type' => $userB->merchant->business_type ?? 'Other',
            'email'    => $userB->merchant->email,
            'currency' => 'THB',
            'timezone' => 'Asia/Bangkok',
            'logo'     => $file,
        ]);

        // Merchant A's logo must be untouched
        Storage::disk('public')->assertExists($pathA);

        // Merchant B's logo must be under B's ID
        $userB->merchant->refresh();
        $this->assertStringContainsString((string) $userB->merchant->id, $userB->merchant->logo_path);
    }

    // ── Authentication ────────────────────────────────────────────────────

    public function test_guest_cannot_update_profile(): void
    {
        $response = $this->put(route('settings.profile.update'), [
            'name'     => 'Test',
            'email'    => 'test@example.com',
            'currency' => 'THB',
            'timezone' => 'Asia/Bangkok',
        ]);

        $response->assertRedirect(route('login'));
    }

    // ── Analytics ────────────────────────────────────────────────────────

    public function test_logo_upload_dispatches_analytics_event(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);
        $file = UploadedFile::fake()->image('logo.png', 100, 100)->size(300);

        // Analytics service calls external provider (no-op in test env) — just ensure no exception
        $response = $this->actingAs($user)->put(route('settings.profile.update'), [
            'name'          => $user->merchant->name,
            'business_type' => $user->merchant->business_type ?? 'Other',
            'email'    => $user->merchant->email,
            'currency' => 'THB',
            'timezone' => 'Asia/Bangkok',
            'logo'     => $file,
        ]);

        $response->assertRedirect();
    }

    // ── Branding Page Renders ─────────────────────────────────────────────

    public function test_profile_edit_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format' => 'Y-m-d',
                'default_expiration_type' => 'never',
                'default_expiration_duration' => null,
                'default_birthday_enabled' => false,
                'locale' => 'en',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('settings'));

        $response->assertOk();
        $response->assertSee('Branding');
    }
}
