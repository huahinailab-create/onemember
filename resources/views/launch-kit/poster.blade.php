@component('launch-kit.print-layout', ['title' => __('launch.asset_poster')])
    <section class="launch-sheet launch-sheet-a4">
        <div class="launch-poster-brand">{{ $merchant->name }}</div>

        <h1 class="launch-poster-headline">{{ __('launch.campaign_headline') }}</h1>
        <p class="launch-poster-offer">{{ __('launch.offer_' . $offer) }}</p>

        <div class="launch-poster-qr">{!! $joinQrSvg !!}</div>
        <p class="launch-poster-scan">{{ __('launch.poster_scan') }}</p>

        <p class="launch-poster-earn">{{ __('launch.poster_earn') }}</p>
        <p class="launch-poster-ask">{{ __('launch.poster_ask') }}</p>

        <footer class="launch-poster-footer">{{ __('launch.poster_powered') }}</footer>
    </section>
@endcomponent
