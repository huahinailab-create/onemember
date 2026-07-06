<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->name(),
            'phone'       => '08' . fake()->unique()->numerify('########'),
            'email'       => fake()->unique()->safeEmail(),
            'birthday'    => fake()->date('Y-m-d', '-18 years'),
            'postal_code' => fake()->numerify('#####'),
            'locale'      => 'th',
        ];
    }
}
