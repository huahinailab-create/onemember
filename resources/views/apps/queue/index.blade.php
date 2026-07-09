<x-app-layout>
    <x-slot name="title">{{ __('queue.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('queue.title') }}</x-slot>

    <x-ui.page-header :title="__('queue.title')" :subtitle="__('queue.subtitle')">
        <a href="{{ route('queue.display') }}" class="btn btn-outline-primary">
            <i class="bi bi-tv me-1"></i>{{ __('queue.open_display') }}
        </a>
    </x-ui.page-header>

    <x-ui.flash :session="false" :with-errors="true" />

    {{-- Today stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><x-ui.stat-card icon="bi-ticket-perforated" :label="__('queue.stat_issued')" :value="$stats['issued']" /></div>
        <div class="col-6 col-md-3"><x-ui.stat-card icon="bi-hourglass-split" :label="__('queue.stat_waiting')" :value="$stats['waiting']" /></div>
        <div class="col-6 col-md-3"><x-ui.stat-card icon="bi-person-check" :label="__('queue.stat_done')" :value="$stats['done']" variant="pink" /></div>
        <div class="col-6 col-md-3"><x-ui.stat-card icon="bi-stopwatch" :label="__('queue.stat_avg_wait')" :value="$stats['avg_wait_minutes'] !== null ? $stats['avg_wait_minutes'] . ' ' . __('queue.minutes_unit') : '—'" variant="pink" /></div>
    </div>

    <div class="row g-4">
        {{-- New ticket --}}
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-plus-circle me-2"></i>{{ __('queue.new_ticket') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('queue.tickets.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="q-name">{{ __('queue.customer_name') }}</label>
                            <input type="text" id="q-name" name="customer_name" maxlength="150" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="q-phone">{{ __('queue.customer_phone') }}</label>
                            <input type="tel" id="q-phone" name="customer_phone" maxlength="30" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="q-type">{{ __('queue.type') }}</label>
                            <select id="q-type" name="type" class="form-select form-select-sm">
                                <option value="walk_in">{{ __('queue.type_walk_in') }}</option>
                                <option value="reservation">{{ __('queue.type_reservation') }}</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="q-reserved">{{ __('queue.reserved_for') }}</label>
                            <input type="datetime-local" id="q-reserved" name="reserved_for" class="form-control form-control-sm">
                            @error('reserved_for')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="q-priority" name="priority" value="1">
                            <label class="form-check-label" for="q-priority">{{ __('queue.priority') }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-ticket-perforated me-1"></i>{{ __('queue.issue_ticket') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Counters --}}
            <div class="card mt-4">
                <div class="card-header fw-semibold"><i class="bi bi-signpost-2 me-2"></i>{{ __('queue.counters') }}</div>
                <div class="card-body">
                    @foreach ($counters as $counter)
                        <div class="d-flex justify-content-between small py-1">
                            <span class="fw-medium">{{ $counter->name }}</span>
                            <span class="text-muted">{{ $counter->staff_name ?? '—' }}</span>
                        </div>
                    @endforeach
                    <form method="POST" action="{{ route('queue.counters.store') }}" class="d-flex gap-2 mt-2">
                        @csrf
                        <input type="text" name="name" required maxlength="100" class="form-control form-control-sm"
                               placeholder="{{ __('queue.counter_name_ph') }}" aria-label="{{ __('queue.counter_name_ph') }}">
                        <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap">+</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Board --}}
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-semibold"><i class="bi bi-megaphone me-2"></i>{{ __('queue.now_serving') }}</div>
                <div class="card-body p-0">
                    @if ($active->isEmpty())
                        <x-ui.empty-state icon="bi-megaphone" :title="__('queue.none_serving')" />
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($active as $ticket)
                                <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                    <span class="fs-4 fw-bold" style="color:var(--om-navy);">#{{ $ticket->number }}</span>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $ticket->customer_name ?? __('queue.walk_in_guest') }}</div>
                                        <div class="text-muted small">{{ $ticket->counter?->name ?? '—' }}</div>
                                    </div>
                                    <x-ui.status-badge :status="$ticket->status" :label="__('queue.status_' . $ticket->status)" />
                                    <div class="d-flex gap-1">
                                        @foreach (\App\Apps\Queue\Models\QueueTicket::TRANSITIONS[$ticket->status] ?? [] as $next)
                                            <form method="POST" action="{{ route('queue.tickets.status', $ticket) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="{{ $next }}">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('queue.action_' . $next) }}</button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-hourglass-split me-2"></i>{{ __('queue.waiting_line') }}</div>
                <div class="card-body p-0">
                    @if ($waiting->isEmpty())
                        <x-ui.empty-state icon="bi-emoji-smile" :title="__('queue.empty_line')" :body="__('queue.empty_line_body')" />
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($waiting as $ticket)
                                <div class="list-group-item d-flex align-items-center gap-3 py-2">
                                    <span class="fw-bold" style="color:var(--om-navy);">#{{ $ticket->number }}</span>
                                    @if ($ticket->priority)
                                        <span class="badge bg-warning text-dark">{{ __('queue.priority') }}</span>
                                    @endif
                                    @if ($ticket->type === 'reservation')
                                        <span class="badge bg-info text-dark">{{ __('queue.type_reservation') }}</span>
                                    @endif
                                    <span class="flex-grow-1 small">{{ $ticket->customer_name ?? __('queue.walk_in_guest') }}</span>
                                    <form method="POST" action="{{ route('queue.tickets.status', $ticket) }}" class="d-flex gap-1 align-items-center">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="called">
                                        @if ($counters->isNotEmpty())
                                            <select name="queue_counter_id" class="form-select form-select-sm" style="width:auto;"
                                                    aria-label="{{ __('queue.counters') }}">
                                                @foreach ($counters as $counter)
                                                    <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-primary">{{ __('queue.action_called') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('queue.tickets.status', $ticket) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                aria-label="{{ __('queue.action_cancelled') }}: #{{ $ticket->number }}">
                                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
