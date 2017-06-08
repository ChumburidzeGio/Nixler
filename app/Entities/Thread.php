<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use DB;

class Thread extends Model
{
    use ValidatingTrait;

    public $table = 'threads';

    protected $fillable  = [
        'subject', 'is_private'
    ];

    protected $rules = [
        'subject'   => 'string',
        'is_private'   => 'required|boolean'
    ];

    protected $throwValidationExceptions = true;

    
    /**
     * Show comments for model
     */
    public function participants()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'thread_participants', 'thread_id', 'user_id')->withPivot('last_read');
    }
    
    /**
     * Show comments for model
     */
    public function latestMessage()
    {
        return $this->messages()->latest('id')->nPerGroup(null, 'thread_id', 1);

        //->nPerGroup(null, 'thread_id', 1);
    }
    
    /**
     * Show comments for model
     */
    public function latestFiveMessage()
    {
        return $this->messages()->orderBy('id','desc')->nPerGroup(null, 'thread_id', 5);
    }
    
    /**
     * Show comments for model
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    /**
     * Add user to conversation
     */
    public function addParticipant($model)
    {
        if($this->participants()->where('user_id', $model->id)->exists()){
            return null;
        }

        return Participant::create([
            'user_id' => $model->id,
            'thread_id' => $this->id,
            'last_read' => (new \Carbon\Carbon)
        ]);
    }
    
    
    /**
     * Add message to conversation
     */
    public function getUnreadAttribute()
    {
        $isUnread = $this->pivot->last_read < $this->latestMessage->first()->created_at;
        $isMine = $this->latestMessage->first()->user_id == auth()->id();
        return ($isUnread && !$isMine);
    }
    
    /**
     * Add message to conversation
     */
    public function markAsRead()
    {
        Participant::where('user_id', auth()->id())->where('thread_id', $this->id)->update([
            'last_read' => (new \Carbon\Carbon)
        ]);
    }


    /**
     * Get all threads which has at last one participant appart from 
     * current user, to load just messages with existing accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithParticipantsExcept($query, $user)
    {
        return $query->whereHas('participants', function ($query) use ($user) {
            return $query->where('thread_participants.user_id', '<>', $user->id);
        })->with(['participants' => function($query) use ($user) {
            return $query->where('thread_participants.user_id', '<>', $user->id);
        }]);
    }
}