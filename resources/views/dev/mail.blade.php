<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Test Mail</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-envelope-check me-2 text-warning"></i>Test Mail</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header fw-semibold">Send Test Email</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.mail.send') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">To</label>
                                    <input type="email" name="to" class="form-control" value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" class="form-control" value="Test email from OneMember Dev Tools" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Body</label>
                                    <textarea name="body" class="form-control" rows="4" required>This is a test email sent from OneMember Developer Tools at {{ now() }}.</textarea>
                                </div>
                                <button class="btn btn-warning w-100"><i class="bi bi-send me-1"></i>Send Test Email</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Mail Configuration</div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                @foreach ($mailConfig as $key => $value)
                                    <tr><th class="text-muted" style="width:140px;">{{ $key }}</th><td><code>{{ $value }}</code></td></tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-semibold">Resend API</div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Test that the Resend API key is valid and the client can be initialised.</p>
                            <form method="POST" action="{{ route('dev.mail.test-resend') }}">
                                @csrf
                                <button class="btn btn-outline-secondary w-100"><i class="bi bi-plug me-1"></i>Test Resend API</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
