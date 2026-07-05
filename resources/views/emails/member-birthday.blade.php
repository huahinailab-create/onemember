@component('mail::message')
@if ($member->merchant->logo_path)
![{{ $member->merchant->name }}]({{ Storage::disk('public')->url($member->merchant->logo_path) }})
@endif
**{{ $member->merchant->name }}** &middot; {{ __('email.powered_by', ['app' => config('app.name')]) }}

---
# {{ __('email.member_birthday_heading') }}

{{ __('email.member_birthday_body_1', ['name' => $member->nickname ?? $member->name, 'merchant' => $member->merchant->name]) }}

@component('mail::panel')
{{ __('email.member_birthday_points', ['points' => number_format($bonusPoints)]) }}
@endcomponent

{{ __('email.member_birthday_body_2') }}

{{ config('email.company_name') }}
@endcomponent
