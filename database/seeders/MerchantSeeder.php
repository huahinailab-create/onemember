<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'admin@onemember.test')->first();

        Merchant::create([
            'user_id'  => $owner->id,
            'name'     => 'Brew & Bloom Coffee',
            'slug'     => 'brew-bloom-coffee',
            'email'    => 'hello@brewbloom.test',
            'phone'    => '+66 2 123 4567',
            'address'  => '123 Sukhumvit Rd, Bangkok 10110',
            'currency' => 'THB',
        ]);

        Merchant::create([
            'user_id'  => $owner->id,
            'name'     => 'Urban Fitness Club',
            'slug'     => 'urban-fitness-club',
            'email'    => 'info@urbanfitness.test',
            'phone'    => '+66 2 987 6543',
            'address'  => '456 Silom Rd, Bangkok 10500',
            'currency' => 'THB',
        ]);

        // Extra random merchants for development
        Merchant::factory(3)->create(['user_id' => $owner->id]);
    }
}
