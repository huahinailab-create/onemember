<?php

namespace App\Services\DevTools;

use App\Enums\MemberStatus;
use App\Enums\TransactionType;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DevDemoService
{
    public function createDemoMerchant(): Merchant
    {
        $user = \App\Models\User::factory()->create([
            'name'              => 'Demo Merchant ' . rand(100, 999),
            'email'             => 'demo' . rand(1000, 9999) . '@example.com',
            'email_verified_at' => now(),
        ]);

        return \App\Models\Merchant::factory()->create([
            'user_id' => $user->id,
            'name'    => 'Demo Business ' . rand(100, 999),
        ]);
    }

    public function generateMembers(Merchant $merchant, int $count): void
    {
        Member::factory()->count($count)->create(['merchant_id' => $merchant->id]);
    }

    public function generateBirthdayMembers(Merchant $merchant, int $count): void
    {
        Member::factory()->count($count)->create([
            'merchant_id' => $merchant->id,
            'birthday'    => now()->format('Y-m-d'),
        ]);
    }

    public function generatePurchases(Merchant $merchant, int $count): void
    {
        $members = $merchant->members()->inRandomOrder()->limit(max(1, $count))->get();
        if ($members->isEmpty()) {
            $members = Member::factory()->count(5)->create(['merchant_id' => $merchant->id]);
        }
        $loyaltyProgram = $this->ensureLoyaltyProgram($merchant);

        foreach (range(1, $count) as $_) {
            $member        = $members->random();
            $amount        = fake()->randomFloat(2, 50, 5000);
            $points        = (int) ($amount / 100);
            $balanceBefore = $member->total_points;

            Transaction::create([
                'merchant_id'        => $merchant->id,
                'member_id'          => $member->id,
                'loyalty_program_id' => $loyaltyProgram?->id,
                'type'               => TransactionType::Earn,
                'points'             => $points,
                'balance_before'     => $balanceBefore,
                'balance_after'      => $balanceBefore + $points,
                'note'               => 'Demo purchase',
                'created_at'         => fake()->dateTimeBetween('-90 days', 'now'),
            ]);

            $member->increment('total_points', $points);
            $member->increment('lifetime_points', $points);
        }
    }

    public function generateLoyaltyPoints(Merchant $merchant, int $count): void
    {
        $members = $merchant->members()->inRandomOrder()->limit(max(1, $count))->get();
        if ($members->isEmpty()) {
            $members = Member::factory()->count(5)->create(['merchant_id' => $merchant->id]);
        }
        $loyaltyProgram = $this->ensureLoyaltyProgram($merchant);

        foreach (range(1, $count) as $_) {
            $member        = $members->random();
            $points        = fake()->numberBetween(10, 500);
            $balanceBefore = $member->total_points;

            Transaction::create([
                'merchant_id'        => $merchant->id,
                'member_id'          => $member->id,
                'loyalty_program_id' => $loyaltyProgram?->id,
                'type'               => TransactionType::Earn,
                'points'             => $points,
                'balance_before'     => $balanceBefore,
                'balance_after'      => $balanceBefore + $points,
                'note'               => 'Demo loyalty points',
                'created_at'         => fake()->dateTimeBetween('-90 days', 'now'),
            ]);

            $member->increment('total_points', $points);
            $member->increment('lifetime_points', $points);
        }
    }

    public function generateStampTransactions(Merchant $merchant, int $count): void
    {
        $members = $merchant->members()->inRandomOrder()->limit(max(1, $count))->get();
        if ($members->isEmpty()) {
            $members = Member::factory()->count(5)->create(['merchant_id' => $merchant->id]);
        }

        $loyaltyProgram = $this->ensureLoyaltyProgram($merchant);

        foreach (range(1, $count) as $_) {
            $member = $members->random();
            Transaction::create([
                'merchant_id'        => $merchant->id,
                'member_id'          => $member->id,
                'loyalty_program_id' => $loyaltyProgram->id,
                'type'               => TransactionType::Earn,
                'points'             => 1,
                'balance_before'     => $member->total_points,
                'balance_after'      => $member->total_points,
                'note'               => 'Demo stamp',
                'created_at'         => fake()->dateTimeBetween('-90 days', 'now'),
            ]);
        }
    }

    public function generateRedemptions(Merchant $merchant, int $count): void
    {
        $members = $merchant->members()->where('total_points', '>', 100)->inRandomOrder()->limit(max(1, $count))->get();
        if ($members->isEmpty()) {
            return;
        }
        $reward = $merchant->rewards()->first();
        if (! $reward) {
            return;
        }
        $loyaltyProgram = $this->ensureLoyaltyProgram($merchant);

        foreach ($members->take($count) as $member) {
            $points        = min($member->total_points, fake()->numberBetween(50, 200));
            $balanceBefore = $member->total_points;

            \App\Models\Redemption::create([
                'merchant_id' => $merchant->id,
                'member_id'   => $member->id,
                'reward_id'   => $reward->id,
                'points_used' => $points,
                'note'        => 'Demo redemption',
                'created_at'  => fake()->dateTimeBetween('-90 days', 'now'),
            ]);

            Transaction::create([
                'merchant_id'        => $merchant->id,
                'member_id'          => $member->id,
                'loyalty_program_id' => $loyaltyProgram?->id,
                'type'               => TransactionType::Redeem,
                'points'             => -$points,
                'balance_before'     => $balanceBefore,
                'balance_after'      => max(0, $balanceBefore - $points),
                'note'               => 'Demo redemption',
                'created_at'         => fake()->dateTimeBetween('-90 days', 'now'),
            ]);

            $member->decrement('total_points', $points);
        }
    }

    public function generateNotifications(Merchant $merchant, int $count): void
    {
        if (! DB::getSchemaBuilder()->hasTable('notifications')) {
            return;
        }

        $members = $merchant->members()->inRandomOrder()->limit(max(1, $count))->get();
        foreach ($members as $member) {
            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\DemoNotification',
                'notifiable_type' => Member::class,
                'notifiable_id'   => $member->id,
                'data'            => json_encode(['message' => 'Demo notification for dev tools']),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    public function resetDemoEnvironment(Merchant $merchant): void
    {
        DB::transaction(function () use ($merchant) {
            $memberIds = $merchant->members()->withTrashed()->pluck('id');

            if ($memberIds->isNotEmpty()) {
                DB::table('transactions')->whereIn('member_id', $memberIds)->delete();
                DB::table('redemptions')->whereIn('member_id', $memberIds)->delete();
                // notifications table may not exist in all environments
                if (DB::getSchemaBuilder()->hasTable('notifications')) {
                    DB::table('notifications')->where('notifiable_type', Member::class)
                        ->whereIn('notifiable_id', $memberIds)->delete();
                }
            }

            $merchant->members()->withTrashed()->forceDelete();
            $merchant->rewards()->withTrashed()->forceDelete();
            DB::table('failed_jobs')->delete();
            DB::table('jobs')->delete();
        });
    }

    private function ensureLoyaltyProgram(Merchant $merchant): \App\Models\LoyaltyProgram
    {
        return $merchant->loyaltyPrograms()->first()
            ?? \App\Models\LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id]);
    }

    public function getStats(): array
    {
        return [
            'merchants'   => \App\Models\Merchant::count(),
            'members'     => Member::count(),
            'transactions'=> Transaction::count(),
            'rewards'     => \App\Models\Reward::count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'pending_jobs'=> DB::table('jobs')->count(),
        ];
    }
}
