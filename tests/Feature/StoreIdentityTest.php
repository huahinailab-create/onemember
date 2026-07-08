<?php

namespace Tests\Feature;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use App\Models\User;
use App\Services\StoreIdentity\StoreIdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * OMEGA-001E — Store Identity & Public URL Foundation. Business Name
 * (brand, shown exactly as typed) and Store URL (`merchants.slug`,
 * ASCII-safe path segment) are distinct concepts — see DECISION-098 and
 * ADR-015. These tests cover the StoreIdentityService directly plus the
 * Settings > Business Profile edit path.
 */
class StoreIdentityTest extends TestCase
{
    use RefreshDatabase;

    private function service(): StoreIdentityService
    {
        return app(StoreIdentityService::class);
    }

    private function makeMerchant(string $name, string $email): Merchant
    {
        $user = User::factory()->create();

        return Merchant::create([
            'user_id' => $user->id,
            'name'    => $name,
            'email'   => $email,
            'status'  => MerchantStatus::Active,
        ]);
    }

    // ── Business Name stays exactly as entered ──────────────────────────

    public function test_business_name_is_stored_verbatim_no_case_or_punctuation_change(): void
    {
        $merchant = $this->makeMerchant("Mike's Coffee", 'mikes@example.com');

        $this->assertSame("Mike's Coffee", $merchant->name);
    }

    // ── Slug generation (Part 3) ─────────────────────────────────────────

    public function test_apostrophe_name_generates_a_clean_store_url(): void
    {
        $merchant = $this->makeMerchant("Mike's Coffee", 'a@example.com');

        $this->assertSame('mikes-coffee', $merchant->slug);
    }

    public function test_thai_business_name_falls_back_to_a_safe_store_url(): void
    {
        // Str::slug() has no Thai transliteration table — this must not
        // crash and must not silently produce an empty/duplicate slug.
        $first  = $this->makeMerchant('ร้านกาแฟมิตร', 'th1@example.com');
        $second = $this->makeMerchant('ร้านกาแฟสุข', 'th2@example.com');

        $this->assertSame('merchant', $first->slug);
        $this->assertSame('merchant-2', $second->slug);
    }

    public function test_generated_store_url_never_lands_on_a_reserved_word(): void
    {
        // A business literally named "Store" would otherwise generate the
        // reserved slug "store", colliding with the /store/{slug} route.
        $merchant = $this->makeMerchant('Store', 'reserved-name@example.com');

        $this->assertNotSame('store', $merchant->slug);
        $this->assertFalse($this->service()->isReserved($merchant->slug));
    }

    // ── StoreIdentityService (Part 6) ────────────────────────────────────

    public function test_sanitize_matches_the_rules_merchants_are_told(): void
    {
        $this->assertSame('mikes-coffee', $this->service()->sanitize("Mike's Coffee!!"));
        $this->assertSame('cafe-deja-vu', $this->service()->sanitize('Café Déjà Vu'));
    }

    public function test_reserved_words_are_never_available(): void
    {
        foreach (['admin', 'store', 'settings', 'login'] as $word) {
            $this->assertTrue($this->service()->isReserved($word));
            $this->assertFalse($this->service()->isAvailable($word));
        }
    }

    public function test_taken_slug_is_unavailable_but_available_to_its_own_owner(): void
    {
        $merchant = $this->makeMerchant('Mike Coffee', 'owner@example.com');

        $this->assertFalse($this->service()->isAvailable($merchant->slug));
        $this->assertTrue($this->service()->isAvailable($merchant->slug, $merchant->id));
    }

    public function test_public_store_url_resolves_through_the_real_route(): void
    {
        $merchant = $this->makeMerchant('Mike Coffee', 'pub@example.com');

        $this->assertSame(
            route('storefront.show', $merchant->slug, absolute: true),
            $this->service()->publicStoreUrl($merchant),
        );
    }

    // ── Editing Store URL via Settings (Part 4/7) ────────────────────────

    public function test_merchant_can_change_their_store_url(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id, 'slug' => 'old-name']);

        $this->actingAs($user)->put(route('settings.profile.update'), $this->profilePayload($merchant, [
            'slug' => 'new-name',
        ]))->assertRedirect();

        $this->assertSame('new-name', $merchant->fresh()->slug);
    }

    public function test_blank_store_url_leaves_existing_slug_untouched(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id, 'slug' => 'keep-me']);

        $this->actingAs($user)->put(route('settings.profile.update'), $this->profilePayload($merchant, [
            'slug' => '',
        ]))->assertRedirect();

        $this->assertSame('keep-me', $merchant->fresh()->slug);
    }

    public function test_store_url_cannot_be_changed_to_one_already_taken(): void
    {
        $this->makeMerchant('Taken Shop', 'taken@example.com')->update(['slug' => 'taken-slug']);

        $user = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id, 'slug' => 'mine']);

        $this->actingAs($user)->put(route('settings.profile.update'), $this->profilePayload($merchant, [
            'slug' => 'taken-slug',
        ]))->assertSessionHasErrors('slug');

        $this->assertSame('mine', $merchant->fresh()->slug);
    }

    public function test_store_url_cannot_be_changed_to_a_reserved_word(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id, 'slug' => 'mine']);

        $this->actingAs($user)->put(route('settings.profile.update'), $this->profilePayload($merchant, [
            'slug' => 'admin',
        ]))->assertSessionHasErrors('slug');

        $this->assertSame('mine', $merchant->fresh()->slug);
    }

    public function test_store_url_rejects_invalid_characters(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id, 'slug' => 'mine']);

        $this->actingAs($user)->put(route('settings.profile.update'), $this->profilePayload($merchant, [
            'slug' => 'Not Valid!!',
        ]))->assertSessionHasErrors('slug');
    }

    public function test_live_availability_check_endpoint_reports_taken_reserved_and_free(): void
    {
        $this->makeMerchant('Taken Shop', 'taken2@example.com')->update(['slug' => 'taken-slug']);

        $user = User::factory()->create();
        Merchant::factory()->create(['user_id' => $user->id, 'slug' => 'mine']);

        $this->actingAs($user)
            ->getJson(route('settings.store-url.availability', ['slug' => 'taken-slug']))
            ->assertOk()->assertJson(['available' => false]);

        $this->actingAs($user)
            ->getJson(route('settings.store-url.availability', ['slug' => 'admin']))
            ->assertOk()->assertJson(['available' => false, 'reserved' => true]);

        $this->actingAs($user)
            ->getJson(route('settings.store-url.availability', ['slug' => 'brand-new-name']))
            ->assertOk()->assertJson(['available' => true]);
    }

    // ── Backward compatibility (Part 5) ──────────────────────────────────

    public function test_existing_merchant_storefront_still_resolves_after_this_sprint(): void
    {
        $merchant = $this->makeMerchant("Mike's Coffee", 'compat@example.com');
        $merchant->update([
            'onboarding_completed_at' => now(),
            'settings'                => ['installed_apps' => ['commerce']],
        ]);

        $this->get(route('storefront.show', $merchant->slug, absolute: false))->assertOk();
        $this->get(route('join.show', $merchant->slug, absolute: false))->assertOk();
    }

    private function profilePayload(Merchant $merchant, array $overrides = []): array
    {
        return array_merge([
            'name'          => $merchant->name,
            'business_type' => $merchant->business_type ?? 'Other',
            'email'         => $merchant->email,
            'currency'      => 'THB',
            'timezone'      => 'Asia/Bangkok',
        ], $overrides);
    }
}
