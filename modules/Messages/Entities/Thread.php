<?php

namespace Modules\Messages\Entities;

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
        return $this->messages()->orderBy('id','desc')->nPerGroup('thread_id', 1);
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
    public function message($text, $model)
    {
        $this->participants()->where('users.id', '<>', $model->id)->get()->map(function($user){
            $user->setMeta('has_messages', true);
        });

        return Message::create([
            'user_id' => $model->id,
            'thread_id' => $this->id,
            'body' => $text
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
    
}