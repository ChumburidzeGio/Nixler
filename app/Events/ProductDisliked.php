<?php

namespace App\Events;

use App\Entities\User;
use App\Entities\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ProductDisliked
{
    use Dispatchable, SerializesModels;

    public $name = 'product:disliked';

    public $actor;

    public $product;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product, User $actor)
    {
        $this->product = $product;

        $this->actor = $actor;
    }

}
