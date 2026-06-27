<x-app-layout>
    <x-slot name="title">Dashboard – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-muted mb-0">Here's an overview of your account.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                        <i class="bi bi-people-fill fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Members</div>
                        <div class="fw-bold fs-5">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10">
                        <i class="bi bi-currency-dollar fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Revenue</div>
                        <div class="fw-bold fs-5">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                        <i class="bi bi-clock-history fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Active Plans</div>
                        <div class="fw-bold fs-5">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10">
                        <i class="bi bi-ticket-perforated fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Open Tickets</div>
                        <div class="fw-bold fs-5">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-rocket-takeoff fs-1 text-primary opacity-50 mb-3 d-block"></i>
            <h5 class="fw-semibold">You're logged in!</h5>
            <p class="text-muted mb-3">Complete your merchant profile to get started.</p>
            <a href="{{ route('merchant.profile.edit') }}" class="btn btn-primary">
                <i class="bi bi-shop me-2"></i>Set Up Merchant Profile
            </a>
        </div>
    </div>

</x-app-layout>
