<?php

namespace Database\Seeders;

use App\Enums\BirthdayRewardType;
use App\Models\BirthdayReward;
use App\Models\LoyaltyProgram;
use Illuminate\Database\Seeder;

class BirthdayRewardSeeder extends Seeder
{
    public function run(): void
    {
        $programs = LoyaltyProgram::all();

        foreach ($programs as $program) {
            BirthdayReward::create([
                'merchant_id'        => $program->merchant_id,
                'loyalty_program_id' => $program->id,
                'name'               => 'Birthday Bonus Points',
                'type'               => BirthdayRewardType::Points,
                'value'              => 200,                // 200 bonus points
                'valid_days_before'  => 0,
                'valid_days_after'   => 7,
                'is_active'          => true,
            ]);
        }
    }
}
