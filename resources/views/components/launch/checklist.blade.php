{{-- MR-001 — reusable, tenant-scoped merchant launch checklist.
     Data comes from LaunchChecklistService::for() / ::nextAction();
     the component renders whatever it is given (no queries here).
     Usage: <x-launch.checklist :checklist="$launchChecklist" :next-action="$launchNextAction" /> --}}
@props(['checklist', 'nextAction' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span class="fw-semibold"><i class="bi bi-rocket-takeoff me-2 text-primary" aria-hidden="true"></i>{{ __('launch_check.title') }}</span>
        @if ($checklist['launch_ready'])
            <span class="badge text-bg-success"><i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>{{ __('launch_check.launch_ready') }}</span>
        @else
            <span class="badge bg-primary">{{ $checklist['done'] }}/{{ $checklist['total'] }} &middot; {{ $checklist['percent'] }}%</span>
        @endif
    </div>
    <div class="card-body">
        <x-ui.progress-bar :percent="$checklist['percent']" color="pink" height="8px" class="mb-3" />

        @if ($checklist['launch_ready'])
            <p class="text-muted small mb-0">{{ __('launch_check.launch_ready_body') }}</p>
        @else
            @if ($nextAction)
                {{-- The ONE next recommended action (deterministic — first incomplete item) --}}
                <div class="d-flex align-items-center gap-3 p-3 border rounded mb-3" style="background:var(--om-surface, #f8f9fa);">
                    <i class="bi bi-arrow-right-circle-fill text-primary fs-4 flex-shrink-0" aria-hidden="true"></i>
                    <div class="flex-grow-1">
                        <div class="text-muted small text-uppercase fw-semibold">{{ __('launch_check.next_title') }}</div>
                        <div class="fw-semibold">{{ __($nextAction['action_key']) }}</div>
                    </div>
                    <a href="{{ $nextAction['url'] }}" class="btn btn-primary btn-sm text-nowrap flex-shrink-0">
                        {{ __('launch_check.next_cta') }}
                    </a>
                </div>
            @endif

            <ul class="list-unstyled mb-0">
                @foreach ($checklist['items'] as $item)
                    <li class="d-flex align-items-center gap-2 py-1">
                        @if ($item['done'])
                            <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
                            <span class="text-muted text-decoration-line-through">{{ __($item['label_key']) }}</span>
                        @else
                            <i class="bi bi-circle text-muted" aria-hidden="true"></i>
                            <a href="{{ $item['url'] }}" class="text-decoration-none">{{ __($item['label_key']) }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
