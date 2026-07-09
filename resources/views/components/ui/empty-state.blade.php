{{-- Design System: standard empty state for lists/tables.
     MR-002: optional help-topic renders a contextual Help Center link
     under the CTA slot, so every empty state can answer "how do I start?".
     Usage: <x-ui.empty-state icon="bi-inbox" :title="__('...')" :body="__('...')"
                help-topic="members.index"><a .../></x-ui.empty-state> --}}
@props(['icon' => 'bi-inbox', 'title', 'body' => null, 'helpTopic' => null])

<div {{ $attributes->merge(['class' => 'text-center text-muted py-5 px-3']) }}>
    <i class="bi {{ $icon }} fs-1 d-block mb-2" aria-hidden="true"></i>
    <div class="fw-semibold">{{ $title }}</div>
    @if ($body)
        <p class="mb-0 mt-1 mx-auto" style="font-size:0.875rem;max-width:380px;">{{ $body }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
    @if ($helpTopic)
        <div class="mt-2">
            <a href="{{ route('help.context', $helpTopic) }}" class="small text-decoration-none" data-help-topic="{{ $helpTopic }}">
                <i class="bi bi-question-circle me-1" aria-hidden="true"></i>{{ __('help.empty_state_link') }}
            </a>
        </div>
    @endif
</div>
