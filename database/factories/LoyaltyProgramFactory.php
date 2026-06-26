<?php

namespace Database\Factories;

use App\Enums\LoyaltyProgramType;
use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyProgram>
 */
class LoyaltyProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'merchant_id'     => Merchant::factory(),
            'name'            => fake()->randomElement(['Points Club', 'Stamp Card', 'VIP Rewards', 'Cashback Program']),
            'type'            => fake()->randomElement(LoyaltyProgramType::cases()),
            'description'     => fake()->sentence(),
            'points_per_unit' => fake()->randomElement([0.5, 1.0, 1.5, 2.0, 5.0]),
            'is_active'       => true,
            'starts_at'       => null,
            'ends_at'         => null,
            'settings'        => null,
        ];
    }

    public function points(): static
    {
        return $this->state(['type' => LoyaltyProgramType::Points]);
    }

    public function stamps(): static
    {
        return $this->state(['type' => LoyaltyProgramType::Stamps]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
