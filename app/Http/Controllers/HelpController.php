<?php

namespace App\Http\Controllers;

use App\Services\KnowledgeService;
use Illuminate\Http\Request;

/**
 * PLATFORM-002 Part 7/11 — merchant-facing Knowledge Center + context help.
 */
class HelpController extends Controller
{
    public function __construct(private readonly KnowledgeService $knowledge)
    {
    }

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $term   = trim((string) $request->query('q', ''));

        return view('help.index', [
            'categories' => $term === '' ? $this->knowledge->index($locale) : collect(),
            'results'    => $term === '' ? collect() : $this->knowledge->search($term, $locale),
            'term'       => $term,
        ]);
    }

    public function article(string $slug)
    {
        $article = $this->knowledge->bySlug($slug, app()->getLocale());

        abort_unless($article, 404);

        return view('help.article', ['article' => $article]);
    }

    /** Part 11 — "?" buttons resolve a screen context key to its article. */
    public function context(string $key)
    {
        $article = $this->knowledge->forContext($key, app()->getLocale());

        if ($article) {
            return redirect()->route('help.article', $article->slug);
        }

        return redirect()->route('help.index')
            ->with('error', __('help.no_context_article'));
    }
}
