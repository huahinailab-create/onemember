@component('mail::message')
# {{ __('email.winback_heading') }}

{{ __('email.winback_body_1', ['name' => $merchant->owner->name ?? '', 'count' => $members->count(), 'days' => $days]) }}

@component('mail::table')
| {{ __('email.winback_col_member') }} | {{ __('email.winback_col_phone') }} | {{ __('email.winback_col_last_visit') }} |
|:--|:--|:--|
@foreach ($members as $member)
| {{ $member->name }} | {{ $member->phone }} | {{ $member->last_activity_at?->format('d M Y') ?? '—' }} |
@endforeach
@endcomponent

{{ __('email.winback_body_2') }}

@component('mail::button', ['url' => config('email.frontend_url') . '/members'])
{{ __('email.winback_cta') }}
@endcomponent

{{ config('email.company_name') }}
@endcomponent
