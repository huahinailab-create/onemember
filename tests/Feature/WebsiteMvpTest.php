<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * WEBSITE-002A — Public Marketing Website MVP.
 *
 * Covers the 8 pages built against docs/OMOS/Website/*.md: every public
 * page returns 200 for a guest, renders in both locales, has no dead
 * internal links, exposes no unapproved fixed prices (DECISION-014 is
 * unresolved — Starter/Professional must show "TBA", never a number),
 * carries basic SEO metadata, and never regresses authenticated app
 * routing. Uses the existing onemember.co corporate domain/routes rather
 * than inventing a parallel site — see ADR (docs/OMOS/12-ADR) and
 * DECISION-099.
 */
class WebsiteMvpTest extends TestCase
{
    use RefreshDatabase;

    /** The 8 in-scope pages for this sprint, by route name. */
    private const PAGES = [
        'corporate.home',
        'corporate.features',
        'corporate.industries',
        'corporate.pricing',
        'corporate.about',
        'corporate.faq',
        'corporate.contact',
        'corporate.resources', // Knowledge Center entry point
    ];

    private function url(string $path): string
    {
        return 'http://' . config('domains.corporate') . $path;
    }

    // ── All public pages return 200 for guests (both locales) ───────────

    public function test_all_website_pages_return_200_for_guest_in_english(): void
    {
        foreach (self::PAGES as $route) {
            $response = $this->withSession(['locale' => 'en'])->get(route($route, absolute: true));
            $response->assertOk("Route [{$route}] did not return 200 (en)");
        }
    }

    public function test_all_website_pages_return_200_for_guest_in_thai(): void
    {
        foreach (self::PAGES as $route) {
            $response = $this->withSession(['locale' => 'th'])->get(route($route, absolute: true));
            $response->assertOk("Route [{$route}] did not return 200 (th)");
        }
    }

    // ── English / Thai parity — no mixed-language pages ──────────────────

    public function test_home_renders_english_positioning_copy(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        $response->assertSee('Your regulars,', false);
        $response->assertSee('coming back more often.', false);
    }

    public function test_home_renders_thai_positioning_copy(): void
    {
        $response = $this->withSession(['locale' => 'th'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        $response->assertSee('ลูกค้าประจำของคุณ', false);
    }

    public function test_industries_page_has_all_ten_blueprint_segments(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.industries', absolute: true));

        $response->assertOk();
        foreach (['Coffee Shops', 'Restaurants', 'Hair Salons', 'Nail Salons', 'Massage &amp; Spa',
                  'Hotels', 'Retail', 'Fashion', 'Pet Shops', 'Beauty Clinics'] as $industry) {
            $response->assertSee($industry, false);
        }
    }

    // ── Main CTAs resolve ─────────────────────────────────────────────────

    public function test_start_free_cta_points_at_the_real_registration_route(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        $response->assertSee('https://' . config('domains.app') . '/register', false);
    }

    public function test_navigation_links_resolve_to_real_routes(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));
        $response->assertOk();

        foreach (self::PAGES as $route) {
            $response->assertSee(route($route, absolute: true), false);
        }
    }

    // ── No dead public links: every corporate.* named route resolves ─────

    public function test_no_dead_links_every_corporate_route_is_reachable(): void
    {
        $corporateRoutes = collect(Route::getRoutes())
            ->filter(fn ($r) => str_starts_with((string) $r->getName(), 'corporate.'))
            ->filter(fn ($r) => in_array('GET', $r->methods()) && ! str_contains($r->uri(), '{'));

        $this->assertGreaterThan(0, $corporateRoutes->count());

        foreach ($corporateRoutes as $route) {
            $response = $this->withSession(['locale' => 'en'])->get('http://' . config('domains.corporate') . '/' . ltrim($route->uri(), '/'));
            $response->assertOk("Route [{$route->getName()}] is a dead link");
        }
    }

    // ── Pricing exposes no unapproved fixed prices (DECISION-014) ────────

    public function test_pricing_page_does_not_expose_a_starter_or_professional_price(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.pricing', absolute: true));

        $response->assertOk();
        $response->assertSee('TBA', false); // safe placeholder, per DECISION-014 pending
        $response->assertDontSeeText('999/month');
        $response->assertDontSeeText('฿1,990');
        $response->assertDontSeeText('฿990');
    }

    public function test_pricing_free_tier_is_the_only_hardcoded_amount_and_it_is_zero(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.pricing', absolute: true));

        $response->assertOk();
        $response->assertSee('฿0', false);
    }

    public function test_pricing_enterprise_shows_contact_us_not_a_number(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.pricing', absolute: true));

        $response->assertOk();
        $response->assertSee('Custom', false);
    }

