{{-- MR-001 — reusable, tenant-scoped merchant launch checklist.
     MR-003 — encouraging progress copy while under way; a calm 🎉
     celebration with quick actions once every step is complete.
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
        @if ($checklist['launch_ready'])
            {{-- MR-003 first-launch celebration (no animation — calm and proud) --}}
            <div class="text-center pt-1 pb-2">
                <div class="fs-1 mb-1" aria-hidden="true">🎉</div>
                <h5 class="fw-bold mb-1">{{ __('launch_check.celebrate_heading') }}</h5>
                <p class="text-muted mb-3 mx-auto" style="max-width:420px;">{{ __('launch_check.celebrate_body') }}</p>
                @php $storefrontItem = collect($checklist['items'])->firstWhere('key', 'storefront'); @endphp
                <div class="row g-2 justify-content-center">
                    @if ($storefrontItem)
                        <div class="col-6 col-lg-3">
                            <a href="{{ $storefrontItem['url'] }}" target="_blank" rel="noopener"
                               class="btn btn-primary w-100 h-100 py-2">
                                <i class="bi bi-shop d-block fs-5 mb-1" aria-hidden="true"></i>
                                <span class="small">{{ __('launch_check.qa_storefront') }}</span>
                            </a>
                        </div>
                    @endif
                    <div class="col-6 col-lg-3">
                        <a href="{{ route('launch-kit.poster') }}" class="btn btn-outline-primary w-100 h-100 py-2">
                            <i class="bi bi-qr-code d-block fs-5 mb-1" aria-hidden="true"></i>
                            <span class="small">{{ __('launch_check.qa_poster') }}</span>
                        </a>
                    </div>
                    <div class="col-6 col-lg-3">
                        <a href="{{ route('members.create') }}" class="btn btn-outline-primary w-100 h-100 py-2">
                            <i class="bi bi-person-plus d-block fs-5 mb-1" aria-hidden="true"></i>
                            <span class="small">{{ __('launch_check.qa_member') }}</span>
                        </a>
                    </div>
                    <div class="col-6 col-lg-3">
                        <a href="{{ route('help.index') }}" class="btn btn-outline-primary w-100 h-100 py-2">
                            <i class="bi bi-book d-block fs-5 mb-1" aria-hidden="true"></i>
                            <span class="small">{{ __('launch_check.qa_guide') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <x-ui.progress-bar :percent="$checklist['percent']" color="pink" height="8px" class="mb-1" />
            <p class="text-muted small mb-3">
                {{ trans_choice('launch_check.steps_left', $checklist['total'] - $checklist['done'], ['count' => $checklist['total'] - $checklist['done']]) }}
            </p>

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
                            <span class="visually-hidden">({{ __('launch_check.sr_done') }})</span>
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
