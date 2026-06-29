@component('mail::message')
# {{ __('email.password_changed_heading') }}

{{ __('email.password_changed_body_1', ['name' => $user->name]) }}

{{ __('email.password_changed_body_2') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/settings', 'color' => 'red'])
{{ __('email.password_changed_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
