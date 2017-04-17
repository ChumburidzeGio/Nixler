<?php

namespace Modules\User\Observers;

use Modules\User\Entities\User;
use Modules\Stream\Repositories\StreamRepository;
use Modules\Stream\Services\RecommService;

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

        $user->emails()->create([
            'address' => $user->email
        ]);
    }

    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function updating(User $user)
    {
        if ($user->username != $user->getOriginal('username')) {
            $user->updateUsernameCallback($user->getOriginal('username'), $user->username);
        }
    }

    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function saved(User $user)
    {
        (new RecommService)->addUser($user);
    }

    /**
     * Listen to the Product deleting event.
     *
     * @param  Product  $product
     * @return void
     */
    public function deleting(User $user)
    {
        (new RecommService)->removeUser($user);
    }

}