<x-guest-layout>

    <h5 class="fw-bold mb-2">Verify your email</h5>
    <p class="text-muted small mb-4">
        Thanks for signing up! Please verify your email address by clicking the link we sent you.
        If you didn't receive it, we can send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Resend Verification Email</button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <div class="d-grid">
            <button type="submit" class="btn btn-outline-secondary btn-sm">Log Out</button>
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
