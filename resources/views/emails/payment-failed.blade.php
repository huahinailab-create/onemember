@component('mail::message')
@if ($merchant->logo_path)
![{{ $merchant->name }}]({{ Storage::disk('public')->url($merchant->logo_path) }})
@endif
**{{ $merchant->name }}** &middot; {{ __('email.powered_by', ['app' => config('app.name')]) }}

---
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

@if ($merchant->receipt_footer)
{{ $merchant->receipt_footer }}

@endif
{{ config('email.company_name') }}
@endcomponent
