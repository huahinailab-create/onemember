<x-app-layout>
    <x-slot name="title">{{ __('members.add_member_heading') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('members.title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ __('members.add_member_heading') }}</h1>
            <p>
                <a href="{{ route('members') }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('members.back_to_members') }}
                </a>
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <span class="fw-semibold">{{ __('members.member_details') }}</span>
                </div>
                <div class="card-body">

                    @if ($errors->has('limit'))
                        <x-subscription-limit-warning level="limit_reached" feature="member" />
                    @elseif ($memberUsage && $memberUsage['level'] !== 'normal')
                        <x-subscription-limit-warning
                            :level="$memberUsage['level']"
                            feature="member"
                            :percentage="$memberUsage['percentage']"
                            :used="$memberUsage['used']"
                            :limit="$memberUsage['limit']" />
                    @endif

                    @error('limit')
                        {{-- error displayed via component above --}}
                    @enderror

                    <form method="POST" action="{{ route('members.store') }}" novalidate>
                        @csrf

                        {{-- Full Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                {{ __('members.full_name') }} <span class="text-danger">*</span>
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

                        {{-- Mobile Number --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                {{ __('members.mobile_number') }} <span class="text-danger">*</span>
                            </label>
                            <input type="tel"
                                   inputmode="numeric"
                                   id="phone"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}"
                                   maxlength="30"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date of Birth --}}
                        <div class="mb-3">
                            <label for="birthday" class="form-label">
                                {{ __('members.date_of_birth') }} <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="birthday"
                                   name="birthday"
                                   class="form-control @error('birthday') is-invalid @enderror"
                                   value="{{ old('birthday') }}"
                                   required>
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Nickname --}}
                        <div class="mb-3">
                            <label for="nickname" class="form-label">{{ __('members.nickname') }}</label>
                            <input type="text"
                                   id="nickname"
                                   name="nickname"
                                   class="form-control @error('nickname') is-invalid @enderror"
                                   value="{{ old('nickname') }}"
                                   maxlength="50">
                            @error('nickname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('members.email') }}</label>
                            <input type="email"
                                   inputmode="email"
                                   autocomplete="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   maxlength="255">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Postal Code --}}
                        <div class="mb-3">
                            <label for="postal_code" class="form-label">{{ __('members.postal_code') }}</label>
                            <input type="text"
                                   inputmode="numeric"
                                   id="postal_code"
                                   name="postal_code"
                                   class="form-control @error('postal_code') is-invalid @enderror"
                                   value="{{ old('postal_code') }}"
                                   maxlength="20"
                                   placeholder="e.g. 10110">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('members.postal_code_hint') }}</div>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label for="notes" class="form-label">{{ __('members.notes') }}</label>
                            <textarea id="notes"
                                      name="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      maxlength="500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('members.notes_hint') }}</div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-check me-1"></i> {{ __('buttons.save_member') }}
                            </button>
                            <a href="{{ route('members') }}" class="btn btn-outline-secondary">{{ __('buttons.cancel') }}</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
