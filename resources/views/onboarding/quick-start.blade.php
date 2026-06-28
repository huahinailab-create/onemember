@extends('layouts.wizard')

@section('title', __('onboarding.quick_start_title') . ' – ' . config('app.name'))

@section('header-action')
    <a href="{{ route('onboarding.skip') }}" class="btn btn-sm btn-outline-secondary">
        {{ __('onboarding.skip_for_now') }}
    </a>
@endsection

@section('content')
<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">{{ __('onboarding.step_5_of_6') }}</span>
            <span class="text-muted small">83%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-primary" style="width:83%;" role="progressbar"></div>
        </div>
    </div>

    <div class="card-body p-4">
        <h2 class="fw-bold fs-4 mb-1">{{ __('onboarding.quick_start_heading') }}</h2>
        <p class="text-muted mb-4">
            We'll create a
            {{ $loyaltyType === 'stamps' ? 'stamp card campaign' : 'points campaign' }}
            with a reward so you can start rewarding members right away.
        </p>

        @if ($hasCampaigns)
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                {{ __('onboarding.has_campaign_notice') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.quick-start.store') }}">
            @csrf

            @php $selected = old('choice', ''); @endphp

            {{-- Yes --}}
            <label class="d-block mb-3" style="cursor:pointer;">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3
                    {{ $selected === 'yes' ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                    id="card-yes">
                    <input type="radio"
                           name="choice"
                           value="yes"
                           class="form-check-input mt-0 flex-shrink-0"
                           style="width:1.25rem;height:1.25rem;"
                           {{ $selected === 'yes' ? 'checked' : '' }}
                           onchange="highlightCard(this)">
                    <div>
                        <div class="fw-semibold">{{ __('onboarding.quick_start_yes') }}
                            <span class="badge bg-primary ms-1 fw-normal" style="font-size:0.7rem;">{{ __('onboarding.recommended') }}</span>
                        </div>
                        @if ($loyaltyType === 'stamps')
                            <div class="text-muted small mt-1">
                                {{ __('onboarding.quick_start_stamps_desc') }}
                            </div>
                        @else
                            <div class="text-muted small mt-1">
                                {{ __('onboarding.quick_start_points_desc') }}
                            </div>
                        @endif
                    </div>
                </div>
            </label>

            {{-- No --}}
            <label class="d-block mb-4" style="cursor:pointer;">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3
                    {{ $selected === 'no' ? 'border-secondary bg-light' : '' }}"
                    id="card-no">
                    <input type="radio"
                           name="choice"
                           value="no"
                           class="form-check-input mt-0 flex-shrink-0"
                           style="width:1.25rem;height:1.25rem;"
                           {{ $selected === 'no' ? 'checked' : '' }}
                           onchange="highlightCard(this)">
                    <div>
                        <div class="fw-semibold">{{ __('onboarding.quick_start_no') }}</div>
                        <div class="text-muted small mt-1">{{ __('onboarding.quick_start_no_sub') }}</div>
                    </div>
                </div>
            </label>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ __('onboarding.finish_setup') }} <i class="bi bi-check-lg ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function highlightCard(radio) {
    document.getElementById('card-yes').classList.remove('border-primary','bg-primary','bg-opacity-10');
    document.getElementById('card-no').classList.remove('border-secondary','bg-light');
    var card = radio.closest('[id^="card-"]');
    if (card) {
        if (radio.value === 'yes') {
            card.classList.add('border-primary','bg-primary','bg-opacity-10');
        } else {
            card.classList.add('border-secondary','bg-light');
        }
    }
}
</script>
@endsection
