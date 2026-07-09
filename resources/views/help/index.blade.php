<x-app-layout>
    <x-slot name="title">{{ __('help.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('help.title') }}</x-slot>

    <x-ui.page-header :title="__('help.title')" :subtitle="__('help.subtitle')" />

    {{-- Search --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('help.index') }}" class="d-flex gap-2">
                <input type="search" name="q" value="{{ $term }}"
                       class="form-control"
                       placeholder="{{ __('help.search_ph') }}"
                       aria-label="{{ __('help.search_ph') }}">
                <button type="submit" class="btn btn-primary" aria-label="{{ __('buttons.search') }}">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>

    @if ($term !== '')
        <div class="card">
            <div class="card-header fw-semibold">{{ __('help.results_for', ['term' => $term]) }}</div>
            <div class="card-body p-0">
                @if ($results->isEmpty())
                    <x-ui.empty-state icon="bi-search" :title="__('help.no_results')" :body="__('help.no_results_body')" />
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($results as $article)
                            <a href="{{ route('help.article', $article->slug) }}"
                               class="list-group-item list-group-item-action py-3">
                                <div class="fw-medium">{{ $article->title }}</div>
                                <div class="text-muted small">{{ __('help.category_' . $article->category) }}</div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        @if ($categories->isEmpty())
            <div class="card"><div class="card-body">
                <x-ui.empty-state icon="bi-life-preserver" :title="__('help.empty_title')" :body="__('help.empty_body')" />
            </div></div>
        @else
            <div class="row g-4">
                @foreach ($categories as $category => $articles)
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-folder2-open me-2 text-primary"></i>{{ __('help.category_' . $category) }}
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach ($articles as $article)
                                    <a href="{{ route('help.article', $article->slug) }}"
                                       class="list-group-item list-group-item-action">
                                        {{ $article->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</x-app-layout>
