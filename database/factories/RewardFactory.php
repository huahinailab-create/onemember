<?php

namespace Database\Factories;

use App\Enums\RewardType;
use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use App\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reward>
 */
class RewardFactory extends Factory
{
    public function definition(): array
    {
        $program = LoyaltyProgram::factory()->create();

        return [
            'merchant_id'        => $program->merchant_id,
            'loyalty_program_id' => $program->id,
            'name'               => fake()->randomElement(['Free Coffee', '10% Discount', 'Birthday Gift', 'Free Delivery', 'Movie Ticket']),
            'description'        => fake()->sentence(),
            'type'               => fake()->randomElement(RewardType::cases()),
            'points_required'    => fake()->randomElement([100, 250, 500, 1000, 2000]),
            'value'              => fake()->randomFloat(2, 10, 500),
            'quantity_available' => fake()->optional(0.5)->numberBetween(10, 100),
            'quantity_redeemed'  => 0,
            'is_active'          => true,
            'valid_from'         => null,
            'valid_until'        => fake()->optional(0.3)->dateTimeBetween('+1 month', '+1 year')?->format('Y-m-d'),
        ];
    }

    public function discount(): static
    {
        return $this->state(['type' => RewardType::DiscountPercentage]);
    }

    public function freeItem(): static
    {
        return $this->state(['type' => RewardType::FreeItem]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
