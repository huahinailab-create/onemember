<x-app-layout>
    <x-slot name="title">{{ __('queue.display_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('queue.display_title') }}</x-slot>

    {{-- Read-only board for a counter-top screen; auto-refreshes. --}}
    <meta http-equiv="refresh" content="15">

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold fs-5"><i class="bi bi-megaphone me-2 text-primary"></i>{{ __('queue.now_serving') }}</div>
                <div class="card-body text-center">
                    @forelse ($called as $ticket)
                        <div class="py-2">
                            <span class="display-4 fw-bold" style="color:var(--om-navy);">#{{ $ticket->number }}</span>
                            <div class="text-muted">{{ $ticket->counter?->name ?? '' }}</div>
                        </div>
                    @empty
                        <p class="text-muted my-5">{{ __('queue.none_serving') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold fs-5"><i class="bi bi-hourglass-split me-2 text-primary"></i>{{ __('queue.waiting_line') }}</div>
                <div class="card-body">
                    @forelse ($waiting as $ticket)
                        <span class="badge {{ $ticket->priority ? 'bg-warning text-dark' : 'bg-light text-secondary border' }} fs-6 me-2 mb-2">
                            #{{ $ticket->number }}
                        </span>
                    @empty
                        <p class="text-muted my-5 text-center">{{ __('queue.empty_line') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
