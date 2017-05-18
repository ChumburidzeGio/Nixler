<?php

namespace App\Policies;

use App\Entities\User;
use App\Entities\Order;

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
        if($user->id != $order->merchant_id) {
            return false;
        }

        if($status == 'closed'){
            return $order->isStatus('sent');
        } 

        elseif($status == 'rejected' || $status == 'confirmed') {
            return $order->isStatus('created');
        }

        elseif($status == 'sent') {
            return $order->isStatus('confirmed');
        }

        return false;
    }
}