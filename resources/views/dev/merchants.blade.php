<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Merchant</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-shop-window me-2 text-warning"></i>Merchants</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            @foreach ($merchants as $merchant)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <strong>{{ $merchant->name }}</strong>
                            <span class="text-muted ms-2 small">{{ $merchant->user?->email }}</span>
                            @if ($merchant->trashed()) <span class="badge bg-danger ms-1">Deleted</span> @endif
                            @if ($merchant->subscription_plan) <span class="badge bg-info ms-1">{{ $merchant->subscription_plan->value }}</span> @endif
                        </div>
                        <small class="text-muted">ID: {{ $merchant->id }}</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @if ($merchant->trashed())
                                <form method="POST" action="{{ route('dev.merchants.restore', $merchant) }}">@csrf<button class="btn btn-sm btn-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button></form>
                            @else
                                <form method="POST" action="{{ route('dev.merchants.archive', $merchant) }}">@csrf<button class="btn btn-sm btn-warning" onclick="return confirm('Archive?')"><i class="bi bi-archive me-1"></i>Archive</button></form>
                            @endif
                            <form method="POST" action="{{ route('dev.merchants.destroy', $merchant) }}" onsubmit="return confirm('PERMANENTLY delete merchant and all data?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-x-circle me-1"></i>Force Delete</button></form>
                            <form method="POST" action="{{ route('dev.merchants.reset-onboarding', $merchant) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Reset onboarding?')"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Onboarding</button></form>
                            <form method="POST" action="{{ route('dev.merchants.reset-subscription', $merchant) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Reset subscription?')"><i class="bi bi-credit-card me-1"></i>Reset Subscription</button></form>
                            <form method="POST" action="{{ route('dev.merchants.activate-trial', $merchant) }}">@csrf<input type="hidden" name="days" value="30"><button class="btn btn-sm btn-outline-success"><i class="bi bi-play-circle me-1"></i>Activate Trial (30d)</button></form>
                            <form method="POST" action="{{ route('dev.merchants.expire-trial', $merchant) }}">@csrf<button class="btn btn-sm btn-outline-warning" onclick="return confirm('Expire trial?')"><i class="bi bi-clock me-1"></i>Expire Trial</button></form>
                            <form method="POST" action="{{ route('dev.merchants.reset-billing', $merchant) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Clear Stripe billing?')"><i class="bi bi-bank me-1"></i>Reset Billing</button></form>
                            <form method="POST" action="{{ route('dev.merchants.reset-loyalty', $merchant) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Reset loyalty program settings?')"><i class="bi bi-star me-1"></i>Reset Loyalty</button></form>
                            <form method="POST" action="{{ route('dev.merchants.reset-campaigns', $merchant) }}" onsubmit="return confirm('Delete ALL campaigns?')">@csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-megaphone me-1"></i>Reset Campaigns</button></form>
                            <form method="POST" action="{{ route('dev.merchants.delete-data', $merchant) }}" onsubmit="return confirm('Delete ALL merchant data (members, transactions, etc)?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Delete All Data</button></form>
                        </div>

                        {{-- Change Plan --}}
                        <form method="POST" action="{{ route('dev.merchants.change-plan', $merchant) }}" class="d-flex gap-2 mt-1">
                            @csrf
                            <select name="plan" class="form-select form-select-sm" style="width:180px;">
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->value }}" @selected($merchant->subscription_plan === $plan)>{{ $plan->value }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-repeat me-1"></i>Change Plan</button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if ($merchants->isEmpty())
                <div class="alert alert-info">No merchants found.</div>
            @endif
        </div>
    </div>
</x-app-layout>
