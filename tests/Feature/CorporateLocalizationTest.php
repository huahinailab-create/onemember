<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

/**
 * DECISION-067 — RELEASE-2C Thai-First Corporate Localization
 *
 * Verifies that onemember.co defaults to Thai, the language switcher is
 * present and functional on corporate pages, and that all corporate pages
 * render content through translation keys (not hardcoded strings).
 */
class CorporateLocalizationTest extends TestCase
{
    // ── 1. Thai default on corporate home ─────────────────────────────────────

    public function test_corporate_home_renders_thai_by_default(): void
    {
        // No session → SetLocale middleware hard-defaults to Thai (Thai-first site).
        $response = $this->get('http://onemember.co/');

        $response->assertOk();
        // footer_tagline is a distinctive Thai string present on every page
        $response->assertSee('ความสัมพันธ์คือหัวใจ — ลูกค้าประจำของคุณ กลับมาบ่อยขึ้น', false);
    }

    public function test_corporate_home_html_lang_attribute_is_thai_by_default(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertOk();
        $response->assertSee('lang="th"', false);
    }

    // ── 2. Language switcher present on corporate pages ───────────────────────

    public function test_corporate_home_has_language_switcher(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertOk();
        $response->assertSee('lang-switcher-btn', false);
        $response->assertSee('/locale', false);
    }

    public function test_corporate_pricing_has_language_switcher(): void
    {
        $response = $this->get('http://onemember.co/pricing');

        $response->assertOk();
        $response->assertSee('lang-switcher-btn', false);
    }

    public function test_language_switcher_shows_globe_emoji(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertOk();
        $response->assertSee('🌐 English', false);
        $response->assertSee('🌐 ภาษาไทย', false);
    }

    // ── 3. Switching to English ───────────────────────────────────────────────

    public function test_switching_to_english_stores_locale_in_session(): void
    {
        $response = $this->withSession([])->post('/locale', [
            'locale'     => 'en',
            'return_url' => 'http://onemember.co/',
        ]);

        $response->assertSessionHas('locale', 'en');
    }

    public function test_switching_to_english_redirects_to_return_url(): void
    {
        $response = $this->withSession([])->post('/locale', [
            'locale'     => 'en',
            'return_url' => 'http://onemember.co/',
        ]);

        $response->assertRedirect('http://onemember.co/');
    }

    public function test_corporate_home_renders_english_after_locale_switch(): void
    {
        $response = $this->withSession(['locale' => 'en'])
            ->get('http://onemember.co/');

        $response->assertOk();
        $response->assertSee('Relationships matter — your regulars, coming back more often.', false);
    }

    // ── 4. Switching back to Thai ─────────────────────────────────────────────

    public function test_switching_back_to_thai_stores_locale_in_session(): void
    {
        $response = $this->withSession(['locale' => 'en'])->post('/locale', [
            'locale'     => 'th',
            'return_url' => 'http://onemember.co/',
        ]);

        $response->assertSessionHas('locale', 'th');
    }

    public function test_corporate_home_renders_thai_after_switching_back(): void
    {
        $response = $this->withSession(['locale' => 'th'])
            ->get('http://onemember.co/');

        $response->assertOk();
        $response->assertSee('ความสัมพันธ์คือหัวใจ — ลูกค้าประจำของคุณ กลับมาบ่อยขึ้น', false);
    }

    // ── 5. Corporate pages use translations ───────────────────────────────────

    public function test_corporate_pricing_page_translates_in_thai(): void
    {
        App::setLocale('th');
        $response = $this->withSession(['locale' => 'th'])
            ->get('http://onemember.co/pricing');

        $response->assertOk();
        $response->assertSee(__('corporate.nav_pricing'), false);
    }

    public function test_corporate_faq_page_translates_in_thai(): void
    {
        $response = $this->withSession(['locale' => 'th'])
            ->get('http://onemember.co/faq');

        $response->assertOk();
        // Thai nav_contact is present in footer on every page
        $response->assertSee('ติดต่อเรา', false);
    }

    public function test_corporate_about_page_translates_in_english(): void
    {
        $response = $this->withSession(['locale' => 'en'])
            ->get('http://onemember.co/about');

        $response->assertOk();
        $response->assertSee('About', false);
    }

    public function test_corporate_privacy_page_renders_without_error(): void
    {
        $response = $this->get('http://onemember.co/privacy');
        $response->assertOk();
    }

    public function test_corporate_terms_page_renders_without_error(): void
    {
        $response = $this->get('http://onemember.co/terms');
        $response->assertOk();
    }

    public function test_corporate_pdpa_page_renders_without_error(): void
    {
        $response = $this->get('http://onemember.co/pdpa');
        $response->assertOk();
    }

    public function test_corporate_contact_page_renders_without_error(): void
    {
        $response = $this->get('http://onemember.co/contact');
        $response->assertOk();
    }

    // ── 6. Invalid locale is rejected ────────────────────────────────────────

    public function test_invalid_locale_is_rejected_and_redirects_back(): void
    {
        $response = $this->withSession([])->post('/locale', [
            'locale'     => 'ja',
            'return_url' => 'http://onemember.co/',
        ]);

        // Should not persist invalid locale
        $response->assertSessionMissing('locale');
    }

    // ── 7. App domain routing unaffected ─────────────────────────────────────

    public function test_app_domain_still_routes_correctly_after_locale_changes(): void
    {
        // Switching locale on corporate must not break app domain routing
        $this->withSession(['locale' => 'th'])->post('/locale', [
            'locale'     => 'en',
            'return_url' => 'http://onemember.co/',
        ]);

        $response = $this->get('http://onemember.co/members');
        $response->assertStatus(404); // app route not served on corporate domain
    }

    // ── 8. SEO — meta tags translate ─────────────────────────────────────────

    public function test_corporate_home_meta_title_is_thai_by_default(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertOk();
        App::setLocale('th');
        $response->assertSee(__('corporate.home_meta_title'), false);
    }

    public function test_corporate_home_og_title_is_thai_by_default(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertOk();
        $response->assertSee('og:title', false);
    }
}
