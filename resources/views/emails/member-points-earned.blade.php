@component('mail::message')
@if ($member->merchant->logo_path)
![{{ $member->merchant->name }}]({{ Storage::disk('public')->url($member->merchant->logo_path) }})
@endif
**{{ $member->merchant->name }}** &middot; {{ __('email.powered_by', ['app' => config('app.name')]) }}

---
# {{ __('email.member_points_earned_heading') }}

{{ __('email.member_points_earned_body_1', ['name' => $member->nickname ?? $member->name, 'merchant' => $member->merchant->name]) }}

@component('mail::panel')
{{ __('email.member_points_earned_points', ['points' => number_format($transaction->points)]) }}

{{ __('email.member_points_earned_balance', ['balance' => number_format($transaction->balance_after)]) }}
@endcomponent

{{ __('email.member_points_earned_body_2') }}

{{ config('email.company_name') }}
@endcomponent
