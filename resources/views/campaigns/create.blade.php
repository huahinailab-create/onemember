<x-app-layout>
    <x-slot name="title">Create Campaign – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Campaigns</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>Create Campaign</h1>
            <p>
                <a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Back to Campaigns
                </a>
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <span class="fw-semibold">Campaign Details</span>
                </div>
                <div class="card-body">

                    @if ($errors->has('limit'))
                        <x-subscription-limit-warning level="limit_reached" feature="campaign" />
                    @elseif ($campaignUsage && $campaignUsage['level'] !== 'normal')
                        <x-subscription-limit-warning
                            :level="$campaignUsage['level']"
                            feature="campaign"
                            :percentage="$campaignUsage['percentage']"
                            :used="$campaignUsage['used']"
                            :limit="$campaignUsage['limit']" />
                    @endif

                    <form method="POST" action="{{ route('campaigns.store') }}" novalidate>
                        @csrf

                        {{-- Campaign Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Campaign Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   maxlength="150"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campaign Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label">
                                Campaign Type <span class="text-danger">*</span>
                            </label>
                            <select id="type"
                                    name="type"
                                    class="form-select @error('type') is-invalid @enderror"
                                    required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select a type…</option>
                                <option value="points" {{ old('type') === 'points' ? 'selected' : '' }}>
                                    Points — customers earn points for every purchase
                                </option>
                                <option value="stamps" {{ old('type') === 'stamps' ? 'selected' : '' }}>
                                    Stamp Card — customers collect stamps per visit
                                </option>
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
                                      rows="3"
                                      maxlength="1000">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional. Describe what this campaign offers to your customers.</div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>
                                    Draft — not yet active
                                </option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
                                    Active — open for earning
                                </option>
                                <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>
                                    Paused — temporarily suspended
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Campaign
                            </button>
                            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
