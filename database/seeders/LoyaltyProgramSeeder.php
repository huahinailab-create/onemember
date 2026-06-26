<?php

namespace Database\Seeders;

use App\Enums\LoyaltyProgramType;
use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use Illuminate\Database\Seeder;

class LoyaltyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {
            LoyaltyProgram::create([
                'merchant_id'     => $merchant->id,
                'name'            => 'Points Rewards',
                'type'            => LoyaltyProgramType::Points,
                'description'     => 'Earn 1 point for every 1 THB spent.',
                'points_per_unit' => 1.0,
                'is_active'       => true,
            ]);

            LoyaltyProgram::create([
                'merchant_id'     => $merchant->id,
                'name'            => 'Stamp Card',
                'type'            => LoyaltyProgramType::Stamps,
                'description'     => 'Collect 10 stamps to get a free item.',
                'points_per_unit' => 1.0,
                'is_active'       => true,
            ]);
        }
    }
}
