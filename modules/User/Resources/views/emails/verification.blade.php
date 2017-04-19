@component('mail::message')
# {{ trans('people::email.verification.greeting') }}

{{ trans('people::email.verification.text') }}

# {{ $code }}

{{trans('people::email.verification.thanks') }}<br>
{{trans('people::email.verification.sender') }}
@endcomponent