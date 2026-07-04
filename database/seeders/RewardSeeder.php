<?php

namespace Database\Seeders;

use App\Enums\RewardType;
use App\Models\LoyaltyProgram;
use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $programs = LoyaltyProgram::all();

        foreach ($programs as $program) {
            $rewards = [
                [
                    'name'            => '10% Discount Voucher',
                    'type'            => RewardType::DiscountPercentage,
                    'points_required' => 200,
                    'value'           => 10.00,
                    'description'     => 'Get 10% off your next purchase.',
                ],
                [
                    'name'            => 'Free Item',
                    'type'            => RewardType::FreeItem,
                    'points_required' => 500,
                    'value'           => 100.00,
                    'description'     => 'Redeem for one free item up to 100 THB.',
                ],
                [
                    'name'            => 'Gift Voucher 200 THB',
                    'type'            => RewardType::Voucher,
                    'points_required' => 1000,
                    'value'           => 200.00,
                    'description'     => '200 THB gift voucher.',
                ],
            ];

            foreach ($rewards as $data) {
                Reward::create(array_merge($data, [
                    'merchant_id'        => $program->merchant_id,
                    'loyalty_program_id' => $program->id,
                ]));
            }
        }
    }
}
