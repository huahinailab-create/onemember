<x-app-layout>
    <x-slot name="title">Add Member – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Members</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>Add Member</h1>
            <p>
                <a href="{{ route('members') }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Back to Members
                </a>
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <span class="fw-semibold">Member Details</span>
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
                                Full Name <span class="text-danger">*</span>
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
                                Mobile Number <span class="text-danger">*</span>
                            </label>
                            <input type="text"
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
                                Date of Birth <span class="text-danger">*</span>
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
                            <label for="nickname" class="form-label">Nickname</label>
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
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   maxlength="255">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes"
                                      name="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      maxlength="500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 500 characters.</div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-check me-1"></i> Save Member
                            </button>
                            <a href="{{ route('members') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
