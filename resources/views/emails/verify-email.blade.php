@component('mail::message')
# Hello!

Please click the button below to verify your email address.

@component('mail::button', ['url' => $url])
{{ __('app.verify_email') }}
@endcomponent

If you did not create an account, no further action is required.
<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent