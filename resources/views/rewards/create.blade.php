<x-app-layout>
    <x-slot name="title">Add Reward – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Campaigns</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>Add Reward</h1>
            <p>
                <a href="{{ route('campaigns.show', $campaign) . '?active_tab=rewards' }}"
                   class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Back to {{ $campaign->name }}
                </a>
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-gift text-primary"></i>
                    <span class="fw-semibold">Reward Details</span>
                    <span class="badge bg-secondary ms-auto">{{ $campaign->name }}</span>
                </div>
                <div class="card-body" x-data="{ unlimited: {{ old('unlimited') ? 'true' : 'false' }} }">
                    <form method="POST" action="{{ route('campaigns.rewards.store', $campaign) }}" novalidate>
                        @csrf

                        {{-- Reward Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Reward Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   maxlength="100"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reward Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label">
                                Reward Type <span class="text-danger">*</span>
                            </label>
                            <select id="type"
                                    name="type"
                                    class="form-select @error('type') is-invalid @enderror"
                                    required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select a type…</option>
                                @foreach (\App\Enums\RewardType::cases() as $case)
                                    <option value="{{ $case->value }}" {{ old('type') === $case->value ? 'selected' : '' }}>
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
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description"
                                      name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="2"
                                      maxlength="1000">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Points Required (Points campaigns only) --}}
                        @if ($campaign->type->value === 'points')
                            <div class="mb-3">
                                <label for="points_required" class="form-label">
                                    Points Required <span class="text-danger">*</span>
                                </label>
                                <div class="input-group" style="max-width:220px;">
                                    <input type="number"
                                           id="points_required"
                                           name="points_required"
                                           class="form-control @error('points_required') is-invalid @enderror"
                                           min="1"
                                           value="{{ old('points_required') }}"
                                           required>
                                    <span class="input-group-text">pts</span>
                                    @error('points_required')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Customers spend this many points to claim this reward.</div>
                            </div>
                        @else
                            {{-- Stamp campaigns: show completion info --}}
                            @php $stampsRequired = $campaign->settings['stamps_required'] ?? '—'; @endphp
                            <div class="mb-3">
                                <label class="form-label">Stamp Requirement</label>
                                <div class="form-control bg-light" style="cursor:default;">
                                    {{ $stampsRequired }} stamps (campaign completion)
                                </div>
                                <div class="form-text">
                                    This reward is available when a customer completes the stamp card.
                                </div>
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
                                       {{ old('unlimited') ? 'checked' : '' }}>
                                <label class="form-check-label" for="unlimited">
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
                                       class="form-control @error('quantity_available') is-invalid @enderror"
                                       min="1"
                                       value="{{ old('quantity_available') }}"
                                       style="max-width:180px;">
                                @error('quantity_available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>
                                    Draft — not yet visible to customers
                                </option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
                                    Active — available for redemption
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Internal Notes --}}
                        <div class="mb-4">
                            <label for="internal_notes" class="form-label">Internal Notes</label>
                            <textarea id="internal_notes"
                                      name="internal_notes"
                                      class="form-control @error('internal_notes') is-invalid @enderror"
                                      rows="2"
                                      maxlength="1000">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Visible to you only. Not shown to customers.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Reward
                            </button>
                            <a href="{{ route('campaigns.show', $campaign) . '?active_tab=rewards' }}"
                               class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
