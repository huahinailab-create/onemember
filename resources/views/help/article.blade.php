<x-app-layout>
    <x-slot name="title">{{ $article->title }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('help.title') }}</x-slot>

    <x-ui.page-header :title="$article->title" :back-url="route('help.index')" />

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-muted small mb-3">
                        {{ __('help.category_' . $article->category) }}
                        · v{{ $article->version }}
                        · {{ $article->updated_at->format('d M Y') }}
                    </div>

                    @if ($article->video_url)
                        {{-- Video placeholder — embeds arrive with the video platform decision --}}
                        <div class="alert alert-light border small">
                            <i class="bi bi-play-circle me-1"></i>
                            <a href="{{ $article->video_url }}" target="_blank" rel="noopener">{{ __('help.watch_video') }}</a>
                        </div>
                    @endif

                    <div class="knowledge-body">{!! $article->renderedBody() !!}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
