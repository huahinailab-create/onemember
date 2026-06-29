@component('mail::message')
# {{ __('email.subscription_purchased_heading') }}

{{ __('email.subscription_purchased_body_1', ['name' => $merchant->owner->name ?? '', 'plan' => ucfirst($planKey)]) }}

@component('mail::table')
| | |
| :--- | :--- |
| **{{ __('email.label_plan') }}** | {{ ucfirst($planKey) }} |
@if ($merchant->subscription_renews_at)
| **{{ __('email.label_next_billing') }}** | {{ $merchant->subscription_renews_at->format('d M Y') }} |
@endif
@endcomponent

@component('mail::button', ['url' => config('email.frontend_url') . '/subscription'])
{{ __('email.subscription_purchased_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
