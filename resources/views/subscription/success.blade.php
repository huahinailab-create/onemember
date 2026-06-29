<x-app-layout>
    <x-slot name="title">{{ __('subscription.success_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('subscription.success_title') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-3">{{ __('subscription.success_title') }}</h2>
                    <p class="text-muted mb-4">{{ __('subscription.success_body') }}</p>

                    @if ($merchant)
                        <dl class="row text-start mb-4 mx-2">
                            <dt class="col-6 text-muted fw-normal">{{ __('subscription.plan') }}</dt>
                            <dd class="col-6 fw-semibold">
                                {{ config("subscriptions.plans.{$merchant->subscription_plan?->value}.name", ucfirst($merchant->subscription_plan?->value ?? 'Active')) }}
                            </dd>
                            <dt class="col-6 text-muted fw-normal">{{ __('subscription.status') }}</dt>
                            <dd class="col-6">
                                <span class="badge {{ $merchant->subscriptionStatus()->badgeClass() }}">
                                    {{ $merchant->subscriptionStatus()->label() }}
                                </span>
                            </dd>
                            @if ($merchant->subscription_renews_at)
                                <dt class="col-6 text-muted fw-normal">{{ __('subscription.renews_on') }}</dt>
                                <dd class="col-6">{{ $merchant->subscription_renews_at->format('d M Y') }}</dd>
                            @endif
                        </dl>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">
                        <i class="bi bi-speedometer2 me-2"></i>{{ __('subscription.success_cta') }}
                    </a>
                    <a href="{{ route('subscription.index') }}" class="btn btn-outline-secondary ms-2 px-4">
                        {{ __('subscription.title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
