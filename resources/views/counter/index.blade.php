<x-app-layout>
    <x-slot name="title">{{ __('mobile.counter_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('mobile.counter_title') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            {{-- Purchase success banner --}}
            @if (session('purchase_success'))
                @php $ps = session('purchase_success'); @endphp
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    <div>
                        <strong>{{ __('members.purchase_recorded_success') }}</strong><br>
                        <span style="font-size:0.9rem;">
                            {{ $ps['member_name'] ?? '' }} —
                            {{ __('members.earned_label') }}
                            +{{ number_format($ps['earned']) }}
                            {{ $ps['type'] === 'stamps' ? __('members.stamps_unit') : __('members.pts') }}
                            &middot; {{ __('members.balance_label') }}: {{ number_format($ps['balance']) }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- Search --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('counter') }}" class="d-flex gap-2">
                        <input type="search"
                               inputmode="tel"
                               name="q"
                               value="{{ $query }}"
                               class="form-control form-control-lg"
                               placeholder="{{ __('mobile.counter_search_ph') }}"
                               autofocus>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                    <div class="form-text mt-2">{{ __('mobile.counter_search_hint') }}</div>
                </div>
            </div>

            {{-- Results --}}
            @if (! is_null($members))
                @forelse ($members as $member)
                    <div class="card mb-3">
                        <div class="card-body d-flex flex-wrap align-items-center gap-3">
                            <div class="flex-grow-1">
                                <div class="fw-semibold fs-5">{{ $member->name }}</div>
                                <div class="text-muted" style="font-size:0.85rem;">
                                    <i class="bi bi-telephone me-1"></i>{{ $member->phone }}
                                    <span class="ms-3">
                                        <i class="bi bi-star-fill me-1" style="color:#FF1585;"></i>{{ number_format($member->total_points) }} {{ __('members.pts') }}
                                    </span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('members.purchases.store', $member) }}"
                                  class="d-flex gap-2 align-items-center">
                                @csrf
                                <input type="hidden" name="return_to" value="counter">
                                <input type="number"
                                       name="purchase_amount"
                                       class="form-control"
                                       style="width:130px;"
                                       min="1"
                                       step="0.01"
                                       placeholder="{{ __('members.purchase_amount') }}"
                                       required>
                                <button type="submit" class="btn btn-primary text-nowrap">
                                    <i class="bi bi-cash-coin me-1"></i>{{ __('members.record_purchase') }}
                                </button>
                            </form>
                            <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-person"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="card">
                        <div class="card-body text-center text-muted py-5">
                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                            {{ __('mobile.counter_no_results', ['query' => $query]) }}
                            <div class="mt-3">
                                <a href="{{ route('members.create') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-person-plus me-1"></i>{{ __('mobile.counter_add_member') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-upc-scan fs-1 d-block mb-2"></i>
                    {{ __('mobile.counter_intro') }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
