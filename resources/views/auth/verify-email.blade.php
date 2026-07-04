<x-guest-layout>

    <h5 class="fw-bold mb-2">{{ __('auth.verify_email_heading') }}</h5>
    <p class="text-muted small mb-4">
        {{ __('auth.verify_email_thanks') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3">
            {{ __('auth.verification_sent') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">{{ __('auth.resend_verification') }}</button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <div class="d-grid">
            <button type="submit" class="btn btn-outline-secondary btn-sm">{{ __('auth.log_out') }}</button>
        </div>
    </form>

    <script>
        (function () {
            var interval = setInterval(function () {
                fetch('{{ route('verification.status') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.verified) {
                        clearInterval(interval);
                        window.location.href = '{{ route('dashboard') }}';
                    }
                })
                .catch(function () { /* ignore network errors */ });
            }, 5000);
        })();
    </script>

</x-guest-layout>
