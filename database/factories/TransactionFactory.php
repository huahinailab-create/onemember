<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type          = fake()->randomElement(TransactionType::cases());
        $balanceBefore = fake()->numberBetween(0, 5000);
        $points        = $type->isCredit()
            ? fake()->numberBetween(10, 500)
            : -fake()->numberBetween(10, min($balanceBefore, 500));

        return [
            'merchant_id'        => Merchant::factory(),
            'member_id'          => Member::factory(),
            'loyalty_program_id' => LoyaltyProgram::factory(),
            'created_by'         => null,
            'type'               => $type,
            'points'             => $points,
            'balance_before'     => $balanceBefore,
            'balance_after'      => max(0, $balanceBefore + $points),
            'reference_type'     => null,
            'reference_id'       => null,
            'note'               => fake()->optional(0.3)->sentence(),
            'created_at'         => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function earn(): static
    {
        return $this->state(function (array $attrs) {
            $points = fake()->numberBetween(10, 500);
            return [
                'type'          => TransactionType::Earn,
                'points'        => $points,
                'balance_after' => $attrs['balance_before'] + $points,
            ];
        });
    }

    public function redeem(): static
    {
        return $this->state(function (array $attrs) {
            $points = fake()->numberBetween(10, min($attrs['balance_before'], 500));
            return [
                'type'          => TransactionType::Redeem,
                'points'        => -$points,
                'balance_after' => max(0, $attrs['balance_before'] - $points),
            ];
        });
    }
}
