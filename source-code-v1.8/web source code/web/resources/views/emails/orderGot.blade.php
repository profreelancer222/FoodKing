@component('mail::message')
    # Order Notification

    Order ID : {{$orderId}}
    {{$message}}

    Thanks,
    {{ config('app.name') }}
@endcomponent
