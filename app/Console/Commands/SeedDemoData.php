<?php

namespace App\Console\Commands;

use App\Enums\LoyaltyProgramType;
use App\Enums\RewardType;
use App\Enums\TransactionType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * BETA-005 — founder demo seed. Creates one complete sample merchant
 * (campaign, rewards, members with history, Commerce products, orders)
 * so a live demo never starts from an empty screen.
 *
 * Local/staging ONLY. Refuses to run in production, never runs
 * automatically, and is idempotent via the fixed demo email.
 */
class SeedDemoData extends Command
{
    protected $signature = 'onemember:demo-seed {--fresh : Delete the existing demo merchant first}';

    protected $description = 'Seed a complete sample merchant for founder demos (local/staging only)';

    public const DEMO_EMAIL = 'demo@onemember.co';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Refusing to seed demo data in production.');

            return self::FAILURE;
        }

        $existing = User::where('email', self::DEMO_EMAIL)->first();
        if ($existing && ! $this->option('fresh')) {
            $this->warn('Demo merchant already exists (' . self::DEMO_EMAIL . '). Use --fresh to recreate.');

            return self::SUCCESS;
        }
        if ($existing) {
            $existing->merchant?->delete();
            $existing->delete();
            $this->line('Removed previous demo merchant.');
        }

        $password = Str::password(16, symbols: false);

        $user = User::create([
            'name'              => 'Demo Founder',
            'email'             => self::DEMO_EMAIL,
            'password'          => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'name'                    => 'Chelsea Café',
            'slug'                    => 'chelsea-cafe-demo',
            'business_type'           => 'Restaurant & Café',
            'onboarding_completed_at' => now(),
            'trial_ends_at'           => now()->addDays(14),
            'settings'                => [
                'currency'       => 'THB',
                'timezone'       => 'Asia/Bangkok',
                'country'        => 'TH',
                'locale'         => 'en',
                'installed_apps' => ['commerce'],
                'commerce'       => [
                    'pickup_enabled'       => true,
                    'payment_instructions' => 'Scan our PromptPay QR at the counter.',
                ],
            ],
        ]);

        $campaign = LoyaltyProgram::factory()->create([
            'merchant_id'     => $merchant->id,
            'name'            => 'Chelsea Points Club',
            'type'            => LoyaltyProgramType::Points,
            'points_per_unit' => 1.0,
            'is_active'       => true,
        ]);

        $rewards = collect([
            ['name' => 'Free Latte',        'points_required' => 100],
            ['name' => 'Croissant on Us',   'points_required' => 250],
            ['name' => '15% Off Your Bill', 'points_required' => 500],
        ])->map(fn (array $r) => Reward::factory()->create($r + [
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $campaign->id,
            'type'               => RewardType::cases()[0],
            'quantity_available' => null,
            'is_active'          => true,
            'valid_until'        => null,
        ]));

        $members = Member::factory()->count(8)->create([
            'merchant_id' => $merchant->id,
            'status'      => 'active',
        ]);

        foreach ($members->take(5) as $member) {
            Transaction::factory()->create([
                'merchant_id'        => $merchant->id,
                'member_id'          => $member->id,
                'loyalty_program_id' => $campaign->id,
                'type'               => TransactionType::cases()[0],
                'points'             => 120,
                'balance_before'     => 0,
                'balance_after'      => 120,
            ]);
        }

        $products = collect([
            ['name' => 'Signature Latte', 'price' => 75],
            ['name' => 'Butter Croissant', 'price' => 55],
            ['name' => 'Iced Matcha',      'price' => 85],
            ['name' => 'Cheesecake Slice', 'price' => 120],
        ])->map(fn (array $p) => Product::factory()->create($p + [
            'merchant_id' => $merchant->id,
            'status'      => 'active',
        ]));

        foreach ([['placed', 'unpaid'], ['accepted', 'paid'], ['completed', 'paid']] as $i => [$status, $paid]) {
            $product = $products[$i];
            $order = Order::create([
                'merchant_id'      => $merchant->id,
                'customer_name'    => ['Anna Walker', 'Ben Carter', 'Mia Torres'][$i],
                'customer_phone'   => '08000000' . ($i + 1) . '0',
                'fulfillment_type' => 'pickup',
                'status'           => $status,
                'payment_status'   => $paid,
                'subtotal'         => $product->price * 2,
                'fulfillment_fee'  => 0,
                'total'            => $product->price * 2,
            ]);
            $order->items()->create([
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->price,
                'qty'        => 2,
            ]);
        }

        $this->info('Demo merchant seeded.');
        $this->table(['Login', 'Password', 'Storefront'], [[
            self::DEMO_EMAIL, $password, route('storefront.show', $merchant->slug),
        ]]);
        $this->line('Campaign: ' . $campaign->name . ' · Rewards: ' . $rewards->count()
            . ' · Members: ' . $members->count() . ' · Products: ' . $products->count() . ' · Orders: 3');

        return self::SUCCESS;
    }
}
