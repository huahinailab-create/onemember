@php
    /** CUSTOMER-001C — one merchant relationship card. */
    $member    = $link->member;
    $merchant  = $member->merchant;
    $programme = $merchant->loyaltyPrograms->first();
    $isStamps  = $programme?->type === \App\Enums\LoyaltyProgramType::Stamps;
    $compact   = $compact ?? false;
@endphp
<a href="{{ route('customer.wallet.membership', $member->public_uuid) }}" class="customer-membership-card">
    <div class="customer-membership-logo">
        @if ($merchant->logo_path)
            <img src="{{ Storage::disk('public')->url($merchant->logo_path) }}" alt="" loading="lazy">
        @else
            <span aria-hidden="true">{{ $merchant->initials() }}</span>
        @endif
    </div>
    <div class="customer-membership-body">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold">{{ $merchant->displayName() }}</span>
            @if ($member->isActive())
                <span class="badge bg-success">{{ __('customer_wallet.status_active') }}</span>
            @else
                <span class="badge bg-secondary">{{ $member->status->label() }}</span>
            @endif
        </div>
        <div class="small text-muted">
            {{ __('customer_wallet.member_since', ['date' => ($member->joined_at ?? $member->created_at)->translatedFormat('M Y')]) }}
            @if ($member->last_activity_at)
                · {{ __('customer_wallet.last_visit', ['date' => $member->last_activity_at->translatedFormat('j M Y')]) }}
            @endif
        </div>
        <div class="customer-membership-points">
            <span class="customer-membership-points-value">{{ number_format($member->total_points) }}</span>
            {{ $isStamps ? __('customer_wallet.unit_stamps') : __('customer_wallet.unit_points') }}
            @unless ($compact)
                <span class="text-muted small ms-2">{{ trans_choice('customer_wallet.rewards_count', $rewardCounts[$member->id] ?? 0, ['count' => $rewardCounts[$member->id] ?? 0]) }}</span>
            @endunless
        </div>
    </div>
    <i class="bi bi-chevron-right customer-membership-chevron" aria-hidden="true"></i>
</a>
