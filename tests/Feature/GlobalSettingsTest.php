<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BETA-008B — Global merchant settings: country, primary + accepted
 * currencies, internal language vs customer-facing languages, timezone,
 * and customer-facing locale resolution.
 */
class GlobalSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'country'                 => 'TH',
            'currency'                => 'THB',
            'timezone'                => 'Asia/Bangkok',
            'settings'                => ['locale' => 'th'],
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'country'             => 'TH',
            'currency'            => 'THB',
            'accepted_currencies' => [],
            'timezone'            => 'Asia/Bangkok',
            'locale'              => 'th',
            'customer_languages'  => ['th', 'en'],
        ], $overrides);
    }

    public function test_merchant_can_update_global_settings(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload([
                'country'  => 'SG',
                'currency' => 'SGD',
                'timezone' => 'Asia/Singapore',
            ]))
            ->assertRedirect(route('settings', absolute: false) . '?tab=localization')
            ->assertSessionHas('success');

        $fresh = $this->merchant->fresh();
        $this->assertSame('SG', $fresh->country);
        $this->assertSame('SGD', $fresh->currency);
        $this->assertSame('Asia/Singapore', $fresh->timezone);
    }

    public function test_invalid_country_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload(['country' => 'XX']))
            ->assertSessionHasErrors(['country']);
    }

    public function test_invalid_currency_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload(['currency' => 'DOGE']))
            ->assertSessionHasErrors(['currency']);

        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload(['accepted_currencies' => ['USD', 'FAKE']]))
            ->assertSessionHasErrors(['accepted_currencies.1']);
    }

    public function test_invalid_customer_language_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload(['customer_languages' => ['xx']]))
            ->assertSessionHasErrors(['customer_languages.0']);

        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload(['customer_languages' => []]))
            ->assertSessionHasErrors(['customer_languages']);
    }

    public function test_multiple_customer_languages_are_saved_in_order(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload([
                'customer_languages' => ['km', 'en', 'th'],
            ]))->assertSessionHasNoErrors();

        $this->assertSame(['km', 'en', 'th'], $this->merchant->fresh()->customerLanguages());
    }

    public function test_internal_language_is_separate_from_customer_languages(): void
    {
        // Cambodia example: internal English, customers see Khmer + English.
        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload([
                'country'             => 'KH',
                'currency'            => 'KHR',
                'accepted_currencies' => ['USD'],
                'timezone'            => 'Asia/Phnom_Penh',
                'locale'              => 'en',
                'customer_languages'  => ['km', 'en'],
            ]))->assertSessionHasNoErrors();

        $fresh = $this->merchant->fresh();
        $this->assertSame('en', $fresh->settings['locale']);            // internal
        $this->assertSame(['km', 'en'], $fresh->customerLanguages());    // customer-facing
        $this->assertSame(['KHR', 'USD'], $fresh->acceptedCurrencies()); // primary first
        $this->assertSame('KH', $fresh->country);
    }

    public function test_settings_are_tenant_scoped(): void
    {
        $otherOwner = User::factory()->create(['email_verified_at' => now()]);
        $other = Merchant::factory()->create([
            'user_id'  => $otherOwner->id,
            'country'  => 'TH',
            'currency' => 'THB',
        ]);

        $this->actingAs($this->user)
            ->put(route('settings.localization.update'), $this->payload(['country' => 'MY', 'currency' => 'MYR']));

        $this->assertSame('MY', $this->merchant->fresh()->country);
        $this->assertSame('TH', $other->fresh()->country);
        $this->assertSame('THB', $other->fresh()->currency);
    }

    public function test_storefront_offers_configured_customer_languages(): void
    {
        $this->merchant->update(['settings' => [
            'locale'             => 'en',
            'installed_apps'     => ['commerce'],
            'customer_languages' => ['km', 'en'],
        ]]);

        $response = $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            // switcher offers both languages…
            ->assertSee('lang=km', false)
            ->assertSee('lang=en', false);

        // …and defaults to the FIRST customer language (km), not internal (en)
        $response->assertSee('lang="km"', false);
    }

    public function test_storefront_honours_lang_param_only_when_offered(): void
    {
        $this->merchant->update(['settings' => [
            'locale'             => 'th',
            'installed_apps'     => ['commerce'],
            'customer_languages' => ['th', 'en'],
        ]]);

        $slug = $this->merchant->slug;

        $this->get(route('storefront.show', ['slug' => $slug, 'lang' => 'en'], absolute: false))
            ->assertOk()->assertSee('lang="en"', false);

        $this->get(route('storefront.show', ['slug' => $slug, 'lang' => 'th'], absolute: false))
            ->assertOk()->assertSee('lang="th"', false);

        // 'km' not offered → falls back to default customer language (th)
        $this->get(route('storefront.show', ['slug' => $slug, 'lang' => 'km'], absolute: false))
            ->assertOk()->assertSee('lang="th"', false);
    }

    public function test_existing_merchant_locale_maps_to_customer_default(): void
    {
        // No customer_languages configured — the merchant's internal locale
        // stays the customer-facing default; shipped languages are offered.
        $this->merchant->update(['settings' => [
            'locale'         => 'th',
            'installed_apps' => ['commerce'],
        ]]);

        $offered = $this->merchant->fresh()->customerLanguages();
        $this->assertSame('th', $offered[0]);
        $this->assertEqualsCanonicalizing(['th', 'en'], $offered);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()->assertSee('lang="th"', false);
    }

    public function test_thai_and_english_internal_languages_still_work(): void
    {
        foreach (['th', 'en'] as $locale) {
            $this->actingAs($this->user)
                ->put(route('settings.localization.update'), $this->payload(['locale' => $locale]))
                ->assertSessionHasNoErrors();

            $this->assertSame($locale, $this->merchant->fresh()->settings['locale']);
        }
    }

    public function test_preferences_endpoint_still_accepts_legacy_global_fields(): void
    {
        // Backwards compatibility: the old preferences contract keeps working.
        $this->actingAs($this->user)->put(route('settings.preferences.update'), [
            'currency'                    => 'USD',
            'timezone'                    => 'Asia/Bangkok',
            'date_format'                 => 'DD/MM/YYYY',
            'default_expiration_type'     => 'never',
            'default_birthday_enabled'    => 1,
            'locale'                      => 'en',
            'email_product_updates'       => 1,
            'email_tips'                  => 1,
            'email_feature_announcements' => 1,
            'country'                     => 'TH',
        ])->assertRedirect();

        $this->assertSame('USD', $this->merchant->fresh()->currency);
    }
}
