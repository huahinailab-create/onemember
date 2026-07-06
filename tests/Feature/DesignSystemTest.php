<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * PLATFORM-001 — every ui.* design-system component renders and honours its
 * contract. These are the components all future screens must reuse.
 */
class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Blade::render happens outside the web middleware stack; share the
        // empty error bag exactly as ShareErrorsFromSession would.
        $this->app['view']->share('errors', new \Illuminate\Support\ViewErrorBag);
    }

    public function test_page_header_renders_title_subtitle_and_actions(): void
    {
        $html = Blade::render(
            '<x-ui.page-header title="Members" subtitle="Manage them"><a href="#" class="btn">Act</a></x-ui.page-header>'
        );

        $this->assertStringContainsString('<h1>Members</h1>', $html);
        $this->assertStringContainsString('Manage them', $html);
        $this->assertStringContainsString('page-header-actions', $html);
    }

    public function test_page_header_back_link_variant(): void
    {
        $html = Blade::render('<x-ui.page-header title="T" back-url="/members" back-label="Back" />');

        $this->assertStringContainsString('href="/members"', $html);
        $this->assertStringContainsString('bi-arrow-left', $html);
    }

    public function test_stat_card_renders_value_and_variant(): void
    {
        $html = Blade::render('<x-ui.stat-card icon="bi-people" label="Members" value="1,234" variant="pink" hint="this week" />');

        $this->assertStringContainsString('stat-card-pink', $html);
        $this->assertStringContainsString('stat-icon-pink', $html);
        $this->assertStringContainsString('1,234', $html);
        $this->assertStringContainsString('this week', $html);
    }

    public function test_empty_state_renders_with_action_slot(): void
    {
        $html = Blade::render('<x-ui.empty-state icon="bi-inbox" title="Nothing" body="Add one"><button>Add</button></x-ui.empty-state>');

        $this->assertStringContainsString('bi-inbox', $html);
        $this->assertStringContainsString('Nothing', $html);
        $this->assertStringContainsString('<button>Add</button>', $html);
    }

    public function test_status_badge_maps_statuses_to_single_colour_source(): void
    {
        $this->assertStringContainsString('bg-success', Blade::render('<x-ui.status-badge status="active" />'));
        $this->assertStringContainsString('bg-primary', Blade::render('<x-ui.status-badge status="placed" />'));
        $this->assertStringContainsString('bg-warning', Blade::render('<x-ui.status-badge status="unpaid" />'));
        $this->assertStringContainsString('bg-danger', Blade::render('<x-ui.status-badge status="cancelled" />'));
        $this->assertStringContainsString('bg-secondary', Blade::render('<x-ui.status-badge status="whatever" />'));
        $this->assertStringContainsString('>Paid<', Blade::render('<x-ui.status-badge status="paid" label="Paid" />'));
    }

    public function test_input_renders_label_required_and_hint(): void
    {
        $html = Blade::render('<x-ui.input name="phone" label="Phone" type="tel" value="081" required hint="Mobile only" />');

        $this->assertStringContainsString('for="phone"', $html);
        $this->assertStringContainsString('text-danger">*</span>', $html);
        $this->assertStringContainsString('type="tel"', $html);
        $this->assertStringContainsString('value="081"', $html);
        $this->assertStringContainsString('Mobile only', $html);
    }

    public function test_select_marks_selected_option_and_placeholder(): void
    {
        $html = Blade::render(
            '<x-ui.select name="country" label="Country" :options="[\'TH\' => \'Thailand\', \'MY\' => \'Malaysia\']" selected="MY" placeholder="Pick" />'
        );

        $this->assertStringContainsString('value="MY" selected', $html);
        $this->assertStringContainsString('Pick', $html);
        $this->assertStringNotContainsString('value="TH" selected', $html);
    }

    public function test_textarea_renders_value(): void
    {
        $html = Blade::render('<x-ui.textarea name="notes" label="Notes" value="hello" rows="4" />');

        $this->assertStringContainsString('rows="4"', $html);
        $this->assertStringContainsString('>hello</textarea>', $html);
    }

    public function test_modal_renders_with_footer_slot(): void
    {
        $html = Blade::render('<x-ui.modal id="m1" title="Confirm"><p>Body</p><x-slot name="footer"><button>OK</button></x-slot></x-ui.modal>');

        $this->assertStringContainsString('id="m1"', $html);
        $this->assertStringContainsString('Confirm', $html);
        $this->assertStringContainsString('modal-footer', $html);
    }

    public function test_progress_bar_clamps_and_sets_aria(): void
    {
        $html = Blade::render('<x-ui.progress-bar :percent="150" color="navy" />');

        $this->assertStringContainsString('width:100%', $html);
        $this->assertStringContainsString('#1A2E5A', $html);
        $this->assertStringContainsString('role="progressbar"', $html);

        $this->assertStringContainsString('width:0%', Blade::render('<x-ui.progress-bar :percent="-5" />'));
    }

    public function test_spinner_renders_accessible_label(): void
    {
        $html = Blade::render('<x-ui.spinner label="Loading data" size="sm" />');

        $this->assertStringContainsString('spinner-border-sm', $html);
        $this->assertStringContainsString('Loading data', $html);
    }

    public function test_flash_renders_session_success(): void
    {
        session()->flash('success', 'It worked');
        $html = Blade::render('<x-ui.flash />');

        $this->assertStringContainsString('alert-success', $html);
        $this->assertStringContainsString('It worked', $html);
    }

    public function test_apps_page_is_the_reference_implementation(): void
    {
        $user = \App\Models\User::factory()->create(['email_verified_at' => now()]);
        \App\Models\Merchant::factory()->create(['user_id' => $user->id, 'onboarding_completed_at' => now()]);

        $this->actingAs($user)
            ->get(route('apps.index', absolute: false))
            ->assertOk()
            ->assertSee('page-header', false);   // x-ui.page-header in use
    }
}
