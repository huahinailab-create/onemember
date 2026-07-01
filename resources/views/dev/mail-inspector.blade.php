<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Mail Inspector</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-envelope-check me-2 text-warning"></i>Mail Inspector</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Configuration</div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                @foreach ($mailConfig as $key => $value)
                                    <tr>
                                        <th class="ps-3 text-muted" style="width:150px;">{{ str_replace('_', ' ', ucfirst($key)) }}</th>
                                        <td><code>{{ $value ?? '—' }}</code></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Queue Status</div>
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <div><span class="h4">{{ $queueStatus['pending'] }}</span><div class="text-muted small">Pending</div></div>
                                <div><span class="h4 {{ $queueStatus['failed'] > 0 ? 'text-danger' : '' }}">{{ $queueStatus['failed'] }}</span><div class="text-muted small">Failed</div></div>
                            </div>
                        </div>
                    </div>

                    @if ($lastTest)
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">Last Test Email</div>
                            <div class="card-body">
                                <div class="text-muted small">
                                    <div>Sent: <strong>{{ \Carbon\Carbon::parse($lastTest->created_at)->diffForHumans() }}</strong></div>
                                    @if ($lastTest->details)
                                        @php $d = json_decode($lastTest->details, true); @endphp
                                        <div>To: <code>{{ $d['to'] ?? '—' }}</code></div>
                                        <div>Subject: {{ $d['subject'] ?? '—' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header fw-semibold">API Checks</div>
                        <div class="card-body d-flex flex-column gap-2">
                            <form method="POST" action="{{ route('dev.mail-inspector.test-resend') }}">@csrf
                                <button class="btn btn-outline-secondary w-100"><i class="bi bi-plug me-1"></i>Verify Resend Configuration</button>
                            </form>
                            <form method="POST" action="{{ route('dev.mail-inspector.check-key') }}">@csrf
                                <button class="btn btn-outline-secondary w-100"><i class="bi bi-key me-1"></i>Check API Key Exists</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Send Test Email</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.mail-inspector.send-test') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">To</label>
                                    <input type="email" name="to" class="form-control" value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" class="form-control" value="Test email from OneMember Mail Inspector" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Body</label>
                                    <textarea name="body" class="form-control" rows="4" required>This is a test email sent via OneMember Developer Tools Mail Inspector at {{ now() }}.</textarea>
                                </div>
                                <button class="btn btn-warning w-100"><i class="bi bi-send me-1"></i>Send Test Email</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-semibold">Send Verification Email</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.mail-inspector.send-verification') }}" class="d-flex gap-2">
                                @csrf
                                <select name="user_id" class="form-select" required>
                                    <option value="">Select user…</option>
                                    @foreach (\App\Models\User::orderBy('email')->limit(50)->get() as $u)
                                        <option value="{{ $u->id }}" @selected($u->id === auth()->id())>{{ $u->email }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-secondary text-nowrap"><i class="bi bi-envelope me-1"></i>Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
