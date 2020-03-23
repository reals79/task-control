@component('mail::message')
# Hello!

You are receiving this email because we received a password reset request for your account.

@component('mail::button', ['url' => $url])
{{ __('app.reset_password') }}
@endcomponent

<br>
This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.
<br>
If you did not request a password reset, no further action is required.
<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent