<x-guest-layout>

    <h5 class="fw-bold mb-2">{{ __('auth.confirm_password_heading') }}</h5>
    <p class="text-muted small mb-4">
        {{ __('auth.confirm_password_body') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">{{ __('auth.password') }}</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">{{ __('auth.confirm_password_heading') }}</button>
        </div>
    </form>

</x-guest-layout>
