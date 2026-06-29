@component('mail::message')
# {{ __('email.payment_failed_heading') }}

{{ __('email.payment_failed_body_1', ['name' => $merchant->owner->name ?? '']) }}

@component('mail::table')
| | |
| :--- | :--- |
| **{{ __('email.label_invoice') }}** | {{ $invoiceId }} |
| **{{ __('email.label_amount') }}** | {{ $amountDue }} |
@endcomponent

{{ __('email.payment_failed_body_2') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/subscription/portal', 'color' => 'red'])
{{ __('email.payment_failed_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
