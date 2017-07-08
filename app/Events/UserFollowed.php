<?php

namespace App\Events;

use App\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserFollowed
{
    use Dispatchable, SerializesModels;

    public $name = 'user:followed';

    public $actor;
    
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, User $actor)
    {
        $this->user = $user;

        $this->actor = $actor;
    }

}
