@component('launch-kit.print-layout', ['title' => __('launch.asset_staff_guide')])
    <section class="launch-sheet launch-sheet-a4 launch-guide">
        <header class="launch-guide-header">
            <div class="launch-guide-brand">{{ $merchant->name }}</div>
            <h1 class="launch-guide-title">{{ __('launch.guide_title') }}</h1>
            <p class="launch-guide-intro">{{ __('launch.guide_intro', ['url' => parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url')]) }}</p>
        </header>

        <ol class="launch-guide-steps">
            @foreach ([
                ['title' => __('launch.guide_step_search'),   'how' => __('launch.guide_step_search_how')],
                ['title' => __('launch.guide_step_add'),      'how' => __('launch.guide_step_add_how')],
                ['title' => __('launch.guide_step_purchase'), 'how' => __('launch.guide_step_purchase_how')],
                ['title' => __('launch.guide_step_redeem'),   'how' => __('launch.guide_step_redeem_how')],
                ['title' => __('launch.guide_step_counter'),  'how' => __('launch.guide_step_counter_how')],
            ] as $step)
                <li class="launch-guide-step">
                    <span class="launch-guide-step-title">{{ $step['title'] }}</span>
                    <span class="launch-guide-step-how">{{ $step['how'] }}</span>
                </li>
            @endforeach
        </ol>

        <div class="launch-guide-say">
            <h2 class="launch-guide-say-title">{{ __('launch.guide_say_title') }}</h2>
            <ul class="launch-guide-say-list">
                <li>{{ __('launch.guide_say_1') }}</li>
                <li>{{ __('launch.guide_say_2') }}</li>
                <li>{{ __('launch.guide_say_3', ['points' => 25]) }}</li>
            </ul>
        </div>

        <div class="launch-guide-qr-row">
            <div class="launch-guide-qr">{!! $counterQrSvg !!}</div>
            <div class="launch-guide-qr-label">{{ __('launch.guide_counter_qr') }}</div>
        </div>

        <footer class="launch-poster-footer">{{ __('launch.poster_powered') }}</footer>
    </section>
@endcomponent
