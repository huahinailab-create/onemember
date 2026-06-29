@component('mail::message')
# {{ __('email.verified_heading') }}

{{ __('email.verified_body_1', ['name' => $user->name]) }}

{{ __('email.verified_body_2') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/dashboard'])
{{ __('email.verified_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
