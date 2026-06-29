@component('mail::message')
@if ($merchant->logo_path)
![{{ $merchant->name }}]({{ Storage::disk('public')->url($merchant->logo_path) }})
@endif
**{{ $merchant->name }}** &middot; {{ __('email.powered_by', ['app' => config('app.name')]) }}

---
# {{ __('email.trial_ending_heading', ['days' => $daysRemaining]) }}

{{ __('email.trial_ending_body_1', ['name' => $merchant->owner->name ?? '', 'days' => $daysRemaining]) }}

@if ($merchant->trial_ends_at)
{{ __('email.trial_ending_body_2', ['date' => $merchant->trial_ends_at->format('d M Y')]) }}
@endif

{{ __('email.trial_ending_body_3') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/subscription'])
{{ __('email.trial_ending_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
