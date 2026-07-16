<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CUSTOMER-001B — the customer address book: CRUD, archive, the
 * single-default invariant, country validation, search, and the ownership
 * boundary (customers only ever touch their own addresses).
 */
class CustomerAddressTest extends TestCase
{
    use RefreshDatabase;

    private function thaiPayload(array $overrides = []): array
    {
        return array_merge([
            'label'          => 'Home',
            'recipient_name' => 'Chelsea W.',
            'phone'          => '081 234 5678',
            'country'        => 'TH',
            'admin_area_1'   => 'Bangkok',
            'admin_area_2'   => 'Watthana',
            'admin_area_3'   => 'Khlong Toei Nuea',
            'postal_code'    => '10110',
            'line1'          => ' 99/1 Sukhumvit Rd ',
        ], $overrides);
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function test_customer_can_create_an_address_with_normalization(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->post(route('customer.addresses.store'), $this->thaiPayload())
            ->assertRedirect(route('customer.addresses.index', absolute: false));

        $address = $customer->addresses()->sole();
        $this->assertSame('99/1 Sukhumvit Rd', $address->line1);      // trimmed
        $this->assertSame('+66812345678', $address->phone);          // E.164
        $this->assertTrue($address->is_default);                     // first address = default
    }

    public function test_first_address_becomes_default_and_second_does_not(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->post(route('customer.addresses.store'), $this->thaiPayload());
        $this->actingAs($customer, 'customer')->post(route('customer.addresses.store'), $this->thaiPayload(['label' => 'Work']));

        $this->assertSame(1, $customer->addresses()->where('is_default', true)->count());
        $this->assertSame('Home', $customer->defaultAddress()->label);
    }

    public function test_multiple_addresses_can_be_saved(): void
    {
        $customer = Customer::factory()->account()->create();

        foreach (['Home', 'Work', 'Parents', 'Hotel'] as $label) {
            $this->actingAs($customer, 'customer')
                ->post(route('customer.addresses.store'), $this->thaiPayload(['label' => $label]));
        }

        $this->assertSame(4, $customer->addresses()->count());
        $this->assertSame(1, $customer->addresses()->where('is_default', true)->count());
    }

    // ── Country validation ────────────────────────────────────────────────

    public function test_thai_address_requires_province_district_subdistrict_postcode(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->post(route('customer.addresses.store'), $this->thaiPayload([
                'admin_area_1' => '', 'admin_area_3' => '', 'postal_code' => '',
            ]))
            ->assertSessionHasErrors(['admin_area_1', 'admin_area_3', 'postal_code']);
    }

    public function test_thai_postcode_must_be_five_digits(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->post(route('customer.addresses.store'), $this->thaiPayload(['postal_code' => '1234']))
            ->assertSessionHasErrors('postal_code');
    }

    public function test_myanmar_address_uses_its_own_schema(): void
    {
        $customer = Customer::factory()->account()->create(['country' => 'MM']);

        // Township (admin_area_3) + state/region (admin_area_1) required; ward allowed
        $this->actingAs($customer, 'customer')->post(route('customer.addresses.store'), [
            'label'          => 'Home',
            'recipient_name' => 'Aung Aung',
            'country'        => 'MM',
            'admin_area_1'   => 'Yangon Region',
            'admin_area_3'   => 'Kyauktada',
            'admin_area_4'   => 'Ward 5',
            'line1'          => '123 Maha Bandula Rd',
        ])->assertRedirect(route('customer.addresses.index', absolute: false));

        $this->assertSame('Ward 5', $customer->addresses()->sole()->admin_area_4);
    }

    public function test_thailand_rejects_myanmar_only_ward_field(): void
    {
        $customer = Customer::factory()->account()->create();

        // admin_area_4 is not part of the TH schema — prohibited
        $this->actingAs($customer, 'customer')
            ->post(route('customer.addresses.store'), $this->thaiPayload(['admin_area_4' => 'Ward 9']))
            ->assertSessionHasErrors('admin_area_4');
    }

    public function test_unsupported_country_is_rejected(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->post(route('customer.addresses.store'), $this->thaiPayload(['country' => 'DE']))
            ->assertSessionHasErrors('country');
    }

    // ── Edit / rename ─────────────────────────────────────────────────────

    public function test_customer_can_edit_and_rename_an_address(): void
    {
        $customer = Customer::factory()->account()->create();
        $address  = CustomerAddress::factory()->default()->for($customer)->create();

        $this->actingAs($customer, 'customer')
            ->put(route('customer.addresses.update', $address), $this->thaiPayload([
                'label' => 'Condo', 'line1' => '55 Thonglor 10',
            ]))
            ->assertRedirect(route('customer.addresses.index', absolute: false));

        $address->refresh();
        $this->assertSame('Condo', $address->label);
        $this->assertSame('55 Thonglor 10', $address->line1);
    }

    // ── Default handling ──────────────────────────────────────────────────

    public function test_set_default_demotes_the_previous_default(): void
    {
        $customer = Customer::factory()->account()->create();
        $first    = CustomerAddress::factory()->default()->for($customer)->create();
        $second   = CustomerAddress::factory()->for($customer)->create(['label' => 'Work']);

        $this->actingAs($customer, 'customer')
            ->post(route('customer.addresses.default', $second))
            ->assertRedirect(route('customer.addresses.index', absolute: false));

        $this->assertFalse($first->refresh()->is_default);
        $this->assertTrue($second->refresh()->is_default);
        $this->assertSame(1, $customer->addresses()->where('is_default', true)->count());
    }

    public function test_deleting_the_default_promotes_another_active_address(): void
    {
        $customer = Customer::factory()->account()->create();
        $default  = CustomerAddress::factory()->default()->for($customer)->create();
        $other    = CustomerAddress::factory()->for($customer)->create(['label' => 'Work']);

        $this->actingAs($customer, 'customer')->delete(route('customer.addresses.destroy', $default));

        $this->assertSoftDeleted($default);
        $this->assertTrue($other->refresh()->is_default);
    }

    public function test_deleting_the_last_address_leaves_no_default(): void
    {
        $customer = Customer::factory()->account()->create();
        $default  = CustomerAddress::factory()->default()->for($customer)->create();

        $this->actingAs($customer, 'customer')->delete(route('customer.addresses.destroy', $default));

        $this->assertNull($customer->defaultAddress());
    }

    // ── Archive / restore ─────────────────────────────────────────────────

    public function test_archiving_the_default_promotes_another_and_restore_works(): void
    {
        $customer = Customer::factory()->account()->create();
        $default  = CustomerAddress::factory()->default()->for($customer)->create();
        $other    = CustomerAddress::factory()->for($customer)->create(['label' => 'Work']);

        $this->actingAs($customer, 'customer')->post(route('customer.addresses.archive', $default));

        $default->refresh();
        $this->assertFalse($default->is_active);
        $this->assertFalse($default->is_default);
        $this->assertTrue($other->refresh()->is_default);

        $this->actingAs($customer, 'customer')->post(route('customer.addresses.restore', $default));
        $this->assertTrue($default->refresh()->is_active);
        $this->assertTrue($other->refresh()->is_default); // restore does not steal default
    }

    // ── Duplicate ─────────────────────────────────────────────────────────

    public function test_duplicating_copies_the_address_but_never_the_default_flag(): void
    {
        $customer = Customer::factory()->account()->create();
        $address  = CustomerAddress::factory()->default()->for($customer)->create();

        $this->actingAs($customer, 'customer')->post(route('customer.addresses.duplicate', $address));

        $this->assertSame(2, $customer->addresses()->count());
        $copy = $customer->addresses()->where('id', '!=', $address->id)->sole();
        $this->assertFalse($copy->is_default);
        $this->assertSame($address->line1, $copy->line1);
        $this->assertStringStartsWith($address->label, $copy->label);
    }

    // ── Search ────────────────────────────────────────────────────────────

    public function test_search_filters_the_address_book(): void
    {
        $customer = Customer::factory()->account()->create();
        CustomerAddress::factory()->for($customer)->create(['label' => 'Home', 'line1' => '99 Sukhumvit Rd']);
        CustomerAddress::factory()->for($customer)->create(['label' => 'Office', 'line1' => '1 Silom Rd']);

        $this->actingAs($customer, 'customer')
            ->get(route('customer.addresses.index', ['q' => 'Silom'], absolute: false))
            ->assertOk()
            ->assertSee('Office')
            ->assertDontSee('Sukhumvit');
    }

    // ── Authorization ─────────────────────────────────────────────────────

    public function test_guests_cannot_access_the_address_book(): void
    {
        $this->get(route('customer.addresses.index', absolute: false))
            ->assertRedirect(route('customer.login', absolute: false));
    }

    public function test_merchant_user_session_cannot_access_the_address_book(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user, 'web')
            ->get(route('customer.addresses.index', absolute: false))
            ->assertRedirect(route('customer.login', absolute: false));
    }

    public function test_customer_cannot_touch_another_customers_address(): void
    {
        $victim   = Customer::factory()->account()->create();
        $address  = CustomerAddress::factory()->default()->for($victim)->create();
        $attacker = Customer::factory()->account()->create();

        $this->actingAs($attacker, 'customer')
            ->get(route('customer.addresses.edit', $address, absolute: false))->assertNotFound();
        $this->actingAs($attacker, 'customer')
            ->put(route('customer.addresses.update', $address), $this->thaiPayload())->assertNotFound();
        $this->actingAs($attacker, 'customer')
            ->delete(route('customer.addresses.destroy', $address))->assertNotFound();
        $this->actingAs($attacker, 'customer')
            ->post(route('customer.addresses.default', $address))->assertNotFound();

        $this->assertNull($address->refresh()->deleted_at);
    }
}
