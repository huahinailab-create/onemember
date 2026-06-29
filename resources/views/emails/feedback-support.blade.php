@component('mail::message')
# {{ __('email.feedback_support_heading') }}

**{{ __('email.label_from') }}:** {{ $user->name }} ({{ $user->email }})

**{{ __('email.label_category') }}:** {{ ucfirst($feedback['category'] ?? 'general') }}

**{{ __('email.label_subject') }}:** {{ $feedback['subject'] ?? '' }}

---

{{ $feedback['message'] ?? '' }}

---

@component('mail::table')
| | |
| :--- | :--- |
| **{{ __('email.label_user_id') }}** | {{ $feedback['user_id'] ?? '' }} |
| **{{ __('email.label_merchant_id') }}** | {{ $feedback['merchant_id'] ?? '-' }} |
| **{{ __('email.label_submitted_at') }}** | {{ $feedback['submitted_at'] ?? '' }} |
| **{{ __('email.label_url') }}** | {{ $feedback['current_url'] ?? '' }} |
@endcomponent

{{ config('email.company_name') }}
@endcomponent
