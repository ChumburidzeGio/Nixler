<?php

namespace Modules\Messages\Traits;

use Modules\Messages\Entities\Thread;
use Modules\Messages\Entities\Message;

trait HasMessages {
    
    /**
     * Show comments for model
     */
    public function threads()
    {
        return $this->belongsToMany(Thread::class, 'thread_participants', 'user_id', 'thread_id')->withPivot('last_read');
    }
    
    /**
     * Show comments for model
     */
    public function addThread($private = false)
    {
        return $this->threads()->create([
            'is_private' => $private
        ]);
    }
    
    /**
     * Show comments for model
     */
    public function hermes()
    {
        $user = $this;
        
        auth()->user()->setMeta('has_messages', false);

        return $this->threads()->has('messages')->with(['participants' => function($query) use ($user) {
            return $query->where('thread_participants.user_id', '<>', $user->id);
        }, 'latestMessage'])->orderBy('updated_at', 'desc');
    }


    /**
     * Show comments for model
     */
    public function findOrCreateThreadWith($model)
    {
        $thread = $this->threads()->whereHas('participants', function ($query) use($model) {
            $query->where('thread_participants.user_id', $model->id);
        })->first();

        if(!$thread){
            $thread = $this->addThread(true);
            $thread->addParticipant($this);
            $thread->addParticipant($model);
        }

        return $thread;
    }


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


    /**
     * Show comments for model
     */
    public function messageIn($thread_id, $message)
    {
        $thread = $this->threads()->findOrFail($thread_id);
        $thread->markAsRead();
        return $thread->message($message, $this);
    }


    /**
     * Show comments for model
     */
    public function thread($id)
    {   
        $model = $this;

        $thread = $this->threads()->with(['participants', 'messages' => function($query){
            return $query->orderBy('id', 'desc')->paginate();
        }])->findOrFail($id);   

        $thread->markAsRead();

        return $thread;
    }
    

}