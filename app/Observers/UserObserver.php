<?php

namespace App\Observers;

use App\Entities\User;
use App\Repositories\UserRepository;
use App\Repositories\ShippingRepository;
use App\Services\RecommService;
use App\Entities\Country;

class UserObserver
{

    protected $repository;

    protected $shippingRepo;

    public function __construct(UserRepository $repository, ShippingRepository $shippingRepo) {
        $this->repository = $repository;
        $this->shippingRepo = $shippingRepo;
    }

    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->repository->recommendProducts($user);
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

            $user->products()->where('owner_username', $user->getOriginal('username'))->update([
                'owner_username' => $user->username
            ]);

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