<?php

namespace Modules\User\Observers;

use Modules\User\Entities\User;
use Modules\Stream\Repositories\StreamRepository;
use Modules\Address\Repositories\ShippingRepository;
use Modules\Stream\Services\RecommService;
use Modules\Address\Entities\Country;

class UserObserver
{

    protected $streamRepo;

    protected $shippingRepo;

    public function __construct(StreamRepository $streamRepo, ShippingRepository $shippingRepo) {
        $this->streamRepo = $streamRepo;
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
        $user->emails()->create([
            'address' => $user->email
        ]);
/*
        if($user->country) {
 
            $this->shippingRepo->settingsUpdate([
                'delivery_full' => true,
                'has_return' => true,
                'return_policy' => ''
            ], $user);
        
        }
*/
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

        if ($user->currency != $user->getOriginal('currency')) {
            $this->streamRepo->recommend($user);
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