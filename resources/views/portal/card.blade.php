<x-portal-layout :branding="$branding"
    :title="$member->name . ' — ' . __('portal.card_title')">

    <div class="container py-4" style="max-width:480px">

        {{-- Print / download actions --}}
        <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
            <a href="{{ route('portal.show', $publicUuid) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('portal.back_to_portal') }}
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"
                        aria-label="{{ __('portal.print_card') }}">
                    <i class="bi bi-printer me-1"></i>{{ __('portal.print_card') }}
                </button>
            </div>
        </div>

        {{-- Digital member card --}}
        <div class="portal-member-card shadow rounded-3 p-4 text-white mb-4"
             role="region" aria-label="{{ __('portal.card_title') }}">

            {{-- Card header: merchant branding --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                @if ($branding->logo())
                    <img src="{{ $branding->logo() }}"
                         alt="{{ $branding->displayName() }}"
                         class="portal-card-logo"
                         style="max-height:40px;max-width:120px;object-fit:contain;">
                @else
                    <span class="fw-bold fs-6">{{ $branding->displayName() }}</span>
                @endif
                <span class="badge bg-white text-dark small">{{ __('portal.member_card') }}</span>
            </div>

            {{-- Member name and number --}}
            <div class="mb-4">
                <div class="small opacity-75 mb-1">{{ __('portal.card_member_name') }}</div>
                <div class="fw-bold fs-5">{{ $member->name }}</div>
                @if ($member->nickname)
                    <div class="small opacity-75">"{{ $member->nickname }}"</div>
                @endif
                <div class="font-monospace small opacity-75 mt-1">{{ $member->member_code }}</div>
            </div>

            {{-- QR Code --}}
            <div class="text-center mb-3">
                <div class="d-inline-block bg-white p-3 rounded-2" aria-label="{{ __('portal.qr_aria') }}">
                    {!! $qrSvg !!}
                </div>
                <div class="small opacity-75 mt-2">{{ __('portal.scan_to_open') }}</div>
            </div>

            {{-- Barcode --}}
            <div class="bg-white rounded-2 p-2 text-center" aria-label="{{ __('portal.barcode_aria') }}">
                {!! $barcodeSvg !!}
                <div class="small text-dark font-monospace mt-1">{{ $member->member_code }}</div>
            </div>

        </div>

        {{-- Member since --}}
        @if ($member->joined_at)
            <p class="text-muted text-center small">
                {{ __('portal.member_since') }}: {{ $member->joined_at->format('d M Y') }}
            </p>
        @endif

    </div>

</x-portal-layout>
