<x-guest-layout>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <h5 class="fw-bold mb-4">{{ __('auth.sign_in_heading') }}</h5>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">{{ __('auth.password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small text-decoration-none">{{ __('auth.forgot_password') }}</a>
                @endif
            </div>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label small">{{ __('auth.remember_me') }}</label>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">{{ __('buttons.sign_in') }}</button>
        </div>

        @if (Route::has('register'))
            <p class="text-center text-muted small mt-3 mb-0">
                {{ __('auth.no_account') }}
                <a href="{{ route('register') }}" class="text-decoration-none">{{ __('auth.register_link') }}</a>
            </p>
        @endif
    </form>

</x-guest-layout>
