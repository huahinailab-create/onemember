<x-app-layout>
    <x-slot name="title">{{ __('launch.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('launch.title') }}</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ __('launch.title') }}</h1>
            <p>{{ __('launch.subtitle') }}</p>
        </div>
        <x-ui.help-button topic="launch-kit" class="flex-shrink-0" />
    </div>

    <div class="row g-4">

        {{-- Join link + QR --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-qr-code me-2 text-primary"></i>{{ __('launch.join_link_heading') }}
                </div>
                <div class="card-body text-center">
                    <div class="launch-qr-frame mx-auto mb-3">{!! $joinQrSvg !!}</div>
                    <div class="small text-muted mb-2">{{ __('launch.join_link_hint') }}</div>
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" class="form-control" id="join-url" value="{{ $joinUrl }}" readonly>
                        <button class="btn btn-outline-primary" type="button" id="copy-join-url"
                                data-copied-label="{{ __('launch.link_copied') }}">
                            {{ __('launch.copy_link') }}
                        </button>
                    </div>
                    <a href="{{ $joinUrl }}" target="_blank" rel="noopener" class="small">
                        {{ $joinUrl }} <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Offer picker + campaign copy --}}
        <div class="col-12 col-lg-7">
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-gift me-2 text-primary"></i>{{ __('launch.offer_heading') }}
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-3">{{ __('launch.offer_hint') }}</div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach ($offers as $o)
                            <a href="{{ route('launch-kit', ['offer' => $o]) }}"
                               class="btn btn-sm {{ $o === $offer ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ __('launch.offer_label_' . $o) }}
                            </a>
                        @endforeach
                    </div>
                    <div class="launch-copy-preview p-3 rounded">
                        <div class="fs-5 fw-bold">{{ __('launch.campaign_headline') }}</div>
                        <div class="fs-6">{{ __('launch.offer_' . $offer) }}</div>
                    </div>
                </div>
            </div>

            {{-- Talking script --}}
            <div class="card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-chat-quote me-2 text-primary"></i>{{ __('launch.script_heading') }}
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">{{ __('launch.script_hint') }}</div>
                    <ul class="mb-0">
                        <li class="mb-1">{{ __('launch.guide_say_1') }}</li>
                        <li class="mb-1">{{ __('launch.guide_say_2') }}</li>
                        <li>{{ __('launch.guide_say_3', ['points' => 25]) }}</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Printable assets --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-printer me-2 text-primary"></i>{{ __('launch.assets_heading') }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ([
                            ['route' => 'launch-kit.poster',       'icon' => 'bi-file-image',   'title' => __('launch.asset_poster'),       'desc' => __('launch.asset_poster_desc')],
                            ['route' => 'launch-kit.counter-card', 'icon' => 'bi-postcard',     'title' => __('launch.asset_counter_card'), 'desc' => __('launch.asset_counter_desc')],
                            ['route' => 'launch-kit.staff-guide',  'icon' => 'bi-person-badge', 'title' => __('launch.asset_staff_guide'),  'desc' => __('launch.asset_staff_desc')],
                        ] as $asset)
                            <div class="col-12 col-md-4">
                                <div class="border rounded p-3 h-100 d-flex flex-column">
                                    <div class="fw-semibold mb-1">
                                        <i class="bi {{ $asset['icon'] }} me-2 text-primary"></i>{{ $asset['title'] }}
                                    </div>
                                    <div class="small text-muted flex-grow-1 mb-3">{{ $asset['desc'] }}</div>
                                    <a href="{{ route($asset['route'], ['offer' => $offer]) }}" target="_blank" rel="noopener"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-printer me-1"></i>{{ __('launch.open_print') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="small text-muted mt-3 mb-0">{{ __('launch.print_hint') }}</div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('copy-join-url')?.addEventListener('click', function () {
            const input = document.getElementById('join-url');
            navigator.clipboard.writeText(input.value).then(() => {
                const original = this.textContent;
                this.textContent = this.dataset.copiedLabel;
                setTimeout(() => { this.textContent = original; }, 1500);
            });
        });
    </script>
</x-app-layout>
