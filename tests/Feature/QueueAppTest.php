<?php

namespace Tests\Feature;

use App\Apps\Queue\Models\QueueCounter;
use App\Apps\Queue\Models\QueueTicket;
use App\Apps\Queue\QueueService;
use App\Events\Domain\QueueTicketCreated;
use App\Marketplace\AppManager;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/** PLATFORM-002 Part 8 — Queue App foundation. */
class QueueAppTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
        ]);
        app(AppManager::class)->install($this->merchant, 'queue');
        $this->user->refresh();
    }

    public function test_queue_routes_403_without_app(): void
    {
        app(AppManager::class)->uninstall($this->merchant->fresh(), 'queue');

        $this->actingAs($this->user->fresh())
            ->get(route('queue.tickets.index', absolute: false))
            ->assertForbidden();
    }

    public function test_board_loads_and_ticket_can_be_issued_with_daily_numbering(): void
    {
        $this->actingAs($this->user)
            ->get(route('queue.tickets.index', absolute: false))
            ->assertOk();

        $this->actingAs($this->user)->post(route('queue.tickets.store'), [
            'customer_name' => 'Somchai', 'type' => 'walk_in',
        ])->assertRedirect(route('queue.tickets.index', absolute: false));

        $this->actingAs($this->user)->post(route('queue.tickets.store'), ['type' => 'walk_in']);

        $this->assertSame([1, 2], QueueTicket::orderBy('id')->pluck('number')->all());
    }

    public function test_ticket_creation_emits_domain_event(): void
    {
        Event::fake([QueueTicketCreated::class]);

        app(QueueService::class)->issueTicket($this->merchant);

        Event::assertDispatched(QueueTicketCreated::class, fn ($e) => $e->name() === 'queue.ticket_created');
    }

    public function test_priority_tickets_lead_the_waiting_line(): void
    {
        $service = app(QueueService::class);
        $normal   = $service->issueTicket($this->merchant);
        $priority = $service->issueTicket($this->merchant, ['priority' => true]);

        $line = QueueTicket::where('merchant_id', $this->merchant->id)->waitingLine()->pluck('id')->all();
        $this->assertSame([$priority->id, $normal->id], $line);

        // Estimated wait: priority ticket has nobody ahead; normal has one.
        $this->assertSame(0, $service->estimatedWaitMinutes($this->merchant, $priority));
        $this->assertSame(5, $service->estimatedWaitMinutes($this->merchant, $normal));
    }

    public function test_status_machine_enforced(): void
    {
        $ticket = app(QueueService::class)->issueTicket($this->merchant);

        // waiting → done is not allowed
        $this->actingAs($this->user)->put(route('queue.tickets.status', $ticket), ['status' => 'done'])
            ->assertSessionHasErrors(['status']);

        // waiting → called → serving → done is
        $counter = QueueCounter::create(['merchant_id' => $this->merchant->id, 'name' => 'Counter 1']);
        $this->actingAs($this->user)->put(route('queue.tickets.status', $ticket), [
            'status' => 'called', 'queue_counter_id' => $counter->id,
        ]);
        $this->assertSame('called', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->called_at);

        $this->actingAs($this->user)->put(route('queue.tickets.status', $ticket), ['status' => 'serving']);
        $this->actingAs($this->user)->put(route('queue.tickets.status', $ticket), ['status' => 'done']);
        $this->assertSame('done', $ticket->fresh()->status);
    }

    public function test_tickets_are_tenant_scoped(): void
    {
        $otherOwner = User::factory()->create(['email_verified_at' => now()]);
        $other = Merchant::factory()->create(['user_id' => $otherOwner->id]);
        $foreign = QueueTicket::create([
            'merchant_id' => $other->id, 'number' => 1, 'type' => 'walk_in', 'status' => 'waiting',
        ]);

        $this->actingAs($this->user)
            ->put(route('queue.tickets.status', $foreign), ['status' => 'called'])
            ->assertForbidden();
    }

    public function test_reservation_requires_time_and_display_renders(): void
    {
        $this->actingAs($this->user)->post(route('queue.tickets.store'), ['type' => 'reservation'])
            ->assertSessionHasErrors(['reserved_for']);

        $this->actingAs($this->user)->get(route('queue.display', absolute: false))->assertOk();
    }
}
