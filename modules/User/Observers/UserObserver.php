<?php

namespace Modules\User\Observers;

use Modules\User\Entities\User;
use Modules\Stream\Repositories\StreamRepository;

class UserObserver
{

    protected $repository;

    public function __construct(StreamRepository $repository) {
        $this->repository = $repository;
    }
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->repository->recommend($user);
    }

}