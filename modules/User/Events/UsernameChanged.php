<?php

namespace Modules\User\Events;

use Modules\User\Entities\User;
use Illuminate\Queue\SerializesModels;

class UsernameChanged
{
    use SerializesModels;

    public $user;
    
    public $old_username;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @param  string  $old
     * @return void
     */
    public function __construct(User $user, $old)
    {
        $this->user = $user;
        $this->old_username = $old;
    }
}