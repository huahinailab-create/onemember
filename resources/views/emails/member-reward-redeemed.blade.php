@component('mail::message')
@if ($member->merchant->logo_path)
![{{ $member->merchant->name }}]({{ Storage::disk('public')->url($member->merchant->logo_path) }})
@endif
**{{ $member->merchant->name }}** &middot; {{ __('email.powered_by', ['app' => config('app.name')]) }}

---
# {{ __('email.member_reward_redeemed_heading') }}

{{ __('email.member_reward_redeemed_body_1', ['name' => $member->nickname ?? $member->name, 'reward' => $reward->name, 'merchant' => $member->merchant->name]) }}

@component('mail::panel')
{{ __('email.member_reward_redeemed_points', ['points' => number_format($redemption->points_used)]) }}

{{ __('email.member_reward_redeemed_balance', ['balance' => number_format($member->total_points)]) }}
@endcomponent

{{ __('email.member_reward_redeemed_body_2') }}

{{ config('email.company_name') }}
@endcomponent
