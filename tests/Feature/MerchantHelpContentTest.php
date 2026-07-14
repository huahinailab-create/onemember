<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\Merchant;
use App\Models\User;
use Database\Seeders\KnowledgeArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** MERCHANT-READY-001 — real merchant help content + surfacing. */
class MerchantHelpContentTest extends TestCase
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
            'settings'                => ['locale' => 'en', 'installed_apps' => ['commerce']],
        ]);

        $this->seed(KnowledgeArticleSeeder::class);
    }

    public function test_seeder_imports_full_manual_and_is_idempotent(): void
    {
        $this->assertGreaterThanOrEqual(47, KnowledgeArticle::where('locale', 'en')->count());
        $this->assertGreaterThanOrEqual(6, KnowledgeArticle::where('locale', 'th')->count());

        foreach (['getting_started', 'members', 'loyalty', 'commerce', 'launch_kit', 'settings', 'troubleshooting', 'quick_start'] as $category) {
            $this->assertTrue(
                KnowledgeArticle::where('category', $category)->published()->exists(),
                "No published articles in {$category}",
            );
        }

        $count = KnowledgeArticle::count();
        $this->seed(KnowledgeArticleSeeder::class); // re-run = update, not duplicate
        $this->assertSame($count, KnowledgeArticle::count());
    }

    public function test_help_center_index_shows_all_categories_in_order(): void
    {
        $response = $this->actingAs($this->user)->get(route('help.index', absolute: false))->assertOk();

        foreach (['category_getting_started', 'category_members', 'category_loyalty', 'category_commerce',
                  'category_launch_kit', 'category_settings', 'category_troubleshooting', 'category_quick_start'] as $key) {
            $response->assertSee(__('help.' . $key));
        }

        // Getting Started renders before Troubleshooting
        $html = $response->getContent();
        $this->assertLessThan(
            strpos($html, __('help.category_troubleshooting')),
            strpos($html, __('help.category_getting_started')),
        );
    }

    public function test_articles_render_markdown_steps(): void
    {
        $this->actingAs($this->user)->get(route('help.article', 'add-a-member', absolute: false))
            ->assertOk()
            ->assertSee('Add a member')
            ->assertSee('<ol>', false)          // numbered steps rendered
            ->assertSee('Counter Mode');
    }

    public function test_search_finds_troubleshooting_content(): void
    {
        $this->actingAs($this->user)->get(route('help.index', ['q' => 'QR code'], absolute: false))
            ->assertOk()
            ->assertSee('QR code not scanning');
    }

    public function test_thai_merchant_gets_thai_articles_with_english_fallback(): void
    {
        $this->user->merchant->update(['settings' => ['locale' => 'th']]);

        $response = $this->actingAs($this->user->fresh())->get(route('help.index', absolute: false))->assertOk();
        $response->assertSee('OneMember คืออะไร?');          // Thai version wins
        $response->assertSee('Quick start: coffee shop');    // EN fallback still listed

        $this->actingAs($this->user->fresh())->get(route('help.article', 'what-is-onemember', absolute: false))
            ->assertOk()
            ->assertSee('ลูกค้าประจำ');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('help.index', absolute: false))->assertRedirect();
        $this->get(route('help.article', 'add-a-member', absolute: false))->assertRedirect();
    }

    public function test_contextual_help_buttons_resolve_to_articles(): void
    {
        $pages = [
            ['route' => 'members',                 'topic' => 'members.index',     'slug' => 'add-a-member'],
            ['route' => 'campaigns.index',         'topic' => 'campaigns.index',   'slug' => 'create-points-campaign'],
            ['route' => 'commerce.products.index', 'topic' => 'commerce.products', 'slug' => 'add-a-product'],
            ['route' => 'launch-kit',              'topic' => 'launch-kit',        'slug' => 'print-qr-poster'],
            // MR-002 — expanded contextual help
            ['route' => 'dashboard',               'topic' => 'dashboard',         'slug' => 'what-is-onemember'],
            ['route' => 'commerce.orders.index',   'topic' => 'commerce.orders',   'slug' => 'receive-orders'],
            ['route' => 'settings',                'topic' => 'settings',          'slug' => null],
        ];

        foreach ($pages as $page) {
            // The page renders its ? button…
            $this->actingAs($this->user)->get(route($page['route'], absolute: false))
                ->assertOk()
                ->assertSee('help/context/' . $page['topic'], false);

            // …and the context route resolves to the right article. A null
            // slug means "any article for this context" (multiple qualify).
            $slug = $page['slug']
                ?? app(\App\Services\KnowledgeService::class)->forContext($page['topic'], 'en')?->slug;
            $this->assertNotNull($slug, "No article registered for context {$page['topic']}");

            $this->actingAs($this->user)->get(route('help.context', $page['topic'], absolute: false))
                ->assertRedirect(route('help.article', $slug, absolute: false));
        }
    }

    /** MR-002 — rewards live inside a campaign; its tab carries a ? button. */
    public function test_rewards_help_button_on_campaign_and_landing_page(): void
    {
        $campaign = \App\Models\LoyaltyProgram::factory()->create([
            'merchant_id' => $this->user->merchant->id,
            'type'        => \App\Enums\LoyaltyProgramType::Points,
            'status'      => \App\Enums\CampaignStatus::Active,
        ]);

        $this->actingAs($this->user)->get(route('campaigns.show', $campaign, absolute: false))
            ->assertOk()
            ->assertSee('help/context/rewards', false);

        $this->actingAs($this->user)->get(route('rewards', absolute: false))
            ->assertOk()
            ->assertSee('help/context/rewards', false);

        $this->actingAs($this->user)->get(route('help.context', 'rewards', absolute: false))
            ->assertRedirect(route('help.article', 'create-rewards', absolute: false));
    }

    /**
     * MR-002 — no dead help links: every literal topic / help-topic used in a
     * Blade view must resolve to a published article (never the fallback).
     */
    public function test_every_help_topic_used_in_views_resolves_to_an_article(): void
    {
        $topics = [];
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            preg_match_all('/(?:help-topic|topic)="([\w.\-]+)"/', file_get_contents($file->getPathname()), $m);
            $topics = array_merge($topics, $m[1]);
        }

        $topics = array_unique($topics);
        $this->assertNotEmpty($topics);

        $knowledge = app(\App\Services\KnowledgeService::class);
        foreach ($topics as $topic) {
            $this->assertNotNull(
                $knowledge->forContext($topic, 'en'),
                "Help topic '{$topic}' is used in a view but no published article has that context_key",
            );
        }
    }

    public function test_sidebar_shows_help_link(): void
    {
        $this->actingAs($this->user)->get('/dashboard')
            ->assertOk()
            ->assertSee(__('navigation.help'));
    }
}