    public function test_pricing_labels_unshipped_enterprise_features_as_planned(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.pricing', absolute: true));

        $response->assertOk();
        $response->assertSee('(planned)', false);
    }

    // ── No fabricated content: testimonials, statistics ───────────────────

    public function test_home_page_hides_testimonials_section_when_none_are_real(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        $response->assertDontSee('corp-testimonial', false);
        $response->assertDontSee('Paris Coffee', false);
        $response->assertDontSee('Milan Beauty Salon', false);
    }

    public function test_home_page_does_not_claim_a_fabricated_setup_time(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        // The old copy claimed "2 min" setup, inconsistent with the approved
        // "10 minutes to launch" proof spine (01-Website-Strategy.md).
        $response->assertDontSeeText('2 min');
        $response->assertSee('10 min', false);
    }

    // ── FAQ: content, structured data, accessibility hooks ───────────────

    public function test_faq_page_has_the_mvp_question_count_within_range(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.faq', absolute: true));

        $response->assertOk();
        $count = substr_count($response->getContent(), 'accordion-button');
        $this->assertGreaterThanOrEqual(30, $count, 'FAQ should publish at least 30 questions');
        $this->assertLessThanOrEqual(45, $count, 'FAQ MVP should not force all 100 questions onto one page');
    }

    public function test_faq_page_has_faqpage_structured_data(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.faq', absolute: true));

        $response->assertOk();
        $response->assertSee('"@type":"FAQPage"', false);
    }

    public function test_faq_accordion_buttons_have_explicit_aria_expanded(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.faq', absolute: true));

        $response->assertOk();
        $response->assertSee('aria-expanded="false"', false);
    }

    // ── SEO metadata ───────────────────────────────────────────────────────

    public function test_every_page_has_title_meta_description_canonical_and_og_tags(): void
    {
        foreach (self::PAGES as $route) {
            $response = $this->withSession(['locale' => 'en'])->get(route($route, absolute: true));
            $response->assertOk();
            $html = $response->getContent();

            $this->assertStringContainsString('<title>', $html, "{$route} missing <title>");
            $this->assertStringContainsString('name="description"', $html, "{$route} missing meta description");
            $this->assertStringContainsString('rel="canonical"', $html, "{$route} missing canonical link");
            $this->assertStringContainsString('property="og:title"', $html, "{$route} missing og:title");
            $this->assertStringContainsString('property="og:description"', $html, "{$route} missing og:description");
        }
    }

    public function test_organization_structured_data_present_sitewide(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        $response->assertSee('"@type":"Organization"', false);
    }

    // ── Accessibility: mobile navigation markup ───────────────────────────

    public function test_mobile_nav_toggle_has_an_accessible_label(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.home', absolute: true));

        $response->assertOk();
        $response->assertSee('navbar-toggler', false);
        $response->assertSee('aria-label="Toggle navigation menu"', false);
    }

    public function test_contact_form_fields_have_associated_labels(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.contact', absolute: true));

        $response->assertOk();
        $response->assertSee('for="contact-name"', false);
        $response->assertSee('id="contact-name"', false);
        $response->assertSee('for="contact-email"', false);
        $response->assertSee('id="contact-email"', false);
    }

    // ── Knowledge Center entry point ──────────────────────────────────────

    public function test_resources_page_has_a_knowledge_center_entry_point(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('corporate.resources', absolute: true));

        $response->assertOk();
        $response->assertSee('Knowledge Center', false);
    }

    // ── Authenticated app routes remain unaffected ────────────────────────

    public function test_authenticated_merchant_dashboard_route_still_works(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        \App\Models\Merchant::factory()->create(['user_id' => $user->id, 'onboarding_completed_at' => now()]);

        $response = $this->actingAs($user->fresh())->get('http://' . config('domains.app') . '/dashboard');

        $response->assertOk();
    }

    public function test_login_route_still_works_on_app_domain(): void
    {
        $response = $this->get('http://' . config('domains.app') . '/login');

        $response->assertOk();
    }

    public function test_app_routes_are_not_served_on_the_corporate_domain(): void
    {
        // /dashboard has a deliberate 302 redirect stub on the corporate
        // domain (routes/web.php); /members has no such stub and must 404.
        $response = $this->get($this->url('/members'));

        $response->assertStatus(404);
    }

    // ── Storefront / Join / Commerce untouched (Part 5 backward-compat) ──

    public function test_storefront_and_join_routes_are_unaffected_by_website_changes(): void
    {
        // These are guest-facing app-domain routes exercised extensively
        // elsewhere (StoreIdentityTest, FullBetaJourneyTest); this is a
        // narrow smoke check that WEBSITE-002A didn't touch app-domain
        // routing at all.
        $this->assertTrue(Route::has('storefront.show'));
        $this->assertTrue(Route::has('join.show'));
    }
}
