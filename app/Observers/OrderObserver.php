<?php

namespace App\Observers;

use App\Entities\Order;
use App\Notifications\OrderStatusChanged;

class OrderObserver
{
    /**
     * Listen to the Order created event.
     *
     * @param  Order  $order
     * @return void
     */
    public function saved(Order $order)
    {
        $order->notify(new OrderStatusChanged());
    }

}