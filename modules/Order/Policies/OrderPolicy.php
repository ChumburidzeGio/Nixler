<?php

namespace Modules\Order\Policies;

use Modules\User\Entities\User;
use Modules\Order\Entities\Order;

class OrderPolicy
{
    /**
     * Determine if the given user can be updated by the user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function updateStatus(User $user, Order $order, $status)
    {
        $is_merchant = ($user->id == $order->merchant_id);

        if($status == 'closed'){
            return ($user->id == $order->user_id && $order->isStatus('sent'));
        } 

        elseif($status == 'rejected' || $status == 'confirmed') {
            return ($is_merchant && $order->isStatus('created'));
        }

        elseif($status == 'sent') {
            return ($is_merchant && $order->isStatus('confirmed'));
        }

        return false;
    }
}