<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Helpers</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-magic me-2 text-warning"></i>Development Helpers</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header fw-semibold">Generate Fake Members</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.helpers.generate-members') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Merchant</label>
                                    <select name="merchant_id" class="form-select" required>
                                        <option value="">Select merchant…</option>
                                        @foreach ($merchants as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Count</label>
                                    <div class="d-flex gap-2">
                                        <input type="number" name="count" class="form-control" value="10" min="1" max="1000">
                                        <button class="btn btn-warning text-nowrap"><i class="bi bi-people me-1"></i>Generate</button>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ([10, 50, 100, 1000] as $n)
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="this.form.count.value={{ $n }}">{{ $n }} members</button>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header fw-semibold">Generate Test Transactions</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dev.helpers.generate-transactions') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Merchant</label>
                                    <select name="merchant_id" class="form-select" required>
                                        <option value="">Select merchant…</option>
                                        @foreach ($merchants as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Count</label>
                                    <div class="d-flex gap-2">
                                        <input type="number" name="count" class="form-control" value="50" min="1" max="500">
                                        <button class="btn btn-warning text-nowrap"><i class="bi bi-arrow-left-right me-1"></i>Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
