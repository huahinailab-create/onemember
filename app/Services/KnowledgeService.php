<?php

namespace App\Services;

use App\Models\KnowledgeArticle;
use Illuminate\Support\Collection;

/**
 * PLATFORM-002 Part 7 — read-side of the Knowledge Center.
 * Locale behaviour: requested locale first, English fallback (same rule as
 * the localization model — content coverage is content work).
 */
class KnowledgeService
{
    /** @return Collection<string, Collection<int, KnowledgeArticle>> category => articles */
    public function index(string $locale): Collection
    {
        return $this->localePreferred($locale)
            ->sortBy([['sort', 'asc'], ['title', 'asc']])
            ->groupBy('category');
    }

    /** @return Collection<int, KnowledgeArticle> */
    public function search(string $term, string $locale): Collection
    {
        if (trim($term) === '') {
            return collect();
        }

        return $this->localePreferred($locale, fn ($q) => $q->search($term))->take(25)->values();
    }

    public function bySlug(string $slug, string $locale): ?KnowledgeArticle
    {
        return KnowledgeArticle::published()->where('slug', $slug)->locale($locale)->latestVersion()->first()
            ?? KnowledgeArticle::published()->where('slug', $slug)->locale('en')->latestVersion()->first();
    }

    /** Context help (Part 11): the article attached to a screen key. */
    public function forContext(string $contextKey, string $locale): ?KnowledgeArticle
    {
        return KnowledgeArticle::published()->where('context_key', $contextKey)->locale($locale)->latestVersion()->first()
            ?? KnowledgeArticle::published()->where('context_key', $contextKey)->locale('en')->latestVersion()->first();
    }

    /** Requested-locale articles plus English ones missing in that locale. */
    private function localePreferred(string $locale, ?\Closure $modifier = null): Collection
    {
        $query = KnowledgeArticle::published();
        if ($modifier) {
            $modifier($query);
        }
        $all = $query->get();

        $inLocale = $all->where('locale', $locale);
        $fallback = $all->where('locale', 'en')
            ->reject(fn ($a) => $inLocale->contains('slug', $a->slug));

        return $inLocale->concat($locale === 'en' ? collect() : $fallback)
            ->groupBy('slug')
            ->map(fn ($versions) => $versions->sortByDesc('version')->first())
            ->values();
    }
}
