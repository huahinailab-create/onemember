@extends('layouts.wizard')

@section('title', "You're Ready! – " . config('app.name'))

@section('content')
<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">Step 6 of 6</span>
            <span class="text-muted small">100%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-success" style="width:100%;" role="progressbar"></div>
        </div>
    </div>

    <div class="card-body p-4 p-md-5 text-center">

        <div class="d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 mx-auto mb-4"
             style="width:80px;height:80px;">
            <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
        </div>

        <h2 class="fw-bold fs-3 mb-2">You're ready!</h2>
        <p class="text-muted mb-1">
            <strong>{{ $merchant->name }}</strong> is all set up.
        </p>
        <p class="text-muted mb-4">
            Start adding members and recording purchases to reward their loyalty.
        </p>

        <div class="row g-3 justify-content-center">
            <div class="col-12 col-sm-6">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                </a>
            </div>
            <div class="col-12 col-sm-6">
                <a href="{{ route('members.create') }}" class="btn btn-outline-primary btn-lg w-100">
                    <i class="bi bi-person-plus me-2"></i>Add First Member
                </a>
            </div>
        </div>

    </div>
</div>

<p class="text-center text-muted small mt-3">
    You can update your business details any time in
    <a href="{{ route('settings') }}">Settings</a>.
</p>
@endsection
