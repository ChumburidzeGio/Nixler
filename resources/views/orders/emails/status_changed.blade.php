@component('mail::message')
# @lang('Order status changed to') {{ $status }}

@lang('The status of order is changed. Please go orders page for more information.')'

@component('mail::button', ['url' => $url])
@lang('View Order')
@endcomponent

@lang('Thanks'),<br>
{{ config('app.name') }}
@endcomponent