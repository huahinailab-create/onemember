@component('mail::message')
# {{ __('email.subscription_cancelled_heading') }}

{{ __('email.subscription_cancelled_body_1', ['name' => $merchant->owner->name ?? '']) }}

@if ($merchant->subscription_renews_at && $merchant->cancel_at_period_end)
{{ __('email.subscription_cancelled_body_2', ['date' => $merchant->subscription_renews_at->format('d M Y')]) }}
@endif

{{ __('email.subscription_cancelled_body_3') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/subscription'])
{{ __('email.subscription_cancelled_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
