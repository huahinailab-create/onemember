<?php

namespace Tests\Feature;

use App\Automation\ActionRegistry;
use App\Automation\ConditionEvaluator;
use App\Jobs\RunAutomationAction;
use App\Models\AutomationRule;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/** PLATFORM-002 Part 6 — automation engine (triggers, conditions, actions). */
class AutomationEngineTest extends TestCase
{
    use RefreshDatabase;

    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create(['user_id' => $user->id]);
    }

    private function rule(array $overrides = []): AutomationRule
    {
        return AutomationRule::create(array_merge([
            'merchant_id'   => $this->merchant->id,
            'name'          => 'Welcome log',
            'trigger_event' => 'member.created',
            'conditions'    => [],
            'actions'       => [['type' => 'log', 'params' => ['message' => 'hi']]],
            'enabled'       => true,
        ], $overrides));
    }

    public function test_matching_event_queues_the_rule_actions(): void
    {
        Queue::fake();
        $rule = $this->rule();

        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        Queue::assertPushed(RunAutomationAction::class, fn ($job) => $job->rule->id === $rule->id);
    }

    public function test_disabled_rule_and_other_events_do_not_fire(): void
    {
        Queue::fake();
        $this->rule(['enabled' => false]);
        $this->rule(['trigger_event' => 'order.placed', 'name' => 'wrong trigger']);

        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        Queue::assertNotPushed(RunAutomationAction::class);
    }

    public function test_rules_are_tenant_scoped(): void
    {
        Queue::fake();
        $otherOwner = User::factory()->create();
        $other      = Merchant::factory()->create(['user_id' => $otherOwner->id]);
        $this->rule(['merchant_id' => $other->id]);

        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        Queue::assertNotPushed(RunAutomationAction::class);
    }

    public function test_conditions_gate_execution(): void
    {
        Queue::fake();
        $this->rule(['conditions' => [['field' => 'name', 'operator' => 'contains', 'value' => 'somchai']]]);

        Member::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Arisa']);
        Queue::assertNotPushed(RunAutomationAction::class);

        Member::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Somchai P.']);
        Queue::assertPushed(RunAutomationAction::class, 1);
    }

    public function test_condition_evaluator_operators(): void
    {
        $e = new ConditionEvaluator();

        $this->assertTrue($e->matches([['field' => 'points', 'operator' => 'gte', 'value' => 10]], ['points' => 10]));
        $this->assertFalse($e->matches([['field' => 'points', 'operator' => 'gt', 'value' => 10]], ['points' => 10]));
        $this->assertTrue($e->matches([['field' => 'status', 'operator' => 'not_equals', 'value' => 'cancelled']], ['status' => 'placed']));
        $this->assertTrue($e->matches([['field' => 'order_id', 'operator' => 'exists']], ['order_id' => 5]));
        // Unknown fields and operators fail closed.
        $this->assertFalse($e->matches([['field' => 'missing', 'operator' => 'equals', 'value' => 1]], []));
        $this->assertFalse($e->matches([['field' => 'points', 'operator' => 'regex', 'value' => 1]], ['points' => 1]));
    }

    public function test_action_job_executes_handler_and_updates_run_stats(): void
    {
        $rule = $this->rule();

        (new RunAutomationAction($rule, ['type' => 'log', 'params' => []], ['member_id' => 1]))
            ->handle(new ActionRegistry());

        $fresh = $rule->fresh();
        $this->assertSame(1, $fresh->run_count);
        $this->assertNotNull($fresh->last_run_at);
    }

    public function test_unknown_action_type_is_logged_not_fatal(): void
    {
        $rule = $this->rule(['actions' => [['type' => 'not_a_thing']]]);

        (new RunAutomationAction($rule, ['type' => 'not_a_thing'], []))
            ->handle(new ActionRegistry());

        $this->assertSame(0, $rule->fresh()->run_count);
    }
}
