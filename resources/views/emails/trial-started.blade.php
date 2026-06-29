@component('mail::message')
# {{ __('email.trial_started_heading') }}

{{ __('email.trial_started_body_1', ['name' => $merchant->owner->name ?? '']) }}

@if ($merchant->trial_ends_at)
{{ __('email.trial_started_body_2', ['date' => $merchant->trial_ends_at->format('d M Y')]) }}
@endif

{{ __('email.trial_started_body_3') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/dashboard'])
{{ __('email.trial_started_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
