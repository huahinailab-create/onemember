<x-app-layout>
    <x-slot name="title">Dashboard – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ Auth::user()->name }}</p>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                <i class="bi bi-speedometer2 text-primary"></i>
            </div>
            <h5 class="fw-semibold mb-2">Dashboard coming soon</h5>
            <p class="text-muted mb-0" style="max-width: 380px; margin: 0 auto;">
                Your dashboard overview is under development and will be available in a future sprint.
            </p>
        </div>
    </div>

</x-app-layout>
