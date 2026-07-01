<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Quick Actions</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            {{-- Stats --}}
            <div class="row g-2 mb-4">
                @foreach (['merchants'=>'Merchants','members'=>'Members','transactions'=>'Transactions','rewards'=>'Rewards','failed_jobs'=>'Failed Jobs'] as $key => $label)
                    <div class="col"><div class="card text-center"><div class="card-body py-2"><div class="h5 mb-0">{{ number_format($stats[$key]) }}</div><div class="text-muted small">{{ $label }}</div></div></div></div>
                @endforeach
            </div>

            <div class="row g-4">
                {{-- Demo Merchant --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-shop me-1"></i>Demo Merchant</div>
                        <div class="card-body">
                            <p class="text-muted small">Creates a new user + merchant with random demo data.</p>
                        </div>
                        <div class="card-footer">
                            <form method="POST" action="{{ route('dev.quick-actions.demo-merchant') }}">@csrf
                                <button class="btn btn-warning w-100"><i class="bi bi-plus-circle me-1"></i>Create Demo Merchant</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Generate Members --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-people me-1"></i>Generate Members</div>
                        <div class="card-body">
                            <form id="membersForm" method="POST" action="{{ route('dev.quick-actions.members') }}">@csrf
                                <select name="merchant_id" class="form-select form-select-sm mb-2" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <input type="hidden" name="count" value="10" id="membersCount">
                            </form>
                        </div>
                        <div class="card-footer d-flex flex-wrap gap-1">
                            @foreach ([10, 100, 1000] as $n)
                                <button class="btn btn-sm btn-outline-warning flex-fill"
                                        onclick="document.getElementById('membersCount').value={{ $n }};document.getElementById('membersForm').submit()">
                                    +{{ $n }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Generate Purchases --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-cart me-1"></i>Generate Purchases</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.quick-actions.purchases') }}" class="d-flex flex-column gap-2">@csrf
                                <select name="merchant_id" class="form-select form-select-sm" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <div class="d-flex gap-2">
                                    <input type="number" name="count" class="form-control form-control-sm" value="50" min="1" max="500">
                                    <button class="btn btn-sm btn-warning text-nowrap">Generate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Loyalty Points --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-star me-1"></i>Generate Loyalty Points</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.quick-actions.points') }}" class="d-flex flex-column gap-2">@csrf
                                <select name="merchant_id" class="form-select form-select-sm" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <div class="d-flex gap-2">
                                    <input type="number" name="count" class="form-control form-control-sm" value="50" min="1" max="500">
                                    <button class="btn btn-sm btn-warning text-nowrap">Generate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Stamps --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-grid me-1"></i>Generate Stamps</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.quick-actions.stamps') }}" class="d-flex flex-column gap-2">@csrf
                                <select name="merchant_id" class="form-select form-select-sm" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <div class="d-flex gap-2">
                                    <input type="number" name="count" class="form-control form-control-sm" value="50" min="1" max="500">
                                    <button class="btn btn-sm btn-warning text-nowrap">Generate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Redemptions --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-gift me-1"></i>Generate Redemptions</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.quick-actions.redemptions') }}" class="d-flex flex-column gap-2">@csrf
                                <select name="merchant_id" class="form-select form-select-sm" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <div class="d-flex gap-2">
                                    <input type="number" name="count" class="form-control form-control-sm" value="20" min="1" max="200">
                                    <button class="btn btn-sm btn-warning text-nowrap">Generate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Birthdays --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-cake2 me-1"></i>Birthday Members</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.quick-actions.birthdays') }}" class="d-flex flex-column gap-2">@csrf
                                <select name="merchant_id" class="form-select form-select-sm" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <div class="d-flex gap-2">
                                    <input type="number" name="count" class="form-control form-control-sm" value="10" min="1" max="100">
                                    <button class="btn btn-sm btn-warning text-nowrap">Generate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Notifications --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-bell me-1"></i>Notifications</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.quick-actions.notifications') }}" class="d-flex flex-column gap-2">@csrf
                                <select name="merchant_id" class="form-select form-select-sm" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <div class="d-flex gap-2">
                                    <input type="number" name="count" class="form-control form-control-sm" value="20" min="1" max="200">
                                    <button class="btn btn-sm btn-warning text-nowrap">Generate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Reset Demo --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-danger">
                        <div class="card-header fw-semibold text-danger"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Demo Data</div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">Deletes all members, transactions, campaigns, rewards for the selected merchant.</p>
                            <form method="POST" action="{{ route('dev.quick-actions.reset-demo') }}" onsubmit="return confirm('Reset all demo data for this merchant?')">@csrf
                                <select name="merchant_id" class="form-select form-select-sm mb-2" required>
                                    <option value="">Select merchant…</option>
                                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                                </select>
                                <button class="btn btn-danger w-100 btn-sm"><i class="bi bi-trash me-1"></i>Reset Demo Data</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
