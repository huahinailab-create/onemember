@component('launch-kit.print-layout', ['title' => __('launch.asset_counter_card')])
    <section class="launch-sheet launch-sheet-a6">
        <div class="launch-card-brand">{{ $merchant->name }}</div>
        <div class="launch-card-headline">{{ __('launch.campaign_headline') }}</div>
        <div class="launch-card-offer">{{ __('launch.offer_' . $offer) }}</div>
        <div class="launch-card-qr">{!! $joinQrSvg !!}</div>
        <div class="launch-card-scan">{{ __('launch.poster_scan') }}</div>
        <footer class="launch-card-footer">{{ __('launch.poster_powered') }}</footer>
    </section>
@endcomponent
