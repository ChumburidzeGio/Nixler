<?php

namespace Modules\Comment\Policies;

use Modules\User\Entities\User;
use Modules\Comment\Entities\Comment;

class CommentPolicy
{
    /**
     * Determine if the given user can create comment.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return auth()->check();
    }

    /**
     * Determine if the given comment can be deleted by the user.
     *
     * @param  User  $user
     * @param  Comment  $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment)
    {
        return !!(auth()->check() && $comment->user_id == $user->id);
    }
}