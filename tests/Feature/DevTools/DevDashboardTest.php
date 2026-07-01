<?php

namespace Tests\Feature\DevTools;

use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['devtools.enabled' => true]);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_dashboard_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev');
        $response->assertOk();
        $response->assertSee('Developer Dashboard');
    }

    public function test_dashboard_shows_stats(): void
    {
        $merchant = Merchant::factory()->create(['user_id' => $this->user->id]);
        Member::factory()->count(5)->create(['merchant_id' => $merchant->id]);

        $response = $this->actingAs($this->user)->get('/dev');
        $response->assertOk();
        $response->assertSee('Merchants');
        $response->assertSee('Members');
    }
}
