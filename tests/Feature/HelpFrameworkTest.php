<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/** PLATFORM-002 Part 11 — screen help framework (topbar entry, ? component). */
class HelpFrameworkTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_topbar_shows_global_help_button(): void
    {
        $this->actingAs($this->user)->get('/dashboard')
            ->assertOk()
            ->assertSee(route('help.index', absolute: false));
    }

    public function test_help_button_component_links_topic_to_context_route(): void
    {
        $html = Blade::render('<x-ui.help-button topic="members.index" />');

        $this->assertStringContainsString('help/context/members.index', $html);
        $this->assertStringContainsString('data-help-topic="members.index"', $html);
        $this->assertStringContainsString('aria-label', $html);
    }

    public function test_context_route_resolves_to_article_when_registered(): void
    {
        KnowledgeArticle::create([
            'slug' => 'managing-members', 'locale' => 'en', 'version' => 1,
            'category' => 'manual', 'title' => 'Managing members',
            'body' => 'How to manage members.', 'context_key' => 'members.index',
            'published' => true,
        ]);

        $this->actingAs($this->user)
            ->get(route('help.context', 'members.index', absolute: false))
            ->assertRedirect(route('help.article', 'managing-members', absolute: false));
    }
}
