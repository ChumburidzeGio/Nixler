@component('mail::message')
# Product Sold

Someone bought your product! Please go to order page and confirm the order. If the product is out of stock or you cant deliver you can also cancel the order.

@component('mail::button', ['url' => $url])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent