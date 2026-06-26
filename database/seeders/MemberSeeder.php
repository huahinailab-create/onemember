<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {
            // 20 members per merchant
            Member::factory(20)->create(['merchant_id' => $merchant->id]);

            // One member with birthday today for testing birthday rewards
            Member::factory()->birthdayToday()->create([
                'merchant_id' => $merchant->id,
                'name'        => 'Birthday Test Member',
                'email'       => "birthday.{$merchant->id}@test.com",
            ]);
        }
    }
}
