<x-guest-layout>

    <h5 class="fw-bold mb-2">Forgot your password?</h5>
    <p class="text-muted small mb-4">
        No problem. Enter your email address and we'll send you a password reset link.
    </p>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </div>

        <p class="text-center text-muted small mt-3 mb-0">
            <a href="{{ route('login') }}" class="text-decoration-none">Back to sign in</a>
        </p>
    </form>

</x-guest-layout>
