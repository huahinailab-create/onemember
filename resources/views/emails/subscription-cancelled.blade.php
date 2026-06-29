@component('mail::message')
@if ($merchant->logo_path)
![{{ $merchant->name }}]({{ Storage::disk('public')->url($merchant->logo_path) }})
@endif
**{{ $merchant->name }}** &middot; {{ __('email.powered_by', ['app' => config('app.name')]) }}

---
# {{ __('email.subscription_cancelled_heading') }}

{{ __('email.subscription_cancelled_body_1', ['name' => $merchant->owner->name ?? '']) }}

@if ($merchant->subscription_renews_at && $merchant->cancel_at_period_end)
{{ __('email.subscription_cancelled_body_2', ['date' => $merchant->subscription_renews_at->format('d M Y')]) }}
@endif

{{ __('email.subscription_cancelled_body_3') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/subscription'])
{{ __('email.subscription_cancelled_cta') }}
@endcomponent

@if ($merchant->receipt_footer)
{{ $merchant->receipt_footer }}

@endif
{{ config('email.company_name') }}
@endcomponent
