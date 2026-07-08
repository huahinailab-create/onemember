<?php

namespace Tests\Feature;

use App\Models\Merchant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * OMEGA-001D — presentation-only title case. Never touches the stored
 * `name` value; only normalizes all-lowercase or ALL-CAPS input so
 * mixed-case names typed deliberately (legal suffixes, connector words)
 * are never mangled.
 */
class MerchantDisplayNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_lowercase_name_is_title_cased(): void
    {
        $merchant = Merchant::factory()->make(['name' => "mike's coffee"]);

        $this->assertSame("Mike's Coffee", $merchant->displayName());
        $this->assertSame("mike's coffee", $merchant->name);
    }

    public function test_uppercase_name_is_title_cased(): void
    {
        $merchant = Merchant::factory()->make(['name' => "MIKE'S COFFEE"]);

        $this->assertSame("Mike's Coffee", $merchant->displayName());
    }

    public function test_mixed_case_name_is_left_untouched(): void
    {
        $merchant = Merchant::factory()->make(['name' => 'Wilkinson LLC']);

        $this->assertSame('Wilkinson LLC', $merchant->displayName());
    }

    public function test_mixed_case_name_with_connector_word_is_left_untouched(): void
    {
        $merchant = Merchant::factory()->make(['name' => 'Aufderhar and Sons']);

        $this->assertSame('Aufderhar and Sons', $merchant->displayName());
    }

    public function test_initials_uses_up_to_two_words(): void
    {
        $this->assertSame('MC', Merchant::factory()->make(['name' => "Mike's Coffee"])->initials());
        $this->assertSame('CC', Merchant::factory()->make(['name' => 'Chelsea Café Bar'])->initials());
        $this->assertSame('S', Merchant::factory()->make(['name' => 'Starbucks'])->initials());
    }
}
