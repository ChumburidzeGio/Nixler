<?php

namespace App\Events;

use App\Entities\User;
use App\Entities\Order;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public $name = 'order:created';

    public $actor;
    
    public $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, User $actor)
    {
        $this->order = $order;

        $this->actor = $actor;
    }

}