<?php

namespace Database\Factories;

use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'merchant_id'      => Merchant::factory(),
            'name'             => fake()->name(),
            'email'            => fake()->unique()->safeEmail(),
            'phone'            => fake()->phoneNumber(),
            'member_code'      => strtoupper(Str::random(10)),
            'birthday'         => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'status'           => MemberStatus::Active,
            'total_points'     => fake()->numberBetween(0, 5000),
            'lifetime_points'  => fn (array $attrs) => $attrs['total_points'] + fake()->numberBetween(0, 10000),
            'joined_at'        => fake()->dateTimeBetween('-2 years', 'now'),
            'last_activity_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => MemberStatus::Inactive]);
    }

    public function blocked(): static
    {
        return $this->state(['status' => MemberStatus::Blocked]);
    }

    public function birthdayToday(): static
    {
        return $this->state([
            'birthday' => now()->format('Y-m-d'),
        ]);
    }
}
