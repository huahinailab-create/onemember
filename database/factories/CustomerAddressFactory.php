<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** CUSTOMER-001B — Thai address by default; myanmar() state for MM shape. */
class CustomerAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id'    => Customer::factory(),
            'label'          => 'Home',
            'recipient_name' => fake()->name(),
            'phone'          => '+668'.fake()->numerify('########'),
            'country'        => 'TH',
            'admin_area_1'   => 'Bangkok',
            'admin_area_2'   => 'Watthana',
            'admin_area_3'   => 'Khlong Toei Nuea',
            'postal_code'    => '10110',
            'line1'          => fake()->buildingNumber().' Sukhumvit Rd',
            'is_default'     => false,
            'is_active'      => true,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }

    public function archived(): static
    {
        return $this->state(['is_active' => false, 'is_default' => false]);
    }

    public function myanmar(): static
    {
        return $this->state([
            'country'      => 'MM',
            'admin_area_1' => 'Yangon Region',
            'admin_area_2' => 'Yangon West',
            'admin_area_3' => 'Kyauktada',
            'admin_area_4' => 'Ward 5',
            'postal_code'  => '11182',
            'line1'        => '123 Maha Bandula Rd',
        ]);
    }
}
