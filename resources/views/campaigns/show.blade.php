<x-app-layout>
    <x-slot name="title">{{ $campaign->name }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Campaigns</x-slot>

    @php $isArchived = $campaign->trashed(); @endphp

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="mb-1">
                <a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Back to Campaigns
                </a>
            </div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                {{ $campaign->name }}
                @if ($isArchived)
                    <span class="badge bg-danger fs-6 fw-normal">Archived</span>
                @else
                    <span class="{{ $campaign->status->badgeClass() }} fs-6 fw-normal">
                        {{ $campaign->status->label() }}
                    </span>
                @endif
            </h1>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            @if ($isArchived)
                <button type="button" class="btn btn-outline-success disabled" title="Coming in a future sprint">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Campaign
                    <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Coming Soon</span>
                </button>
            @else
                @if ($campaign->status === \App\Enums\CampaignStatus::Active)
                    <form method="POST" action="{{ route('campaigns.pause', $campaign) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning">
                            <i class="bi bi-pause-circle me-1"></i>Pause Campaign
                        </button>
                    </form>
                @endif
                <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#archiveModal">
                    <i class="bi bi-archive me-1"></i>Archive Campaign
                </button>
            @endif
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Campaign Settings Card (edit form) --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-star text-primary"></i>
                    <span class="fw-semibold">Campaign Settings</span>
                    @if ($isArchived)
                        <span class="badge bg-danger ms-auto" style="font-size:.65rem;">Read-only</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('campaigns.update', $campaign) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Campaign Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label form-label-sm">
                                Campaign Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name', $campaign->name) }}"
                                   maxlength="150"
                                   required
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campaign Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label form-label-sm">
                                Campaign Type <span class="text-danger">*</span>
                            </label>
                            <select id="type"
                                    name="type"
                                    class="form-select form-select-sm @error('type') is-invalid @enderror"
                                    {{ $isArchived ? 'disabled' : '' }}>
                                <option value="points" {{ old('type', $campaign->type->value) === 'points' ? 'selected' : '' }}>
                                    Points — customers earn points for every purchase
                                </option>
                                <option value="stamps" {{ old('type', $campaign->type->value) === 'stamps' ? 'selected' : '' }}>
                                    Stamp Card — customers collect stamps per visit
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label form-label-sm">Description</label>
                            <textarea id="description"
                                      name="description"
                                      class="form-control form-control-sm @error('description') is-invalid @enderror"
                                      rows="3"
                                      maxlength="1000"
                                      {{ $isArchived ? 'disabled' : '' }}>{{ old('description', $campaign->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label form-label-sm">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    class="form-select form-select-sm @error('status') is-invalid @enderror"
                                    {{ $isArchived ? 'disabled' : '' }}>
                                <option value="draft" {{ old('status', $campaign->status?->value) === 'draft' ? 'selected' : '' }}>
                                    Draft — not yet active
                                </option>
                                <option value="active" {{ old('status', $campaign->status?->value) === 'active' ? 'selected' : '' }}>
                                    Active — open for earning
                                </option>
                                <option value="paused" {{ old('status', $campaign->status?->value) === 'paused' ? 'selected' : '' }}>
                                    Paused — temporarily suspended
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Read-only meta --}}
                        <dl class="row mb-0 small" style="row-gap:.5rem;">
                            <dt class="col-5 text-muted fw-normal">Created</dt>
                            <dd class="col-7 mb-0">{{ $campaign->created_at->format('d M Y') }}</dd>

                            <dt class="col-5 text-muted fw-normal">Last Updated</dt>
                            <dd class="col-7 mb-0">{{ $campaign->updated_at->format('d M Y, H:i') }}</dd>
                        </dl>

                    </div>

                    @unless ($isArchived)
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>Save Changes
                            </button>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary btn-sm">
                                Discard
                            </a>
                        </div>
                    @endunless

                </form>
            </div>
        </div>

        {{-- Info summary card --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span class="fw-semibold">Summary</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0" style="row-gap:.75rem;">
                        <dt class="col-5 text-muted fw-normal">Campaign Name</dt>
                        <dd class="col-7 mb-0 fw-medium">{{ $campaign->name }}</dd>

                        <dt class="col-5 text-muted fw-normal">Type</dt>
                        <dd class="col-7 mb-0 d-flex align-items-center gap-1">
                            <i class="bi {{ $campaign->type->icon() }} text-muted" style="font-size:.875rem;"></i>
                            {{ $campaign->type->label() }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7 mb-0">
                            @if ($isArchived)
                                <span class="badge bg-danger">Archived</span>
                            @else
                                <span class="{{ $campaign->status->badgeClass() }}">{{ $campaign->status->label() }}</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted fw-normal">Description</dt>
                        <dd class="col-7 mb-0">
                            @if ($campaign->description)
                                <span style="white-space:pre-line;">{{ $campaign->description }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>

    {{-- Workspace Tabs --}}
    <div class="card">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs px-3" id="campaignTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-rules" data-bs-toggle="tab"
                            data-bs-target="#pane-rules" type="button" role="tab">
                        <i class="bi bi-sliders me-1"></i>Rules
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-rewards" data-bs-toggle="tab"
                            data-bs-target="#pane-rewards" type="button" role="tab">
                        <i class="bi bi-gift me-1"></i>Rewards
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-transactions" data-bs-toggle="tab"
                            data-bs-target="#pane-transactions" type="button" role="tab">
                        <i class="bi bi-arrow-left-right me-1"></i>Transactions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-analytics" data-bs-toggle="tab"
                            data-bs-target="#pane-analytics" type="button" role="tab">
                        <i class="bi bi-bar-chart-line me-1"></i>Analytics
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="campaignTabsContent">
            @foreach ([
                'pane-rules'        => ['icon' => 'bi-sliders',           'label' => 'Rules'],
                'pane-rewards'      => ['icon' => 'bi-gift',              'label' => 'Rewards'],
                'pane-transactions' => ['icon' => 'bi-arrow-left-right',  'label' => 'Transactions'],
                'pane-analytics'    => ['icon' => 'bi-bar-chart-line',    'label' => 'Analytics'],
            ] as $paneId => $meta)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }} text-center py-5"
                     id="{{ $paneId }}" role="tabpanel">
                    <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi {{ $meta['icon'] }} text-primary"></i>
                    </div>
                    <h6 class="fw-semibold mb-1">{{ $meta['label'] }} — Coming Soon</h6>
                    <p class="text-muted mb-0 small">This feature will be available in a future sprint.</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Archive Modal --}}
    @unless ($isArchived)
        <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger" id="archiveModalLabel">
                            <i class="bi bi-archive me-2"></i>Archive Campaign
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Are you sure you want to archive <strong>{{ $campaign->name }}</strong>?</p>
                        <p class="text-muted small mb-0">
                            This campaign will be removed from your active list. Archiving does not delete any data.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('campaigns.archive', $campaign) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-archive me-1"></i>Archive Campaign
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endunless

</x-app-layout>
