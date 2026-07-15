<x-mail::message>
# {{ __('customer.otp_mail_heading') }}

{{ __('customer.otp_mail_intro') }}

<x-mail::panel>
<div style="text-align:center;font-size:28px;font-weight:700;letter-spacing:0.35em;">{{ $code }}</div>
</x-mail::panel>

{{ __('customer.otp_mail_expiry', ['minutes' => $minutes]) }}

{{ __('customer.otp_mail_ignore') }}

{{ __('customer.otp_mail_signoff') }}<br>
OneMember
</x-mail::message>
