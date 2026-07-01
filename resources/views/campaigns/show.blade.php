<x-app-layout>
    <x-slot name="title">{{ $campaign->name }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('campaigns.title') }}</x-slot>

    @php $isArchived = $campaign->trashed(); @endphp

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="mb-1">
                <a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('campaigns.back_to_campaigns') }}
                </a>
            </div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                {{ $campaign->name }}
                @if ($isArchived)
                    <span class="badge bg-danger fs-6 fw-normal">{{ __('campaigns.status_archived') }}</span>
                @else
                    <span class="{{ $campaign->status->badgeClass() }} fs-6 fw-normal">
                        {{ $campaign->status->label() }}
                    </span>
                @endif
            </h1>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            @if ($isArchived)
                <button type="button" class="btn btn-outline-success disabled" title="{{ __('buttons.coming_soon') }}">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('campaigns.restore_campaign') }}
                    <span class="badge bg-secondary ms-1" style="font-size:.65rem;">{{ __('buttons.coming_soon') }}</span>
                </button>
            @else
                @if ($campaign->status === \App\Enums\CampaignStatus::Active)
                    <form method="POST" action="{{ route('campaigns.pause', $campaign) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning">
                            <i class="bi bi-pause-circle me-1"></i>{{ __('campaigns.pause_campaign') }}
                        </button>
                    </form>
                @endif
                <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#archiveModal">
                    <i class="bi bi-archive me-1"></i>{{ __('campaigns.archive_campaign') }}
                </button>
            @endif
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Campaign Settings Card (edit form) --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-star text-primary"></i>
                    <span class="fw-semibold">{{ __('campaigns.campaign_settings') }}</span>
                    @if ($isArchived)
                        <span class="badge bg-danger ms-auto" style="font-size:.65rem;">{{ __('campaigns.read_only') }}</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('campaigns.update', $campaign) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Campaign Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label form-label-sm">
                                {{ __('campaigns.campaign_name') }} <span class="text-danger">*</span>
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
                                {{ __('campaigns.campaign_type') }} <span class="text-danger">*</span>
                            </label>
                            <select id="type"
                                    name="type"
                                    class="form-select form-select-sm @error('type') is-invalid @enderror"
                                    {{ $isArchived ? 'disabled' : '' }}>
                                <option value="points" {{ old('type', $campaign->type->value) === 'points' ? 'selected' : '' }}>
                                    {{ __('campaigns.type_points') }}
                                </option>
                                <option value="stamps" {{ old('type', $campaign->type->value) === 'stamps' ? 'selected' : '' }}>
                                    {{ __('campaigns.type_stamps') }}
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label form-label-sm">{{ __('campaigns.description') }}</label>
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
                                {{ __('campaigns.status') }} <span class="text-danger">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    class="form-select form-select-sm @error('status') is-invalid @enderror"
                                    {{ $isArchived ? 'disabled' : '' }}>
                                <option value="draft" {{ old('status', $campaign->status?->value) === 'draft' ? 'selected' : '' }}>
                                    {{ __('campaigns.status_draft') }}
                                </option>
                                <option value="active" {{ old('status', $campaign->status?->value) === 'active' ? 'selected' : '' }}>
                                    {{ __('campaigns.status_active') }}
                                </option>
                                <option value="paused" {{ old('status', $campaign->status?->value) === 'paused' ? 'selected' : '' }}>
                                    {{ __('campaigns.status_paused') }}
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Read-only meta --}}
                        <dl class="row mb-0 small" style="row-gap:.5rem;">
                            <dt class="col-5 text-muted fw-normal">{{ __('campaigns.created') }}</dt>
                            <dd class="col-7 mb-0">{{ $campaign->created_at->format('d M Y') }}</dd>

                            <dt class="col-5 text-muted fw-normal">{{ __('campaigns.col_updated') }}</dt>
                            <dd class="col-7 mb-0">{{ $campaign->updated_at->format('d M Y, H:i') }}</dd>
                        </dl>

                    </div>

                    @unless ($isArchived)
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>{{ __('buttons.save_changes') }}
                            </button>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary btn-sm">
                                {{ __('buttons.discard') }}
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
                    <span class="fw-semibold">{{ __('campaigns.campaign_summary') }}</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0" style="row-gap:.75rem;">
                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.campaign_name') }}</dt>
                        <dd class="col-7 mb-0 fw-medium">{{ $campaign->name }}</dd>

                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.col_type') }}</dt>
                        <dd class="col-7 mb-0 d-flex align-items-center gap-1">
                            <i class="bi {{ $campaign->type->icon() }} text-muted" style="font-size:.875rem;"></i>
                            {{ $campaign->type->label() }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.col_status') }}</dt>
                        <dd class="col-7 mb-0">
                            @if ($isArchived)
                                <span class="badge bg-danger">{{ __('campaigns.status_archived') }}</span>
                            @else
                                <span class="{{ $campaign->status->badgeClass() }}">{{ $campaign->status->label() }}</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.description') }}</dt>
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
                        <i class="bi bi-sliders me-1"></i>{{ __('campaigns.tab_rules') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-rewards" data-bs-toggle="tab"
                            data-bs-target="#pane-rewards" type="button" role="tab">
                        <i class="bi bi-gift me-1"></i>{{ __('campaigns.tab_rewards') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-transactions" data-bs-toggle="tab"
                            data-bs-target="#pane-transactions" type="button" role="tab">
                        <i class="bi bi-arrow-left-right me-1"></i>{{ __('campaigns.tab_transactions') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-analytics" data-bs-toggle="tab"
                            data-bs-target="#pane-analytics" type="button" role="tab">
                        <i class="bi bi-bar-chart-line me-1"></i>{{ __('campaigns.tab_analytics') }}
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
                    if (this.expirationType === 'never') return 'Points never expire.';
                    const d = parseInt(this.expirationDuration) || 0;
                    if (!d) return 'Not configured.';
                    return 'Points expire after ' + d + ' ' + this.expirationType + '.';
                },
                get birthdayText() {
                    if (!this.birthdayEnabled) return 'No birthday bonus.';
                    const p = parseInt(this.birthdayPoints) || 0;
                    if (!p) return 'Not configured.';
                    const before = parseInt(this.birthdayDaysBefore) || 0;
                    const after  = parseInt(this.birthdayDaysAfter)  || 0;
                    return p + ' bonus point' + (p === 1 ? '' : 's') + '. Valid '
                         + before + ' day' + (before === 1 ? '' : 's') + ' before until '
                         + after  + ' day' + (after  === 1 ? '' : 's') + ' after birthday.';
                },
            };
        }
        </script>

        <div class="tab-content" id="campaignTabsContent">

            {{-- ── Rules Tab ──────────────────────────────────── --}}
            @php
                $campaignConfigData = [
                    'type'               => $campaign->type->value,
                    'currency'           => $campaign->merchant->currency ?? 'THB',
                    'campaignName'       => $campaign->name,
                    'campaignStatus'     => $isArchived ? 'Archived' : $campaign->status->label(),
                    'spendAmount'        => (int) ($settings['spend_amount']              ?? 100),
                    'pointsAwarded'      => (int) ($settings['points_awarded']             ?? 1),
                    'expirationType'     => $settings['expiration_type']                   ?? 'never',
                    'expirationDuration' => $settings['expiration_duration']               ?? '',
                    'birthdayEnabled'    => (bool)($settings['birthday_enabled']           ?? false),
                    'birthdayPoints'     => $settings['birthday_points']                   ?? '',
                    'birthdayDaysBefore' => (int) ($settings['birthday_valid_days_before'] ?? 7),
                    'birthdayDaysAfter'  => (int) ($settings['birthday_valid_days_after']  ?? 7),
                    'stampsRequired'     => (int) ($settings['stamps_required']            ?? 10),
                    'rewardDescription'  => $settings['reward_description']                ?? '',
                ];
            @endphp
            <div class="tab-pane fade show active" id="pane-rules" role="tabpanel"
                 x-data="campaignConfig(@json($campaignConfigData))">

                <div class="row g-0">

                    {{-- ── Configuration Form ──────────────────── --}}
                    <div class="col-12 col-lg-7 p-4 border-end">

                        @if ($isArchived)
                            <div class="alert alert-secondary py-2 px-3 mb-4 small">
                                <i class="bi bi-lock me-1"></i>{{ __('campaigns.archived_rules_note') }}
                            </div>
                        @endif

                        @if ($campaign->type->value === 'points')

                            {{-- ── Points Configuration ─────────── --}}
                            <form method="POST" action="{{ route('campaigns.configure', $campaign) }}" novalidate>
                                @csrf
                                @method('PUT')

                                <h6 class="fw-semibold mb-3 text-primary">
                                    <i class="bi bi-star-fill me-1"></i>{{ __('campaigns.points_rules') }}
                                </h6>

                                {{-- Earn Method (read-only in MVP) --}}
                                <div class="mb-3">
                                    <label class="form-label form-label-sm">{{ __('campaigns.earn_method') }}</label>
                                    <select class="form-select form-select-sm bg-light" disabled>
                                        <option selected>{{ __('campaigns.spend_amount') }}</option>
                                    </select>
                                    <div class="form-text">{{ __('campaigns.earn_method_hint') }}</div>
                                </div>

                                {{-- Earn Rate --}}
                                <div class="mb-4">
                                    <label class="form-label form-label-sm fw-medium">{{ __('campaigns.earn_rate') }}</label>
                                    <div class="row g-2 align-items-end">
                                        <div class="col">
                                            <label for="spend_amount" class="form-label form-label-sm">
                                                {{ __('campaigns.spend_amount') }} <span class="text-danger">*</span>
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
                                                {{ __('campaigns.points_awarded') }} <span class="text-danger">*</span>
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

                                {{-- Point Expiration --}}
                                <div class="mb-4">
                                    <label class="form-label form-label-sm fw-medium mb-2">{{ __('campaigns.point_expiration') }}</label>

                                    <div class="form-check mb-1">
                                        <input type="radio"
                                               id="exp_never"
                                               name="expiration_type"
                                               value="never"
                                               class="form-check-input"
                                               x-model="expirationType"
                                               {{ $isArchived ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="exp_never">
                                            {{ __('campaigns.exp_never') }}
                                            <span class="badge bg-success ms-1" style="font-size:.65rem;">{{ __('onboarding.recommended') }}</span>
                                        </label>
                                    </div>

                                    <div class="form-check mb-1">
                                        <input type="radio"
                                               id="exp_months"
                                               name="expiration_type"
                                               value="months"
                                               class="form-check-input"
                                               x-model="expirationType"
                                               {{ $isArchived ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="exp_months">
                                            {{ __('campaigns.exp_months') }}
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input type="radio"
                                               id="exp_years"
                                               name="expiration_type"
                                               value="years"
                                               class="form-check-input"
                                               x-model="expirationType"
                                               {{ $isArchived ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="exp_years">
                                            {{ __('campaigns.exp_years') }}
                                        </label>
                                    </div>

                                    <div x-show="expirationType !== 'never'" x-cloak class="ms-4 mt-1">
                                        <label for="expiration_duration" class="form-label form-label-sm">
                                            {{ __('campaigns.duration') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-sm" style="max-width:200px;">
                                            <input type="number"
                                                   id="expiration_duration"
                                                   name="expiration_duration"
                                                   class="form-control @error('expiration_duration') is-invalid @enderror"
                                                   min="1"
                                                   x-model.number="expirationDuration"
                                                   value="{{ old('expiration_duration', $settings['expiration_duration'] ?? '') }}"
                                                   {{ $isArchived ? 'disabled' : '' }}
                                                   placeholder="e.g. 24">
                                            <span class="input-group-text" x-text="expirationType === 'months' ? 'months' : 'years'"></span>
                                            @error('expiration_duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-text mt-2">
                                        <i class="bi bi-lightbulb text-warning me-1"></i>
                                        Customers are more likely to return when points never expire or have a long expiration period such as 2 years.
                                    </div>
                                </div>

                                <hr class="my-3">

                                {{-- Birthday Bonus --}}
                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input type="checkbox"
                                               id="birthday_enabled"
                                               name="birthday_enabled"
                                               value="1"
                                               class="form-check-input"
                                               x-model="birthdayEnabled"
                                               {{ ($settings['birthday_enabled'] ?? false) ? 'checked' : '' }}
                                               {{ $isArchived ? 'disabled' : '' }}>
                                        <label class="form-check-label fw-medium" for="birthday_enabled">
                                            {{ __('campaigns.enable_birthday_bonus') }}
                                        </label>
                                    </div>

                                    <div x-show="birthdayEnabled" x-cloak class="mt-2 ms-4">

                                        {{-- Birthday Points --}}
                                        <div class="mb-3">
                                            <label for="birthday_points" class="form-label form-label-sm">
                                                {{ __('campaigns.birthday_points') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-sm" style="max-width:180px;">
                                                <input type="number"
                                                       id="birthday_points"
                                                       name="birthday_points"
                                                       class="form-control @error('birthday_points') is-invalid @enderror"
                                                       min="1"
                                                       x-model.number="birthdayPoints"
                                                       value="{{ old('birthday_points', $settings['birthday_points'] ?? '') }}"
                                                       {{ $isArchived ? 'disabled' : '' }}
                                                       placeholder="e.g. 100">
                                                <span class="input-group-text">pts</span>
                                                @error('birthday_points')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Valid Days --}}
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label for="birthday_valid_days_before" class="form-label form-label-sm">
                                                    {{ __('campaigns.birthday_days_before') }} <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <input type="number"
                                                           id="birthday_valid_days_before"
                                                           name="birthday_valid_days_before"
                                                           class="form-control @error('birthday_valid_days_before') is-invalid @enderror"
                                                           min="0"
                                                           x-model.number="birthdayDaysBefore"
                                                           value="{{ old('birthday_valid_days_before', $settings['birthday_valid_days_before'] ?? 7) }}"
                                                           {{ $isArchived ? 'disabled' : '' }}>
                                                    <span class="input-group-text">days</span>
                                                    @error('birthday_valid_days_before')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="birthday_valid_days_after" class="form-label form-label-sm">
                                                    {{ __('campaigns.birthday_days_after') }} <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <input type="number"
                                                           id="birthday_valid_days_after"
                                                           name="birthday_valid_days_after"
                                                           class="form-control @error('birthday_valid_days_after') is-invalid @enderror"
                                                           min="0"
                                                           x-model.number="birthdayDaysAfter"
                                                           value="{{ old('birthday_valid_days_after', $settings['birthday_valid_days_after'] ?? 7) }}"
                                                           {{ $isArchived ? 'disabled' : '' }}>
                                                    <span class="input-group-text">days</span>
                                                    @error('birthday_valid_days_after')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                @unless ($isArchived)
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check-lg me-1"></i>{{ __('campaigns.save_rules') }}
                                    </button>
                                @endunless

                            </form>

                        @else

                            {{-- ── Stamp Card Configuration ──────── --}}
                            <form method="POST" action="{{ route('campaigns.configure', $campaign) }}" novalidate>
                                @csrf
                                @method('PUT')

                                <h6 class="fw-semibold mb-3 text-primary">
                                    <i class="bi bi-grid-3x3-gap-fill me-1"></i>{{ __('campaigns.stamp_card_rules') }}
                                </h6>

                                <div class="mb-3">
                                    <label for="stamps_required" class="form-label form-label-sm">
                                        {{ __('campaigns.stamps_required') }} <span class="text-danger">*</span>
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
                                        {{ __('campaigns.reward_description') }} <span class="text-danger">*</span>
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
                                    {{ __('campaigns.one_stamp_per_purchase') }}
                                </div>

                                @unless ($isArchived)
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check-lg me-1"></i>{{ __('campaigns.save_rules') }}
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
                                <span class="fw-semibold">{{ __('campaigns.campaign_summary') }}</span>
                                <span class="badge bg-primary bg-opacity-25 text-primary ms-auto"
                                      style="font-size:.65rem;">{{ __('campaigns.live_preview') }}</span>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold mb-3" x-text="campaignName"></h6>

                                <dl class="row mb-0 small" style="row-gap:.5rem;">

                                    <dt class="col-5 text-muted fw-normal">{{ __('campaigns.col_type') }}</dt>
                                    <dd class="col-7 mb-0">{{ $campaign->type->label() }}</dd>

                                    @if ($campaign->type->value === 'points')

                                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.summary_earn_rule') }}</dt>
                                        <dd class="col-7 mb-0">
                                            <span x-text="'Customers earn ' + pointsAwarded + ' point' + (pointsAwarded === 1 ? '' : 's') + ' for every ' + spendAmount + ' ' + currency + ' spent.'"></span>
                                        </dd>

                                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.summary_point_expiration') }}</dt>
                                        <dd class="col-7 mb-0" x-text="expirationText"></dd>

                                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.summary_birthday_bonus') }}</dt>
                                        <dd class="col-7 mb-0" x-text="birthdayText"></dd>

                                    @else

                                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.summary_earn_rule') }}</dt>
                                        <dd class="col-7 mb-0">{{ __('campaigns.summary_stamp_earn_rule') }}</dd>

                                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.summary_stamps_required') }}</dt>
                                        <dd class="col-7 mb-0">
                                            <span x-text="stampsRequired + ' stamp' + (stampsRequired === 1 ? '' : 's')"></span>
                                        </dd>

                                        <dt class="col-5 text-muted fw-normal">{{ __('campaigns.summary_reward') }}</dt>
                                        <dd class="col-7 mb-0">
                                            <span x-text="rewardDescription || '—'"></span>
                                        </dd>

                                    @endif

                                    <dt class="col-5 text-muted fw-normal">{{ __('campaigns.col_status') }}</dt>
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

            {{-- ── Rewards Tab ─────────────────────────────────── --}}
            <div class="tab-pane fade" id="pane-rewards" role="tabpanel">

                {{-- Rewards toolbar --}}
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Reward filter">
                        @foreach (['active' => __('campaigns.filter_active'), 'draft' => __('campaigns.filter_draft'), 'archived' => __('campaigns.filter_archived'), 'all' => __('campaigns.filter_all')] as $val => $lbl)
                            <a href="{{ route('campaigns.show', $campaign) . '?' . http_build_query(['reward_filter' => $val, 'active_tab' => 'rewards'] + request()->only(['reward_search'])) }}"
                               class="btn {{ $rewardFilter === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ $lbl }}
                            </a>
                        @endforeach
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        <form method="GET" action="{{ route('campaigns.show', $campaign) }}"
                              class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="reward_filter" value="{{ $rewardFilter }}">
                            <input type="hidden" name="active_tab" value="rewards">
                            <input type="text"
                                   name="reward_search"
                                   class="form-control form-control-sm"
                                   placeholder="{{ __('campaigns.search_rewards_ph') }}"
                                   value="{{ request('reward_search') }}"
                                   style="width:180px;">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-search"></i>
                            </button>
                            @if (request('reward_search'))
                                <a href="{{ route('campaigns.show', $campaign) . '?' . http_build_query(['reward_filter' => $rewardFilter, 'active_tab' => 'rewards']) }}"
                                   class="btn btn-sm btn-outline-secondary">{{ __('buttons.clear') }}</a>
                            @endif
                        </form>

                        @unless ($isArchived)
                            <a href="{{ route('campaigns.rewards.create', $campaign) }}"
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('buttons.add_reward') }}
                            </a>
                        @endunless
                    </div>
                </div>

                {{-- Rewards list --}}
                @if ($rewards->isEmpty())
                    <div class="text-center py-5">
                        <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                            <i class="bi bi-gift text-primary"></i>
                        </div>
                        @if (request('reward_search'))
                            <h6 class="fw-semibold mb-1">{{ __('campaigns.rewards_empty_search_title') }}</h6>
                            <p class="text-muted mb-0 small">{!! __('campaigns.rewards_empty_search_body', ['link' => route('campaigns.show', $campaign) . '?' . http_build_query(['reward_filter' => $rewardFilter, 'active_tab' => 'rewards'])]) !!}</p>
                        @elseif ($rewardFilter === 'archived')
                            <h6 class="fw-semibold mb-1">{{ __('campaigns.rewards_empty_archived_title') }}</h6>
                            <p class="text-muted mb-0 small">{{ __('campaigns.rewards_empty_archived_body') }}</p>
                        @else
                            <h6 class="fw-semibold mb-1">{{ __('campaigns.rewards_empty_title') }}</h6>
                            @unless ($isArchived)
                                <p class="text-muted mb-0 small">
                                    {!! __('campaigns.rewards_empty_body', ['link' => route('campaigns.rewards.create', $campaign)]) !!}
                                </p>
                            @endunless
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">{{ __('campaigns.col_reward_name') }}</th>
                                    <th>{{ __('campaigns.col_reward_type') }}</th>
                                    @if ($campaign->type->value === 'points')
                                        <th class="text-end">{{ __('campaigns.col_points_required') }}</th>
                                    @endif
                                    <th class="text-end">{{ __('campaigns.col_quantity') }}</th>
                                    <th>{{ __('campaigns.col_status') }}</th>
                                    <th class="text-end pe-4">{{ __('campaigns.col_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rewards as $reward)
                                    <tr class="{{ $reward->trashed() ? 'text-muted' : '' }}">
                                        <td class="ps-4 fw-medium">
                                            <a href="{{ route('campaigns.rewards.show', [$campaign, $reward]) }}"
                                               class="text-decoration-none {{ $reward->trashed() ? 'text-muted' : '' }}">
                                                {{ $reward->name }}
                                            </a>
                                        </td>
                                        <td>{{ $reward->type->label() }}</td>
                                        @if ($campaign->type->value === 'points')
                                            <td class="text-end">
                                                {{ $reward->points_required ? number_format($reward->points_required) . ' pts' : '—' }}
                                            </td>
                                        @endif
                                        <td class="text-end">
                                            {{ $reward->quantity_available === null ? __('campaigns.unlimited') : number_format($reward->quantity_available) }}
                                        </td>
                                        <td>
                                            @if ($reward->trashed())
                                                <span class="badge bg-danger">{{ __('campaigns.status_archived') }}</span>
                                            @else
                                                <span class="{{ $reward->status->badgeClass() }}">
                                                    {{ $reward->status->label() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('campaigns.rewards.show', [$campaign, $reward]) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil me-1"></i>{{ __('buttons.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-top text-muted" style="font-size:.8125rem;">
                        {{ trans_choice('campaigns.reward_count', $rewards->count(), ['count' => $rewards->count()]) }}
                    </div>
                @endif

            </div>
            {{-- ── /Rewards Tab ─────────────────────────────────── --}}

            {{-- Coming Soon Tabs --}}
            @foreach ([
                'pane-transactions' => ['icon' => 'bi-arrow-left-right',  'label' => __('campaigns.tab_transactions')],
                'pane-analytics'    => ['icon' => 'bi-bar-chart-line',    'label' => __('campaigns.tab_analytics')],
            ] as $paneId => $meta)
                <div class="tab-pane fade text-center py-5" id="{{ $paneId }}" role="tabpanel">
                    <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi {{ $meta['icon'] }} text-primary"></i>
                    </div>
                    <h6 class="fw-semibold mb-1">{{ $meta['label'] }} — {{ __('buttons.coming_soon') }}</h6>
                    <p class="text-muted mb-0 small">{{ __('campaigns.coming_soon_note') }}</p>
                </div>
            @endforeach

        </div>

        {{-- Activate correct tab from URL param --}}
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tab = new URLSearchParams(window.location.search).get('active_tab');
            if (tab) {
                const el = document.getElementById('tab-' + tab);
                if (el) bootstrap.Tab.getOrCreateInstance(el).show();
            }
        });
        </script>
    </div>

    {{-- Archive Modal --}}
    @unless ($isArchived)
        <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger" id="archiveModalLabel">
                            <i class="bi bi-archive me-2"></i>{{ __('campaigns.archive_campaign') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">{!! __('campaigns.archive_confirm', ['name' => '<strong>' . e($campaign->name) . '</strong>']) !!}</p>
                        <p class="text-muted small mb-0">
                            {{ __('campaigns.archive_note') }}
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('buttons.cancel') }}</button>
                        <form method="POST" action="{{ route('campaigns.archive', $campaign) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-archive me-1"></i>{{ __('campaigns.archive_campaign') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endunless

</x-app-layout>
