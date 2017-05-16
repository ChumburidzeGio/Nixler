@component('mail::message')
# {{ trans('users.settings.email.verification.greeting') }}

{{ trans('users.settings.email.verification.text') }}

# {{ $code }}

{{trans('users.settings.email.verification.thanks') }}<br>
{{trans('users.settings.email.verification.sender') }}
@endcomponent