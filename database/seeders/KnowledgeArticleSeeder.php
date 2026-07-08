<?php

namespace Database\Seeders;

use App\Models\KnowledgeArticle;
use Illuminate\Database\Seeder;

/**
 * MERCHANT-READY-001 — imports the merchant Help Center content from
 * database/seeders/knowledge/*.md (front-matter + Markdown body).
 *
 * Idempotent (updateOrCreate on slug+locale+version) — safe to re-run on
 * every deploy: `php artisan db:seed --class=KnowledgeArticleSeeder`.
 * Content lives in git; editing an article = edit the file, re-seed.
 */
class KnowledgeArticleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (glob(database_path('seeders/knowledge/*.md')) as $file) {
            $parsed = $this->parse(file_get_contents($file));

            if ($parsed === null) {
                $this->command?->warn('Skipped (bad front-matter): ' . basename($file));

                continue;
            }

            [$meta, $body] = $parsed;

            KnowledgeArticle::updateOrCreate(
                [
                    'slug'    => $meta['slug'],
                    'locale'  => $meta['locale'] ?? 'en',
                    'version' => (int) ($meta['version'] ?? 1),
                ],
                [
                    'category'    => $meta['category'] ?? 'general',
                    'title'       => $meta['title'],
                    'body'        => trim($body),
                    'context_key' => $meta['context'] ?? null,
                    'video_url'   => $meta['video'] ?? null,
                    'sort'        => (int) ($meta['sort'] ?? 0),
                    'published'   => true,
                ],
            );
        }
    }

    /** @return array{0: array<string,string>, 1: string}|null */
    private function parse(string $raw): ?array
    {
        if (! preg_match('/\A---\s*\n(.*?)\n---\s*\n(.*)\z/s', $raw, $m)) {
            return null;
        }

        $meta = [];
        foreach (preg_split('/\r?\n/', $m[1]) as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $meta[trim($key)] = trim(trim($value), '"');
            }
        }

        if (empty($meta['slug']) || empty($meta['title'])) {
            return null;
        }

        return [$meta, $m[2]];
    }
}
