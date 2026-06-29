@component('mail::message')
# {{ __('email.subscription_renewed_heading') }}

{{ __('email.subscription_renewed_body_1', ['name' => $merchant->owner->name ?? '', 'plan' => ucfirst($planKey)]) }}

@component('mail::table')
| | |
| :--- | :--- |
| **{{ __('email.label_plan') }}** | {{ ucfirst($planKey) }} |
| **{{ __('email.label_next_billing') }}** | {{ $renewsAt->format('d M Y') }} |
@endcomponent

@component('mail::button', ['url' => config('email.frontend_url') . '/subscription'])
{{ __('email.subscription_renewed_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
