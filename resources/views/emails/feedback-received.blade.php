@component('mail::message')
# {{ __('email.feedback_thankyou_heading') }}

{{ __('email.feedback_thankyou_body_1', ['name' => $user->name]) }}

{{ __('email.feedback_thankyou_body_2', ['category' => $feedback['category'] ?? 'general']) }}

{{ __('email.feedback_thankyou_body_3') }}

{{ config('email.company_name') }}
@endcomponent
