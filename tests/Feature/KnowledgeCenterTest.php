<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** PLATFORM-002 Part 7 — Knowledge Center: search, versioning, locale fallback, context. */
class KnowledgeCenterTest extends TestCase
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
            'settings'                => ['locale' => 'en'],
        ]);
    }

    private function article(array $overrides = []): KnowledgeArticle
    {
        return KnowledgeArticle::create(array_merge([
            'slug'      => 'getting-started',
            'locale'    => 'en',
            'version'   => 1,
            'category'  => 'getting_started',
            'title'     => 'Getting started with OneMember',
            'body'      => "## Welcome\nCreate your first campaign.",
            'published' => true,
        ], $overrides));
    }

    public function test_help_index_lists_published_articles_by_category(): void
    {
        $this->article();
        $this->article(['slug' => 'hidden', 'title' => 'Hidden draft', 'published' => false]);

        $this->actingAs($this->user)->get(route('help.index', absolute: false))
            ->assertOk()
            ->assertSee('Getting started with OneMember')
            ->assertDontSee('Hidden draft');
    }

    public function test_article_renders_markdown_safely(): void
    {
        $this->article(['body' => "## Steps\n\n<script>alert(1)</script>\n\nThis is **bold** text."]);

        $this->actingAs($this->user)->get(route('help.article', 'getting-started', absolute: false))
            ->assertOk()
            ->assertSee('<h2>Steps</h2>', false)
            ->assertSee('<strong>bold</strong>', false)
            // the injected script must be stripped from the rendered body
            ->assertDontSee('alert(1)', false);
    }

    public function test_search_finds_by_title_and_body(): void
    {
        $this->article();
        $this->article(['slug' => 'counter-faq', 'title' => 'Counter questions', 'body' => 'How to record a purchase quickly.']);

        $this->actingAs($this->user)->get(route('help.index', ['q' => 'purchase'], absolute: false))
            ->assertOk()
            ->assertSee('Counter questions')
            ->assertDontSee('Getting started with OneMember');
    }

    public function test_highest_published_version_wins(): void
    {
        $this->article(['title' => 'Old guide v1']);
        $this->article(['version' => 2, 'title' => 'New guide v2']);

        $this->actingAs($this->user)->get(route('help.article', 'getting-started', absolute: false))
            ->assertOk()
            ->assertSee('New guide v2');
    }

    public function test_thai_reader_gets_thai_article_with_english_fallback(): void
    {
        $this->user->merchant->update(['settings' => ['locale' => 'th']]);
        $this->article(); // EN only
        $this->article(['slug' => 'thai-only', 'locale' => 'th', 'title' => 'บทความภาษาไทย']);

        $response = $this->actingAs($this->user->fresh())->get(route('help.index', absolute: false))->assertOk();
        $response->assertSee('บทความภาษาไทย');
        $response->assertSee('Getting started with OneMember'); // EN fallback listed
    }

    public function test_context_help_redirects_to_matching_article_or_index(): void
    {
        $this->article(['context_key' => 'members.index']);

        $this->actingAs($this->user)->get(route('help.context', 'members.index', absolute: false))
            ->assertRedirect(route('help.article', 'getting-started', absolute: false));

        $this->actingAs($this->user)->get(route('help.context', 'unknown.screen', absolute: false))
            ->assertRedirect(route('help.index', absolute: false))
            ->assertSessionHas('error');
    }
}
