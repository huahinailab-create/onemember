<?php

namespace Tests\Feature\DevTools;

use App\Jobs\GenerateDemoDataJob;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DevQuickActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['devtools.enabled' => true]);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_quick_actions_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/quick-actions');
        $response->assertOk();
        $response->assertSee('Quick Actions');
    }

    public function test_create_demo_merchant_creates_merchant(): void
    {
        $initial = Merchant::count();
        $this->actingAs($this->user)->post('/dev/quick-actions/demo-merchant');
        $this->assertGreaterThan($initial, Merchant::count());
    }

    public function test_generate_members_dispatches_job(): void
    {
        Queue::fake();
        $merchant = Merchant::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->post('/dev/quick-actions/members', [
            'merchant_id' => $merchant->id,
            'count'       => 10,
        ]);

        Queue::assertPushed(GenerateDemoDataJob::class, fn ($job) => true);
    }

    public function test_generate_members_requires_valid_merchant(): void
    {
        $response = $this->actingAs($this->user)->post('/dev/quick-actions/members', [
            'merchant_id' => 99999,
            'count'       => 10,
        ]);
        $response->assertSessionHasErrors('merchant_id');
    }

    public function test_reset_demo_clears_members(): void
    {
        $merchant = Merchant::factory()->create(['user_id' => $this->user->id]);
        Member::factory()->count(5)->create(['merchant_id' => $merchant->id]);

        $this->assertEquals(5, $merchant->members()->count());

        $this->actingAs($this->user)->post('/dev/quick-actions/reset-demo', [
            'merchant_id' => $merchant->id,
        ]);

        $this->assertEquals(0, $merchant->members()->withTrashed()->count());
    }
}
