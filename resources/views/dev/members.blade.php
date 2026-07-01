<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Members</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-people-fill me-2 text-warning"></i>Members</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dev.members') }}" class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="q" class="form-control" placeholder="Search by name, email, phone, or code…" value="{{ $query ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <select name="merchant_id" class="form-select">
                                <option value="">— Or filter by merchant —</option>
                                @foreach ($merchants as $m)
                                    <option value="{{ $m->id }}" @selected(request('merchant_id') == $m->id)>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100"><i class="bi bi-search"></i> Search</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($members as $member)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <strong>{{ $member->name }}</strong>
                            <span class="text-muted ms-2 small">{{ $member->member_code }}</span>
                            @if ($member->trashed()) <span class="badge bg-danger ms-1">Archived</span> @endif
                        </div>
                        <small class="text-muted">{{ $member->merchant?->name ?? '—' }} · {{ $member->total_points }} pts</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @if ($member->trashed())
                                <form method="POST" action="{{ route('dev.members.restore', $member) }}">@csrf<button class="btn btn-sm btn-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button></form>
                            @else
                                <form method="POST" action="{{ route('dev.members.archive', $member) }}">@csrf<button class="btn btn-sm btn-warning" onclick="return confirm('Archive member?')"><i class="bi bi-archive me-1"></i>Archive</button></form>
                            @endif
                            <form method="POST" action="{{ route('dev.members.destroy', $member) }}" onsubmit="return confirm('PERMANENTLY delete member?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-x-circle me-1"></i>Force Delete</button></form>
                            <form method="POST" action="{{ route('dev.members.reset-points', $member) }}">@csrf<button class="btn btn-sm btn-outline-warning" onclick="return confirm('Reset points to 0?')"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Points</button></form>
                            <form method="POST" action="{{ route('dev.members.reset-stamps', $member) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Reset stamps?')"><i class="bi bi-grid me-1"></i>Reset Stamps</button></form>
                            <form method="POST" action="{{ route('dev.members.delete-transactions', $member) }}" onsubmit="return confirm('Delete ALL transactions?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Del Transactions</button></form>
                            <form method="POST" action="{{ route('dev.members.delete-redemptions', $member) }}" onsubmit="return confirm('Delete ALL redemptions?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Del Redemptions</button></form>
                            <form method="POST" action="{{ route('dev.members.delete-notifications', $member) }}" onsubmit="return confirm('Delete notifications?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-secondary"><i class="bi bi-bell-slash me-1"></i>Del Notifications</button></form>
                            <form method="POST" action="{{ route('dev.members.regenerate-qr', $member) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Regenerate QR?')"><i class="bi bi-qr-code me-1"></i>Regen QR</button></form>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#pts-{{ $member->id }}"><i class="bi bi-plus-circle me-1"></i>Adjust Points</button>
                        </div>
                        <div class="collapse mt-2" id="pts-{{ $member->id }}">
                            <div class="d-flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('dev.members.set-points', $member) }}" class="d-flex gap-1">@csrf
                                    <input type="number" name="points" class="form-control form-control-sm" placeholder="Set total" style="width:110px;">
                                    <button class="btn btn-sm btn-secondary">Set</button>
                                </form>
                                <form method="POST" action="{{ route('dev.members.add-points', $member) }}" class="d-flex gap-1">@csrf
                                    <input type="number" name="points" class="form-control form-control-sm" placeholder="Add" style="width:90px;">
                                    <button class="btn btn-sm btn-success">+ Add</button>
                                </form>
                                <form method="POST" action="{{ route('dev.members.deduct-points', $member) }}" class="d-flex gap-1">@csrf
                                    <input type="number" name="points" class="form-control form-control-sm" placeholder="Deduct" style="width:90px;">
                                    <button class="btn btn-sm btn-warning">− Deduct</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
