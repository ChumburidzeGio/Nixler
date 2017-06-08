<?php

namespace App\Traits;

use App\Entities\Thread;
use App\Entities\Message;

trait HasMessages {


    /**
     * Show comments for model
     */
    public function messages()
    {
        $thread = $this->findOrCreateThreadWith($model);

        return $thread->message($text, $this);

    }


    /**
     * Show comments for model
     */
    public function message($text, $model)
    {
        return $this->hasMany(Message::class);

    }

}