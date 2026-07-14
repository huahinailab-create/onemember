{{-- MR-003 — guided launch journey: after a merchant completes a launch
     step (controller flashes `launch_step`), explain why the step matters
     and recommend exactly ONE next action (LaunchChecklistService — fixed
     order, no AI, no randomness). When the step completed the whole
     checklist, hand off to the dashboard celebration instead.
     Rendered once, globally, right after <x-ui.flash /> in the app layout. --}}
@php
    $launchStep     = session('launch_step');
    $launchMerchant = auth()->user()?->merchant;
@endphp

@if ($launchStep && $launchMerchant)
    @php
        $launchService   = app(\App\Services\LaunchChecklistService::class);
        $launchChecklist = $launchService->for($launchMerchant);
        $launchNext      = $launchService->nextAction($launchMerchant, $launchChecklist);
    @endphp

    <div class="card mb-4" role="status" style="border-left:4px solid var(--om-pink);">
        <div class="card-body py-3">
            <p class="text-muted small mb-2">
                <i class="bi bi-lightbulb me-1" aria-hidden="true"></i>{{ __('launch_check.why_' . $launchStep) }}
            </p>
            @if ($launchNext)
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="flex-grow-1">
                        <span class="text-muted small text-uppercase fw-semibold">{{ __('launch_check.next_title') }}</span>
                        <div class="fw-semibold">{{ __($launchNext['action_key']) }}</div>
                    </div>
                    <a href="{{ $launchNext['url'] }}" class="btn btn-primary btn-sm text-nowrap">
                        {{ __('launch_check.next_cta') }}
                    </a>
                </div>
            @else
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="flex-grow-1">
                        <span aria-hidden="true">🎉</span>
                        <span class="fw-semibold">{{ __('launch_check.launch_ready') }}</span>
                        <span class="text-muted small">— {{ __('launch_check.celebrate_body') }}</span>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm text-nowrap">
                        {{ __('launch_check.celebrate_dashboard_cta') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
