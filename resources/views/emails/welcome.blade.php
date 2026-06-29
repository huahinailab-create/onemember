@component('mail::message')
# {{ __('email.welcome_heading', ['name' => config('email.company_name')]) }}

{{ __('email.welcome_body_1', ['name' => $user->name]) }}

{{ __('email.welcome_body_2') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/dashboard'])
{{ __('email.welcome_cta') }}
@endcomponent

{{ __('email.welcome_footer') }}

{{ config('email.company_name') }}
@endcomponent
