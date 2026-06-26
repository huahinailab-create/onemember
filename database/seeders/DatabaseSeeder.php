<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user — owner of all demo merchants
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@onemember.test',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            MerchantSeeder::class,
            LoyaltyProgramSeeder::class,
            RewardSeeder::class,
            BirthdayRewardSeeder::class,
            MemberSeeder::class,
            // TransactionSeeder and RedemptionSeeder are intentionally omitted from
            // the base seed — transactions require coordinated balance tracking.
            // Generate them in tests using factories directly.
        ]);
    }
}
