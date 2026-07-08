<?php

namespace Tests\Feature;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression: a business name with an apostrophe or other special
 * character must never break slug generation or merchant creation.
 * Root cause was never the apostrophe itself (Str::slug already handles
 * it) — it was that slug generation had no collision handling, so two
 * merchants whose names slugify to the same value (e.g. "Mike's Coffee"
 * and "Mikes Coffee" both -> "mikes-coffee") crashed on the `slug`
 * unique constraint.
 */
class MerchantSlugTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_apostrophe_in_business_name_produces_a_clean_slug(): void
    {
        $merchant = $this->makeMerchant("Mike's Coffee", 'mikes@example.com');

        $this->assertSame('mikes-coffee', $merchant->slug);
    }

    public function test_colliding_slugs_are_disambiguated_instead_of_crashing(): void
    {
        $first  = $this->makeMerchant("Mike's Coffee", 'a@example.com');
        $second = $this->makeMerchant('Mikes Coffee', 'b@example.com');
        $third  = $this->makeMerchant('MIKES COFFEE!!', 'c@example.com');

        $this->assertSame('mikes-coffee', $first->slug);
        $this->assertSame('mikes-coffee-2', $second->slug);
        $this->assertSame('mikes-coffee-3', $third->slug);
    }

    public function test_name_with_no_transliterable_characters_falls_back_to_a_safe_slug(): void
    {
        $merchant = $this->makeMerchant('!!!', 'symbols@example.com');

        $this->assertSame('merchant', $merchant->slug);
    }

    public function test_soft_deleted_merchant_slug_is_still_respected(): void
    {
        $first = $this->makeMerchant("Mike's Coffee", 'a@example.com');
        $first->delete();

        $second = $this->makeMerchant('Mikes Coffee', 'b@example.com');

        $this->assertSame('mikes-coffee-2', $second->slug);
    }

    public function test_existing_merchant_routes_still_resolve_by_slug(): void
    {
        $merchant = $this->makeMerchant("Mike's Coffee", 'mikes@example.com');
        $merchant->update([
            'onboarding_completed_at' => now(),
            'settings'                => ['installed_apps' => ['commerce']],
        ]);

        $this->get(route('storefront.show', $merchant->slug, absolute: false))->assertOk();
    }
}
