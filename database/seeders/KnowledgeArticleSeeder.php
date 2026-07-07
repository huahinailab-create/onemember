<?php

namespace Database\Seeders;

use App\Models\KnowledgeArticle;
use Illuminate\Database\Seeder;

/**
 * PLATFORM-002 Part 7 — starter Knowledge Center content (never auto-runs;
 * invoke with `php artisan db:seed --class=KnowledgeArticleSeeder`).
 * Real documentation is content work; these two articles prove the rails.
 */
class KnowledgeArticleSeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeArticle::updateOrCreate(
            ['slug' => 'getting-started', 'locale' => 'en', 'version' => 1],
            [
                'category'    => 'getting_started',
                'title'       => 'Getting started with OneMember',
                'body'        => "## Welcome\n\n1. Create your first **campaign**.\n2. Add a **reward**.\n3. Add your first **member**.\n4. Print your **Launch Kit** and put the poster by the counter.",
                'context_key' => 'dashboard',
                'published'   => true,
                'sort'        => 1,
            ],
        );

        KnowledgeArticle::updateOrCreate(
            ['slug' => 'what-is-counter-mode', 'locale' => 'en', 'version' => 1],
            [
                'category'    => 'faq',
                'title'       => 'What is Counter Mode?',
                'body'        => "Counter Mode is the fast staff-facing screen for recording purchases.\n\nEnable it in **Settings → Business Preferences**, then open it from the top bar.",
                'context_key' => 'counter',
                'published'   => true,
                'sort'        => 1,
            ],
        );
    }
}
