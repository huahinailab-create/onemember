<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Feature Flags</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-toggles me-2 text-warning"></i>Feature Flags</h4>

            <div class="alert alert-secondary d-flex align-items-start gap-2 mb-4">
                <i class="bi bi-info-circle mt-1"></i>
                <div>
                    Feature flags are read-only here. To change a flag, edit your <code>.env</code> file (local) or the Forge environment variables (staging/production), then run <code>php artisan config:clear</code>.
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th class="ps-3">Variable</th><th>Value</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($flags as $key => $value)
                                @php
                                    $val = $value ?? 'not set';
                                    $isOk = !in_array($val, ['not set', '']) && $val !== 'false' && $val !== '0' && $val !== 'no';
                                    $isDanger = in_array($key, ['APP_DEBUG']) && app()->environment('production');
                                @endphp
                                <tr>
                                    <th class="ps-3"><code>{{ $key }}</code></th>
                                    <td><code>{{ $val }}</code></td>
                                    <td>
                                        @if ($isDanger)
                                            <span class="badge bg-danger">DANGER: true in production</span>
                                        @elseif ($val === 'not set' || $val === '')
                                            <span class="badge bg-warning text-dark">Not set</span>
                                        @elseif ($val === 'false' || $val === '0' || $val === 'no')
                                            <span class="badge bg-secondary">Disabled</span>
                                        @else
                                            <span class="badge bg-success">Set</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header fw-semibold">How to Enable Developer Tools</div>
                <div class="card-body">
                    <h6>Local development</h6>
                    <pre class="bg-light p-2 rounded small mb-3">DEV_TOOLS_ENABLED=true
APP_ENV=local</pre>

                    <h6>Staging (Forge)</h6>
                    <p class="small text-muted">In Forge → Site → Environment, add:</p>
                    <pre class="bg-light p-2 rounded small mb-3">DEV_TOOLS_ENABLED=true
APP_ENV=staging</pre>
                    <p class="small text-muted">Then run: <code>php artisan config:clear</code></p>

                    <h6>Production</h6>
                    <div class="alert alert-danger small mb-0">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Never set <code>DEV_TOOLS_ENABLED=true</code> in production. The middleware also enforces <code>APP_ENV != production</code> as a second guard, but the flag should be <code>false</code> regardless.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
