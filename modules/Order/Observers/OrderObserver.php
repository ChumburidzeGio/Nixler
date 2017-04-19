<?php

namespace Modules\Order\Observers;

use Modules\Order\Entities\Order;
use Modules\Order\Notifications\OrderStatusChanged;

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