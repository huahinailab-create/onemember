<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Demo Reset</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-arrow-counterclockwise me-2 text-warning"></i>Demo Data Reset</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">What This Does</div>
                        <div class="card-body">
                            <ul class="small text-muted mb-0">
                                <li>Archives (soft-deletes) the merchant</li>
                                <li>Force-deletes all members</li>
                                <li>Deletes all transactions</li>
                                <li>Deletes all redemptions</li>
                                <li>Deletes all campaigns</li>
                                <li>Deletes all rewards</li>
                                <li>Deletes all notifications</li>
                                <li>Clears failed jobs &amp; pending queue jobs</li>
                                <li>All run inside a database transaction</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-semibold">Current Stats</div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                @foreach (['merchants'=>'Merchants','members'=>'Members','transactions'=>'Transactions','rewards'=>'Rewards','failed_jobs'=>'Failed Jobs','pending_jobs'=>'Pending Jobs'] as $k => $l)
                                    <tr><th class="ps-3 text-muted">{{ $l }}</th><td>{{ number_format($stats[$k]) }}</td></tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white fw-semibold">
                            <i class="bi bi-exclamation-triangle me-1"></i>Reset Demo Environment
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Select a merchant to reset. Type <code>DELETE</code> to confirm. This runs inside a transaction and cannot be undone.</p>
                            <form id="demoResetForm" method="POST" action="{{ route('dev.demo-reset.reset') }}">
                                @csrf @method('DELETE')
                                <div class="mb-3">
                                    <label class="form-label">Merchant</label>
                                    <select name="merchant_id" class="form-select" required>
                                        <option value="">Select merchant…</option>
                                        @foreach ($merchants as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->user?->email ?? 'no user' }}){{ $m->trashed() ? ' [deleted]' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Type <strong>DELETE</strong> to confirm</label>
                                    <input type="text" name="confirm" class="form-control" id="demoConfirm" placeholder="DELETE" autocomplete="off" required>
                                </div>
                                <button type="submit" id="demoResetBtn" class="btn btn-danger w-100" disabled>
                                    <i class="bi bi-nuclear me-1"></i>Reset Demo Environment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.getElementById('demoConfirm').addEventListener('input', function () {
    document.getElementById('demoResetBtn').disabled = this.value !== 'DELETE';
});
</script>
