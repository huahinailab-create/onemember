{{-- MR-001 — merchant health card. Green/amber/red per dimension, computed
     deterministically by LaunchChecklistService::health(); this component
     only renders. Usage: <x-launch.health-card :health="$merchantHealth" /> --}}
@props(['health'])

@php
    $badge = [
        'green' => 'text-bg-success',
        'amber' => 'text-bg-warning',
        'red'   => 'text-bg-danger',
    ];
    // Launch % row follows fixed thresholds: 100 green · ≥50 amber · <50 red.
    $launchStatus = $health['percent'] === 100 ? 'green' : ($health['percent'] >= 50 ? 'amber' : 'red');
@endphp

<div {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span class="fw-semibold"><i class="bi bi-heart-pulse me-2 text-primary" aria-hidden="true"></i>{{ __('launch_check.health_title') }}</span>
        @if ($health['launch_ready'])
            <span class="badge text-bg-success"><i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>{{ __('launch_check.launch_ready') }}</span>
        @endif
    </div>
    <ul class="list-group list-group-flush">
        @foreach ($health['rows'] as $row)
            <li class="list-group-item d-flex align-items-center justify-content-between gap-2 py-2">
                <a href="{{ $row['url'] }}" class="text-decoration-none text-reset">{{ __($row['label_key']) }}</a>
                <span class="d-flex align-items-center gap-2">
                    @if ($row['value'] !== null)
                        <span class="text-muted small">{{ $row['value'] }}</span>
                    @endif
                    <span class="badge rounded-pill {{ $badge[$row['status']] }}">{{ __('launch_check.status_' . $row['status']) }}</span>
                </span>
            </li>
        @endforeach
        <li class="list-group-item py-2">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                <span>{{ __('launch_check.health_launch') }}</span>
                <span class="d-flex align-items-center gap-2">
                    <span class="text-muted small">{{ $health['percent'] }}%</span>
                    <span class="badge rounded-pill {{ $badge[$launchStatus] }}">{{ __('launch_check.status_' . $launchStatus) }}</span>
                </span>
            </div>
            <x-ui.progress-bar :percent="$health['percent']" color="navy" height="6px" />
        </li>
    </ul>
</div>
