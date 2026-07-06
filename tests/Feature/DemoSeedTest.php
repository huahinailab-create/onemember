<?php

namespace Tests\Feature;

use App\Console\Commands\SeedDemoData;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_creates_a_complete_sample_merchant(): void
    {
        $this->artisan('onemember:demo-seed')->assertSuccessful();

        $user = User::where('email', SeedDemoData::DEMO_EMAIL)->firstOrFail();
        $merchant = Merchant::where('user_id', $user->id)->firstOrFail();

        $this->assertNotNull($merchant->onboarding_completed_at);
        $this->assertTrue($merchant->hasApp('commerce'));
        $this->assertTrue($merchant->loyaltyPrograms()->exists());
        $this->assertSame(8, $merchant->members()->count());
        $this->assertSame(4, \App\Models\Product::where('merchant_id', $merchant->id)->count());
        $this->assertSame(3, Order::where('merchant_id', $merchant->id)->count());

        // Storefront actually works with the seeded data
        $this->get(route('storefront.show', $merchant->slug, absolute: false))
            ->assertOk()->assertSee('Signature Latte');
    }

    public function test_demo_seed_is_idempotent_without_fresh_flag(): void
    {
        $this->artisan('onemember:demo-seed')->assertSuccessful();
        $this->artisan('onemember:demo-seed')->assertSuccessful();

        $this->assertSame(1, User::where('email', SeedDemoData::DEMO_EMAIL)->count());
    }

    public function test_demo_seed_refuses_to_run_in_production(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $this->artisan('onemember:demo-seed')->assertFailed();

        app()->detectEnvironment(fn () => 'testing');
        $this->assertSame(0, User::where('email', SeedDemoData::DEMO_EMAIL)->count());
    }
}
