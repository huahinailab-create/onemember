<x-app-layout>
    <x-slot name="title">{{ $reward->name }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Campaigns</x-slot>

    @php $isArchived = $reward->trashed(); @endphp

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="mb-1">
                <a href="{{ route('campaigns.show', $campaign) . '?active_tab=rewards' }}"
                   class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Back to {{ $campaign->name }}
                </a>
            </div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                {{ $reward->name }}
                @if ($isArchived)
                    <span class="badge bg-danger fs-6 fw-normal">Archived</span>
                @else
                    <span class="{{ $reward->status->badgeClass() }} fs-6 fw-normal">
                        {{ $reward->status->label() }}
                    </span>
                @endif
            </h1>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            @if ($isArchived)
                <span class="btn btn-outline-secondary disabled">
                    <i class="bi bi-lock me-1"></i>Archived
                </span>
            @else
                <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#archiveModal">
                    <i class="bi bi-archive me-1"></i>Archive Reward
                </button>
            @endif
            <a href="{{ route('campaigns.show', $campaign) . '?active_tab=rewards' }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-3">

        {{-- Edit form --}}
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-gift text-primary"></i>
                    <span class="fw-semibold">Reward Details</span>
                    @if ($isArchived)
                        <span class="badge bg-danger ms-auto" style="font-size:.65rem;">Read-only</span>
                    @endif
                </div>

                <form method="POST"
                      action="{{ route('campaigns.rewards.update', [$campaign, $reward]) }}"
                      novalidate
                      x-data="{ unlimited: {{ $reward->quantity_available === null ? 'true' : 'false' }} }">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Reward Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label form-label-sm">
                                Reward Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name', $reward->name) }}"
                                   maxlength="100"
                                   required
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reward Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label form-label-sm">
                                Reward Type <span class="text-danger">*</span>
                            </label>
                            <select id="type"
                                    name="type"
                                    class="form-select form-select-sm @error('type') is-invalid @enderror"
                                    {{ $isArchived ? 'disabled' : '' }}>
                                @foreach (\App\Enums\RewardType::cases() as $case)
                                    <option value="{{ $case->value }}"
                                            {{ old('type', $reward->type->value) === $case->value ? 'selected' : '' }}>
                                        {{ $case->label() }}
                                    </option>
                                @endforeach
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
                                      rows="2"
                                      maxlength="1000"
                                      {{ $isArchived ? 'disabled' : '' }}>{{ old('description', $reward->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Points Required / Stamp info --}}
                        @if ($campaign->type->value === 'points')
                            <div class="mb-3">
                                <label for="points_required" class="form-label form-label-sm">
                                    Points Required <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm" style="max-width:220px;">
                                    <input type="number"
                                           id="points_required"
                                           name="points_required"
                                           class="form-control @error('points_required') is-invalid @enderror"
                                           min="1"
                                           value="{{ old('points_required', $reward->points_required) }}"
                                           {{ $isArchived ? 'disabled' : '' }}
                                           required>
                                    <span class="input-group-text">pts</span>
                                    @error('points_required')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            @php $stampsRequired = $campaign->settings['stamps_required'] ?? '—'; @endphp
                            <div class="mb-3">
                                <label class="form-label form-label-sm">Stamp Requirement</label>
                                <div class="form-control form-control-sm bg-light" style="cursor:default;">
                                    {{ $stampsRequired }} stamps (campaign completion)
                                </div>
                                <div class="form-text">Awarded automatically when the stamp card is complete.</div>
                            </div>
                        @endif

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input type="checkbox"
                                       id="unlimited"
                                       name="unlimited"
                                       value="1"
                                       class="form-check-input"
                                       x-model="unlimited"
                                       {{ $reward->quantity_available === null ? 'checked' : '' }}
                                       {{ $isArchived ? 'disabled' : '' }}>
                                <label class="form-check-label form-label-sm" for="unlimited">
                                    Unlimited Quantity
                                </label>
                            </div>

                            <div x-show="!unlimited" x-cloak>
                                <label for="quantity_available" class="form-label form-label-sm">
                                    Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       id="quantity_available"
                                       name="quantity_available"
                                       class="form-control form-control-sm @error('quantity_available') is-invalid @enderror"
                                       min="1"
                                       value="{{ old('quantity_available', $reward->quantity_available) }}"
                                       {{ $isArchived ? 'disabled' : '' }}
                                       style="max-width:180px;">
                                @error('quantity_available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                                <option value="draft" {{ old('status', $reward->status?->value) === 'draft' ? 'selected' : '' }}>
                                    Draft — not yet visible to customers
                                </option>
                                <option value="active" {{ old('status', $reward->status?->value) === 'active' ? 'selected' : '' }}>
                                    Active — available for redemption
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Internal Notes --}}
                        <div class="mb-3">
                            <label for="internal_notes" class="form-label form-label-sm">Internal Notes</label>
                            <textarea id="internal_notes"
                                      name="internal_notes"
                                      class="form-control form-control-sm @error('internal_notes') is-invalid @enderror"
                                      rows="2"
                                      maxlength="1000"
                                      {{ $isArchived ? 'disabled' : '' }}>{{ old('internal_notes', $reward->internal_notes) }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Visible to you only.</div>
                        </div>

                    </div>

                    @unless ($isArchived)
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>Save Changes
                            </button>
                            <a href="{{ route('campaigns.rewards.show', [$campaign, $reward]) }}"
                               class="btn btn-outline-secondary btn-sm">Discard</a>
                        </div>
                    @endunless

                </form>
            </div>
        </div>

        {{-- Meta card --}}
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span class="fw-semibold">Details</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small" style="row-gap:.75rem;">
                        <dt class="col-5 text-muted fw-normal">Campaign</dt>
                        <dd class="col-7 mb-0">
                            <a href="{{ route('campaigns.show', $campaign) }}"
                               class="text-decoration-none">{{ $campaign->name }}</a>
                        </dd>

                        <dt class="col-5 text-muted fw-normal">Campaign Type</dt>
                        <dd class="col-7 mb-0">{{ $campaign->type->label() }}</dd>

                        <dt class="col-5 text-muted fw-normal">Created</dt>
                        <dd class="col-7 mb-0">{{ $reward->created_at->format('d M Y') }}</dd>

                        <dt class="col-5 text-muted fw-normal">Last Updated</dt>
                        <dd class="col-7 mb-0">{{ $reward->updated_at->format('d M Y, H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>

    {{-- Archive Modal --}}
    @unless ($isArchived)
        <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger" id="archiveModalLabel">
                            <i class="bi bi-archive me-2"></i>Archive Reward
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Are you sure you want to archive <strong>{{ $reward->name }}</strong>?</p>
                        <p class="text-muted small mb-0">
                            This reward will be removed from your active list. Archiving does not delete any data.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('campaigns.rewards.archive', [$campaign, $reward]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-archive me-1"></i>Archive Reward
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endunless

</x-app-layout>
