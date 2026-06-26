<?php

namespace Database\Factories;

use App\Enums\RedemptionStatus;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Redemption;
use App\Models\Reward;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Redemption>
 */
class RedemptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'merchant_id'    => Merchant::factory(),
            'member_id'      => Member::factory(),
            'reward_id'      => Reward::factory(),
            'transaction_id' => Transaction::factory(),
            'used_by'        => null,
            'code'           => strtoupper(Str::random(8)),
            'status'         => RedemptionStatus::Pending,
            'points_used'    => fake()->numberBetween(100, 2000),
            'redeemed_at'    => null,
            'expires_at'     => fake()->dateTimeBetween('+1 day', '+30 days'),
        ];
    }

    public function used(): static
    {
        return $this->state([
            'status'      => RedemptionStatus::Used,
            'redeemed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status'     => RedemptionStatus::Expired,
            'expires_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
