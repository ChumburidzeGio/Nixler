<?php

namespace App\Policies;

use App\Entities\User;

class UserPolicy
{
    /**
     * Determine if the given user can be updated by the user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function update(User $user, User $target)
    {
        return !!(auth()->check() && $target->id == $user->id);
    }
}