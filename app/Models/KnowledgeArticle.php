<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * PLATFORM-002 Part 7 — one Knowledge Center article.
 *
 * Platform-level content (no merchant_id — the same manual serves every
 * tenant), written in Markdown, versioned per (slug, locale): readers get
 * the highest published version. context_key ties an article to a screen
 * for the Part 11 help framework. video_url is a placeholder until video
 * hosting is decided.
 */
class KnowledgeArticle extends Model
{
    protected $fillable = [
        'slug', 'locale', 'version', 'category', 'title', 'body',
        'video_url', 'context_key', 'published', 'sort',
    ];

    protected $casts = ['published' => 'boolean'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function scopeLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    /** Simple LIKE search over title + body (DB-agnostic; upgradeable later). */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return $query->where(fn (Builder $q) => $q
            ->where('title', 'like', $like)
            ->orWhere('body', 'like', $like));
    }

    /** Highest published version wins. */
    public function scopeLatestVersion(Builder $query): Builder
    {
        return $query->orderByDesc('version');
    }

    public function renderedBody(): string
    {
        return Str::markdown($this->body, [
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
