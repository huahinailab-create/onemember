<?php

namespace Tests\Feature\DevTools;

use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use App\Services\DevTools\DevDemoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevDemoServiceTest extends TestCase
{
    use RefreshDatabase;

    private DevDemoService $service;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service  = app(DevDemoService::class);
        $this->merchant = Merchant::factory()->create();
    }

    public function test_create_demo_merchant_creates_user_and_merchant(): void
    {
        $initialUsers     = User::count();
        $initialMerchants = Merchant::count();

        $merchant = $this->service->createDemoMerchant();

        $this->assertGreaterThan($initialUsers, User::count());
        $this->assertGreaterThan($initialMerchants, Merchant::count());
        $this->assertInstanceOf(Merchant::class, $merchant);
    }

    public function test_generate_members_creates_correct_count(): void
    {
        $this->service->generateMembers($this->merchant, 5);
        $this->assertEquals(5, $this->merchant->members()->count());
    }

    public function test_generate_birthday_members_sets_today_birthday(): void
    {
        $this->service->generateBirthdayMembers($this->merchant, 3);
        $count = $this->merchant->members()
            ->whereDate('birthday', today())
            ->count();
        $this->assertEquals(3, $count);
    }

    public function test_generate_purchases_creates_transactions(): void
    {
        Member::factory()->count(3)->create(['merchant_id' => $this->merchant->id]);
        $this->service->generatePurchases($this->merchant, 5);
        $this->assertEquals(5, $this->merchant->transactions()->count());
    }

    public function test_reset_demo_environment_deletes_all_member_data(): void
    {
        Member::factory()->count(10)->create(['merchant_id' => $this->merchant->id]);
        $this->assertEquals(10, $this->merchant->members()->count());

        $this->service->resetDemoEnvironment($this->merchant);

        $this->assertEquals(0, $this->merchant->members()->withTrashed()->count());
    }

    public function test_get_stats_returns_expected_keys(): void
    {
        $stats = $this->service->getStats();

        $this->assertArrayHasKey('merchants', $stats);
        $this->assertArrayHasKey('members', $stats);
        $this->assertArrayHasKey('transactions', $stats);
        $this->assertArrayHasKey('rewards', $stats);
        $this->assertArrayHasKey('failed_jobs', $stats);
        $this->assertArrayHasKey('pending_jobs', $stats);
    }
}
