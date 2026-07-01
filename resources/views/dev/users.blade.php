<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Users</x-slot>

    <div class="d-flex">
        @include('dev._nav')

        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-person-gear me-2 text-warning"></i>Users</h4>

            {{-- Flash --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{!! session('success') !!}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            {{-- Search --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dev.users') }}">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search by email, name, or phone…" value="{{ $query ?? '' }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Results --}}
            @if (!empty($users) && count($users))
                @foreach ($users as $user)
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <span class="text-muted ms-2">{{ $user->email }}</span>
                                @if ($user->trashed())
                                    <span class="badge bg-danger ms-2">Deleted</span>
                                @endif
                                @if ($user->email_verified_at)
                                    <span class="badge bg-success ms-1">Verified</span>
                                @else
                                    <span class="badge bg-warning text-dark ms-1">Unverified</span>
                                @endif
                            </div>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @if ($user->trashed())
                                    <form method="POST" action="{{ route('dev.users.restore', $user) }}">@csrf
                                        <button class="btn btn-sm btn-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dev.users.soft-delete', $user) }}">@csrf
                                        <button class="btn btn-sm btn-warning" onclick="return confirm('Soft delete {{ $user->email }}?')"><i class="bi bi-trash me-1"></i>Soft Delete</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('dev.users.destroy', $user) }}" onsubmit="return confirm('PERMANENTLY delete {{ $user->email }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-x-circle me-1"></i>Force Delete</button>
                                </form>

                                @if (!$user->email_verified_at)
                                    <form method="POST" action="{{ route('dev.users.verify-email', $user) }}">@csrf
                                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-circle me-1"></i>Verify Email</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dev.users.unverify-email', $user) }}">@csrf
                                        <button class="btn btn-sm btn-outline-warning"><i class="bi bi-x-circle me-1"></i>Unverify Email</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('dev.users.resend-verification', $user) }}">@csrf
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-envelope me-1"></i>Resend Verification</button>
                                </form>

                                <form method="POST" action="{{ route('dev.users.temp-password', $user) }}" onsubmit="return confirm('Generate temporary password for {{ $user->email }}?')">@csrf
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-key me-1"></i>Temp Password</button>
                                </form>

                                <form method="POST" action="{{ route('dev.users.login-as', $user) }}" onsubmit="return confirm('Login as {{ $user->email }}?')">@csrf
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Login As</button>
                                </form>

                                <form method="POST" action="{{ route('dev.users.clear-failed-logins', $user) }}">@csrf
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-shield-x me-1"></i>Clear Failed Logins</button>
                                </form>

                                <form method="POST" action="{{ route('dev.users.delete-sessions', $user) }}" onsubmit="return confirm('Delete all sessions for {{ $user->email }}?')">@csrf
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-door-open me-1"></i>Delete Sessions</button>
                                </form>

                                {{-- Reset Password --}}
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#reset-pw-{{ $user->id }}">
                                    <i class="bi bi-lock me-1"></i>Reset Password
                                </button>
                            </div>

                            <div class="collapse mt-3" id="reset-pw-{{ $user->id }}">
                                <form method="POST" action="{{ route('dev.users.reset-password', $user) }}" class="d-flex gap-2">
                                    @csrf
                                    <input type="text" name="password" class="form-control form-control-sm" placeholder="New password (min 8 chars)" style="max-width:250px;">
                                    <button class="btn btn-sm btn-warning" type="submit">Set Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif ($query)
                <div class="alert alert-info">No users found for "{{ $query }}".</div>
            @endif
        </div>
    </div>
</x-app-layout>
