<x-guest-layout>

    {{-- Trial value strip --}}
    <div class="rounded-3 p-3 mb-4" style="background:#fff0f7;border:1px solid #ffd6eb;">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge fw-semibold px-2 py-1" style="background:#FF1585;font-size:.75rem;">
                {{ __('auth.trial_badge') }}
            </span>
            <span class="fw-semibold small" style="color:#1A2E5A;">{{ __('auth.trial_heading') }}</span>
        </div>
        <ul class="list-unstyled mb-0 small" style="color:#1A2E5A;">
            <li><i class="bi bi-check-circle-fill me-2" style="color:#FF1585;"></i>{{ __('auth.trial_tick_1') }}</li>
            <li><i class="bi bi-check-circle-fill me-2" style="color:#FF1585;"></i>{{ __('auth.trial_tick_2') }}</li>
            <li><i class="bi bi-check-circle-fill me-2" style="color:#FF1585;"></i>{{ __('auth.trial_tick_3') }}</li>
        </ul>
    </div>

    <h5 class="fw-bold mb-4">{{ __('auth.create_account_heading') }}</h5>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('auth.name') }}</label>
            <input id="name" type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('auth.password') }}</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">{{ __('buttons.register') }}</button>
        </div>

        <p class="text-center text-muted small mt-3 mb-0">
            {{ __('auth.have_account') }}
            <a href="{{ route('login') }}" class="text-decoration-none">{{ __('auth.sign_in_link') }}</a>
        </p>
    </form>

</x-guest-layout>
