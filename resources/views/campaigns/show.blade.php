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
        @php $settings = $campaign->settings ?? []; @endphp

        <script>
        function campaignConfig(data) {
            return {
                ...data,
                get expirationText() {
                    if (!this.expirationEnabled) return 'Never';
                    const d = parseInt(this.expirationDuration) || 0;
                    if (!d) return 'Not set';
                    return d + ' ' + this.expirationUnit;
                },
                get birthdayText() {
                    if (!this.birthdayEnabled) return 'None';
                    const p = parseInt(this.birthdayPoints) || 0;
                    if (!p) return 'Not set';
                    return p + ' Point' + (p === 1 ? '' : 's');
                },
            };
        }
        </script>

        <div class="tab-content" id="campaignTabsContent">

            {{-- ── Rules Tab ──────────────────────────────────── --}}
            <div class="tab-pane fade show active" id="pane-rules" role="tabpanel"
                 x-data="campaignConfig(@json([
                     'type'               => $campaign->type->value,
                     'currency'           => $campaign->merchant->currency ?? 'THB',
                     'campaignName'       => $campaign->name,
                     'campaignStatus'     => $isArchived ? 'Archived' : $campaign->status->label(),
                     'spendAmount'        => (int) ($settings['spend_amount']         ?? 100),
                     'pointsAwarded'      => (int) ($settings['points_awarded']        ?? 1),
                     'expirationEnabled'  => (bool)($settings['expiration_enabled']    ?? false),
                     'expirationDuration' => $settings['expiration_duration']          ?? '',
                     'expirationUnit'     => $settings['expiration_unit']              ?? 'months',
                     'birthdayEnabled'    => (bool)($settings['birthday_bonus_enabled'] ?? false),
                     'birthdayPoints'     => $settings['birthday_bonus_points']        ?? '',
                     'stampsRequired'     => (int) ($settings['stamps_required']       ?? 10),
                     'rewardDescription'  => $settings['reward_description']           ?? '',
                 ]))">

                <div class="row g-0">

                    {{-- ── Configuration Form ──────────────────── --}}
                    <div class="col-12 col-lg-7 p-4 border-end">

                        @if ($isArchived)
                            <div class="alert alert-secondary py-2 px-3 mb-4 small">
                                <i class="bi bi-lock me-1"></i>This campaign is archived. Rules are read-only.
                            </div>
                        @endif

                        @if ($campaign->type->value === 'points')

                            {{-- ── Points Configuration ─────────── --}}
                            <form method="POST" action="{{ route('campaigns.configure', $campaign) }}" novalidate>
                                @csrf
                                @method('PUT')

                                <h6 class="fw-semibold mb-3 text-primary">
                                    <i class="bi bi-star-fill me-1"></i>Points Rules
                                </h6>

                                {{-- Earn Method (read-only in MVP) --}}
                                <div class="mb-3">
                                    <label class="form-label form-label-sm">Earn Method</label>
                                    <select class="form-select form-select-sm bg-light" disabled>
                                        <option selected>Spend Amount</option>
                                    </select>
                                    <div class="form-text">Only Spend Amount is supported in this version.</div>
                                </div>

                                {{-- Earn Rate --}}
                                <div class="mb-4">
                                    <label class="form-label form-label-sm fw-medium">Earn Rate</label>
                                    <div class="row g-2 align-items-end">
                                        <div class="col">
                                            <label for="spend_amount" class="form-label form-label-sm">
                                                Spend Amount <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number"
                                                       id="spend_amount"
                                                       name="spend_amount"
                                                       class="form-control @error('spend_amount') is-invalid @enderror"
                                                       min="1"
                                                       step="1"
                                                       x-model.number="spendAmount"
                                                       value="{{ old('spend_amount', $settings['spend_amount'] ?? 100) }}"
                                                       {{ $isArchived ? 'disabled' : '' }}
                                                       required>
                                                <span class="input-group-text">{{ $campaign->merchant->currency ?? 'THB' }}</span>
                                                @error('spend_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-auto pb-1 text-muted fw-bold">→</div>
                                        <div class="col">
                                            <label for="points_awarded" class="form-label form-label-sm">
                                                Points Awarded <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number"
                                                       id="points_awarded"
                                                       name="points_awarded"
                                                       class="form-control @error('points_awarded') is-invalid @enderror"
                                                       min="1"
                                                       step="1"
                                                       x-model.number="pointsAwarded"
                                                       value="{{ old('points_awarded', $settings['points_awarded'] ?? 1) }}"
                                                       {{ $isArchived ? 'disabled' : '' }}
                                                       required>
                                                <span class="input-group-text">pt</span>
                                                @error('points_awarded')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">

                                {{-- Points Expiration --}}
                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input type="checkbox"
                                               id="expiration_enabled"
                                               name="expiration_enabled"
                                               value="1"
                                               class="form-check-input"
                                               x-model="expirationEnabled"
                                               {{ ($settings['expiration_enabled'] ?? false) ? 'checked' : '' }}
                                               {{ $isArchived ? 'disabled' : '' }}>
                                        <label class="form-check-label fw-medium" for="expiration_enabled">
                                            Enable Points Expiration
                                        </label>
                                    </div>

                                    <div x-show="expirationEnabled" x-cloak class="mt-2 ms-4">
                                        <div class="row g-2">
                                            <div class="col-5">
                                                <label for="expiration_duration" class="form-label form-label-sm">Duration</label>
                                                <input type="number"
                                                       id="expiration_duration"
                                                       name="expiration_duration"
                                                       class="form-control form-control-sm @error('expiration_duration') is-invalid @enderror"
                                                       min="1"
                                                       x-model.number="expirationDuration"
                                                       value="{{ old('expiration_duration', $settings['expiration_duration'] ?? '') }}"
                                                       {{ $isArchived ? 'disabled' : '' }}
                                                       placeholder="e.g. 24">
                                                @error('expiration_duration')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-5">
                                                <label for="expiration_unit" class="form-label form-label-sm">Unit</label>
                                                <select id="expiration_unit"
                                                        name="expiration_unit"
                                                        class="form-select form-select-sm"
                                                        x-model="expirationUnit"
                                                        {{ $isArchived ? 'disabled' : '' }}>
                                                    <option value="months" {{ ($settings['expiration_unit'] ?? 'months') === 'months' ? 'selected' : '' }}>Months</option>
                                                    <option value="years"  {{ ($settings['expiration_unit'] ?? '') === 'years'  ? 'selected' : '' }}>Years</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-text mt-2">
                                        <i class="bi bi-lightbulb text-warning me-1"></i>
                                        Recommended: No expiration or a long expiration period (for example 2 years) to encourage customer loyalty.
                                    </div>
                                </div>

                                <hr class="my-3">

                                {{-- Birthday Bonus --}}
                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input type="checkbox"
                                               id="birthday_bonus_enabled"
                                               name="birthday_bonus_enabled"
                                               value="1"
                                               class="form-check-input"
                                               x-model="birthdayEnabled"
                                               {{ ($settings['birthday_bonus_enabled'] ?? false) ? 'checked' : '' }}
                                               {{ $isArchived ? 'disabled' : '' }}>
                                        <label class="form-check-label fw-medium" for="birthday_bonus_enabled">
                                            Enable Birthday Bonus
                                        </label>
                                    </div>

                                    <div x-show="birthdayEnabled" x-cloak class="mt-2 ms-4">
                                        <label for="birthday_bonus_points" class="form-label form-label-sm">
                                            Bonus Points <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-sm" style="max-width:180px;">
                                            <input type="number"
                                                   id="birthday_bonus_points"
                                                   name="birthday_bonus_points"
                                                   class="form-control @error('birthday_bonus_points') is-invalid @enderror"
                                                   min="1"
                                                   x-model.number="birthdayPoints"
                                                   value="{{ old('birthday_bonus_points', $settings['birthday_bonus_points'] ?? '') }}"
                                                   {{ $isArchived ? 'disabled' : '' }}
                                                   placeholder="e.g. 100">
                                            <span class="input-group-text">pts</span>
                                            @error('birthday_bonus_points')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @unless ($isArchived)
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check-lg me-1"></i>Save Rules
                                    </button>
                                @endunless

                            </form>

                        @else

                            {{-- ── Stamp Card Configuration ──────── --}}
                            <form method="POST" action="{{ route('campaigns.configure', $campaign) }}" novalidate>
                                @csrf
                                @method('PUT')

                                <h6 class="fw-semibold mb-3 text-primary">
                                    <i class="bi bi-grid-3x3-gap-fill me-1"></i>Stamp Card Rules
                                </h6>

                                <div class="mb-3">
                                    <label for="stamps_required" class="form-label form-label-sm">
                                        Stamps Required <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm" style="max-width:220px;">
                                        <input type="number"
                                               id="stamps_required"
                                               name="stamps_required"
                                               class="form-control @error('stamps_required') is-invalid @enderror"
                                               min="1"
                                               step="1"
                                               x-model.number="stampsRequired"
                                               value="{{ old('stamps_required', $settings['stamps_required'] ?? 10) }}"
                                               {{ $isArchived ? 'disabled' : '' }}
                                               required>
                                        <span class="input-group-text">stamps</span>
                                        @error('stamps_required')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="reward_description" class="form-label form-label-sm">
                                        Reward Description <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="reward_description"
                                           name="reward_description"
                                           class="form-control form-control-sm @error('reward_description') is-invalid @enderror"
                                           placeholder="e.g. Free Coffee"
                                           maxlength="255"
                                           x-model="rewardDescription"
                                           value="{{ old('reward_description', $settings['reward_description'] ?? '') }}"
                                           {{ $isArchived ? 'disabled' : '' }}
                                           required>
                                    @error('reward_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info py-2 px-3 small mb-4">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Each qualifying purchase earns one stamp.
                                </div>

                                @unless ($isArchived)
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check-lg me-1"></i>Save Rules
                                    </button>
                                @endunless

                            </form>

                        @endif

                    </div>

                    {{-- ── Live Summary Card ───────────────────── --}}
                    <div class="col-12 col-lg-5 p-4">
                        <div class="card border-primary border-opacity-25 h-100">
                            <div class="card-header bg-primary bg-opacity-10 d-flex align-items-center gap-2">
                                <i class="bi bi-eye text-primary"></i>
                                <span class="fw-semibold">Campaign Summary</span>
                                <span class="badge bg-primary bg-opacity-25 text-primary ms-auto"
                                      style="font-size:.65rem;">Live Preview</span>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold mb-3" x-text="campaignName"></h6>

                                <dl class="row mb-0 small" style="row-gap:.5rem;">

                                    <dt class="col-5 text-muted fw-normal">Type</dt>
                                    <dd class="col-7 mb-0">{{ $campaign->type->label() }}</dd>

                                    @if ($campaign->type->value === 'points')

                                        <dt class="col-5 text-muted fw-normal">Earn Rule</dt>
                                        <dd class="col-7 mb-0">
                                            <span x-text="'Customers earn ' + pointsAwarded + ' point' + (pointsAwarded === 1 ? '' : 's') + ' for every ' + spendAmount + ' ' + currency + ' spent.'"></span>
                                        </dd>

                                        <dt class="col-5 text-muted fw-normal">Points Expiration</dt>
                                        <dd class="col-7 mb-0" x-text="expirationText"></dd>

                                        <dt class="col-5 text-muted fw-normal">Birthday Bonus</dt>
                                        <dd class="col-7 mb-0" x-text="birthdayText"></dd>

                                    @else

                                        <dt class="col-5 text-muted fw-normal">Earn Rule</dt>
                                        <dd class="col-7 mb-0">Customers receive 1 stamp for every qualifying purchase.</dd>

                                        <dt class="col-5 text-muted fw-normal">Stamps Required</dt>
                                        <dd class="col-7 mb-0">
                                            <span x-text="stampsRequired + ' stamp' + (stampsRequired === 1 ? '' : 's')"></span>
                                        </dd>

                                        <dt class="col-5 text-muted fw-normal">Reward</dt>
                                        <dd class="col-7 mb-0">
                                            <span x-text="rewardDescription || '—'"></span>
                                        </dd>

                                    @endif

                                    <dt class="col-5 text-muted fw-normal">Status</dt>
                                    <dd class="col-7 mb-0">
                                        <span x-text="campaignStatus"></span>
                                    </dd>

                                </dl>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {{-- ── /Rules Tab ──────────────────────────────────── --}}

            {{-- Coming Soon Tabs --}}
            @foreach ([
                'pane-rewards'      => ['icon' => 'bi-gift',              'label' => 'Rewards'],
                'pane-transactions' => ['icon' => 'bi-arrow-left-right',  'label' => 'Transactions'],
                'pane-analytics'    => ['icon' => 'bi-bar-chart-line',    'label' => 'Analytics'],
            ] as $paneId => $meta)
                <div class="tab-pane fade text-center py-5" id="{{ $paneId }}" role="tabpanel">
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
