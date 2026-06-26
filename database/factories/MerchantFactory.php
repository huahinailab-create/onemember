<?php

namespace Database\Factories;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Merchant>
 */
class MerchantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'user_id'  => User::factory(),
            'name'     => $name,
            'slug'     => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'email'    => fake()->unique()->companyEmail(),
            'phone'    => fake()->phoneNumber(),
            'address'  => fake()->address(),
            'status'   => MerchantStatus::Active,
            'currency' => 'THB',
            'settings' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => MerchantStatus::Inactive]);
    }

    public function suspended(): static
    {
        return $this->state(['status' => MerchantStatus::Suspended]);
    }
}
