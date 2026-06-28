@extends('layouts.wizard')

@section('title', 'Loyalty Preference – ' . config('app.name'))

@section('header-action')
    <a href="{{ route('onboarding.skip') }}" class="btn btn-sm btn-outline-secondary">
        Skip for Now
    </a>
@endsection

@section('content')
<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">Step 4 of 6</span>
            <span class="text-muted small">67%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-primary" style="width:67%;" role="progressbar"></div>
        </div>
    </div>

    <div class="card-body p-4">
        <h2 class="fw-bold fs-4 mb-1">What type of loyalty program would you like to start with?</h2>
        <p class="text-muted mb-4">You can always add more campaign types later.</p>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.loyalty.store') }}">
            @csrf

            @php $selected = old('loyalty_type', $merchant?->settings['onboarding_loyalty_type'] ?? ''); @endphp

            {{-- Points option --}}
            <label class="d-block mb-3 cursor-pointer" style="cursor:pointer;">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3
                    {{ $selected === 'points' ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                    id="card-points">
                    <input type="radio"
                           name="loyalty_type"
                           value="points"
                           class="form-check-input mt-0 flex-shrink-0"
                           style="width:1.25rem;height:1.25rem;"
                           {{ $selected === 'points' ? 'checked' : '' }}
                           onchange="highlightCard(this)">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-lightning text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-semibold fs-5">Points</div>
                        <div class="text-muted small">Customers earn points based on purchases.</div>
                    </div>
                </div>
            </label>

            {{-- Stamps option --}}
            <label class="d-block mb-4 cursor-pointer" style="cursor:pointer;">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3
                    {{ $selected === 'stamps' ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                    id="card-stamps">
                    <input type="radio"
                           name="loyalty_type"
                           value="stamps"
                           class="form-check-input mt-0 flex-shrink-0"
                           style="width:1.25rem;height:1.25rem;"
                           {{ $selected === 'stamps' ? 'checked' : '' }}
                           onchange="highlightCard(this)">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-grid-3x3 text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-semibold fs-5">Stamps</div>
                        <div class="text-muted small">Customers collect stamps and redeem rewards.</div>
                    </div>
                </div>
            </label>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    Save and Continue <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function highlightCard(radio) {
    document.querySelectorAll('[id^="card-"]').forEach(function(card) {
        card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    var card = radio.closest('[id^="card-"]');
    if (card) {
        card.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    }
}
</script>
@endsection
