@component('mail::message')
# Order {{ $status }}

The status of order is changed. Please go orders page for more information.

@component('mail::button', ['url' => $url])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent