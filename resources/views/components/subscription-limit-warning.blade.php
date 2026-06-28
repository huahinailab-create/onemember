@props([
    'level',        // 'normal' | 'warning' | 'limit_reached'
    'feature',      // human-readable label, e.g. 'member'
    'percentage' => null,
    'used'       => null,
    'limit'      => null,
])

@if ($level === 'warning')
    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
        <div>
            <span class="fw-medium">You have used {{ $percentage }}% of your {{ $feature }} limit.</span>
            @if ($limit !== null)
                <span class="text-muted small d-block mt-1">{{ number_format($used) }} of {{ number_format($limit) }} used.</span>
            @endif
        </div>
    </div>
@elseif ($level === 'limit_reached')
    <div class="alert alert-info d-flex align-items-start gap-2 mb-3" role="alert">
        <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-medium">You've reached your current plan limit.</div>
            <p class="mb-2 mt-1 small">Upgrade your subscription to continue growing your business.</p>
            <button type="button" class="btn btn-sm btn-outline-primary" disabled>
                <i class="bi bi-arrow-up-circle me-1"></i>Upgrade Plan
            </button>
        </div>
    </div>
@endif
