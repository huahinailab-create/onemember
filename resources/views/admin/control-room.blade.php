<x-admin-layout title="Control Room">

    @php
        // Status → badge/colour mapping for the control room.
        $badge = fn ($s) => match ($s) {
            'healthy' => 'bg-success',
            'warning' => 'bg-warning text-dark',
            'critical' => 'bg-danger',
            default   => 'bg-secondary',   // manual
        };
        $badgeLabel = fn ($s) => match ($s) {
            'healthy' => 'Healthy',
            'warning' => 'Warning',
            'critical' => 'Critical',
            default   => 'Manual check',
        };
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h5 class="fw-600 mb-1" style="color:#1A2E5A;"><i class="bi bi-hdd-network me-2" style="color:#FF1585;"></i>OneMember Control Room</h5>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Internal production health and service dependencies. Checked {{ $checkedAt->format('d M Y H:i') }}.</p>
        </div>
    </div>

    {{-- Config warnings --}}
    @if (! empty($warnings))
        <div class="stat-card card p-3 mb-4" style="border-left:4px solid #DC2626;">
            <h6 class="fw-600 mb-2" style="color:#DC2626;font-size:0.85rem;"><i class="bi bi-exclamation-triangle me-1"></i>Config Warnings</h6>
            <ul class="mb-0 ps-3" style="font-size:0.85rem;">
                @foreach ($warnings as $warning)
                    <li class="text-muted">{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Internal checks --}}
        <div class="col-12 col-lg-7">
            <div class="stat-card card">
                <div class="p-3 border-bottom">
                    <h6 class="fw-600 mb-0" style="color:#1A2E5A;font-size:0.85rem;"><i class="bi bi-cpu me-2" style="color:#FF1585;"></i>Internal Status</h6>
                </div>
                <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        @foreach ($internal as $row)
                            <tr>
                                <td class="ps-3 fw-medium" style="min-width:130px;">{{ $row['label'] }}</td>
                                <td style="min-width:110px;"><span class="badge {{ $badge($row['status']) }}">{{ $badgeLabel($row['status']) }}</span></td>
                                <td class="pe-3 text-muted" style="font-size:0.82rem;">{{ $row['detail'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        {{-- Feature flags --}}
        <div class="col-12 col-lg-5">
            <div class="stat-card card h-100">
                <div class="p-3 border-bottom">
                    <h6 class="fw-600 mb-0" style="color:#1A2E5A;font-size:0.85rem;"><i class="bi bi-toggles me-2" style="color:#FF1585;"></i>Feature Flags</h6>
                </div>
                <div class="p-3">
                    @forelse ($featureFlags as $flag)
                        <div class="d-flex align-items-center justify-content-between py-1">
                            <span class="fw-medium" style="font-size:0.85rem;">{{ $flag['key'] }}</span>
                            <span class="badge {{ $flag['status'] === 'healthy' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $flag['status'] === 'healthy' ? 'On' : 'Off' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0" style="font-size:0.85rem;">No feature flags configured.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- External services --}}
    <div class="stat-card card mt-4">
        <div class="p-3 border-bottom">
            <h6 class="fw-600 mb-0" style="color:#1A2E5A;font-size:0.85rem;"><i class="bi bi-diagram-3 me-2" style="color:#FF1585;"></i>External Services</h6>
            <p class="text-muted mb-0 mt-1" style="font-size:0.78rem;">Phase 1: presence-only detection. Rows without local config require a manual dashboard check — no external calls are made.</p>
        </div>
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.85rem;">
                <thead>
                    <tr style="font-size:0.72rem;color:#6B7280;text-transform:uppercase;letter-spacing:0.04em;">
                        <th class="ps-3">Service</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Required action</th>
                        <th class="pe-3">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($external as $svc)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $svc['service'] }}</td>
                            <td class="text-muted">{{ $svc['purpose'] }}</td>
                            <td><span class="badge {{ $badge($svc['status']) }}">{{ $badgeLabel($svc['status']) }}</span></td>
                            <td class="text-muted">{{ $svc['action'] }}</td>
                            <td class="pe-3 text-muted">{{ $svc['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
