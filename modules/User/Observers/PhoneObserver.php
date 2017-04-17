<?php

namespace Modules\User\Observers;

use Modules\User\Entities\Phone;

class PhoneObserver
{

    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(Phone $phone)
    {}

}