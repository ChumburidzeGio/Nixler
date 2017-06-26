<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\User;
use App\Entities\Message;
use App\Entities\Participant;
use Carbon\Carbon;
use stdClass;

class MessengerRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return User::class;
    }


    /**
     * Find thread by ID
     */
    public function getAllThreads()
    {
        $user = auth()->user();

        $user->notifications_count = 0;

        $user->save();

        $threads = $user->threads()->has('messages')->with('latestMessage')->withParticipantsExcept($user)->latest('updated_at')->paginate(30);

        return $threads->map(function($item) {

            $message = !is_null($item->latestMessage) ? $item->latestMessage->first() : null;
            $ps = $item->participants;

            return [
                'id' => $item->id,
                'link' => route('threads', ['id' => $item->id]),
                'photo' => $ps->first() ? $ps->first()->avatar('comments') : null,
                'name' => $ps->pluck('name')->implode(', '),
                'message' => $message ? strip_tags($message->body_parsed) : '',
                'last_replied' =>$message ? !!($message->user_id == auth()->user()->id) : false,
                'unread' => $item->unread
            ];

        });
    }


    /**
     * Find thread by ID
     */
    public function findThreadById($id)
    {
        $user = auth()->user();

        $rThread = $user->threads()->with(['participants'])->findOrFail($id);   

        $messages = $rThread->messages()->latest('id')->paginate();

        $rThread->markAsRead();

        $thread = new stdClass;
        
        $thread->id = $rThread->id;

        $thread->title = $rThread->subject ? : $rThread->participants->filter(function($user){
            return ($user->id != auth()->user()->id);
        })->pluck('name')->implode(', ');

        $participantsKeyed = $rThread->participants->keyBy('id');

        $thread->messages = $messages->map(function($item) use ($participantsKeyed) {
            return [
                'id' => $item->id,
                'photo' => array_get($participantsKeyed, $item->user_id)->avatar('message'),
                'body' => $item->body_parsed,
                'time' => $item->created_at->format('c'),
                'author' => array_get($participantsKeyed, $item->user_id)->name,
                'link' => array_get($participantsKeyed, $item->user_id)->link(),
                'own' => $item->is_own
            ];
        });

        $thread->participants = $rThread->participants->map(function($item){
            return [
            'url' => $item->link(),
            'avatar' => $item->avatar('comments'),
            'name' => $item->name,
            'me' => ($item->id == auth()->user()->id)
            ];
        });

        $thread->messages_count = $messages->total();

        return $thread;
    }


    /**
     * Update response time field for all users, who have been online for last day
     */
    public function updateResponseTimes()
    {
        $users = User::whereHas('threads', function($q){
            return $q->whereBetween('threads.updated_at', [Carbon::now()->subHours(24), Carbon::now()]);
        })->with('threads.latestFiveMessage')->get();

        $users->map(function($user) {
            $user->response_time = $this->calculateAvgResponseTimeForUser($user);
            $user->save();
        });
    }


    /**
     * Calculate Average response time for user threads
     */
    public function calculateAvgResponseTimeForUser($user)
    {
        $times = $user->threads->map(function($thread) use ($user) {

            $messages = $thread->latestFiveMessage;

            $messages = $this->groupMessages($messages);

            $times = [];

            $messages = array_values(array_filter($messages->toArray()));

            foreach ($messages as $key => $message) {

                if($message['user_id'] !== $user->id) continue;

                if(isset($messages[$key + 1])){
                    $times[] = strtotime($message['updated_at']) - strtotime($messages[$key + 1]['created_at']);
                }
            }

            return $times;
        });

        $times = array_flatten($times->toArray());

        return array_sum($times) / count($times);
    }


    /**
     * Group messages by owner
     */
    public function groupMessages($messages)
    {
        foreach ($messages as $key => $message) {

            if(isset($messages[$key + 1]) && $messages[$key + 1]->user_id == $message->user_id){

                $diff = strtotime($messages[$key + 1]->updated_at) - strtotime($message->updated_at);

                if($diff > (60 * 60 * 6)){
                    continue;
                }

                $messages[$key + 1]->body = $messages[$key + 1]->body."\n".$message->body;
                $messages[$key + 1]->updated_at = $message->updated_at;
                unset($messages[$key]);
            }

        }

        return $messages->values();
    }


    /**
     * Find users by Id and exchange message between them
     */
    public function findOrCreateThreadBetween($sender, $messagable)
    {
        $thread = $sender->threads()->whereHas('participants', function ($query) use($messagable) {
            $query->where('thread_participants.user_id', $messagable->id);
        })->where('is_private', true)->first();

        if($thread && $thread->participants()->count() > 2) {
            $thread->delete();
            $thread = null;
        }

        if(!$thread){

            $thread = $sender->threads()->create([
                'is_private' => true
            ]);

            $thread->addParticipant($sender);

            $thread->addParticipant($messagable);
        }

        return $thread;
    }


    /**
     * Find users by Id and exchange message between them
     */
    public function sendMessageById($sender, $messagable, $message)
    {
        $users = User::whereIn('id', [$messagable, $sender])->get();

        foreach ($users as $user) {

            if(is_int($sender) && $user->id == $sender){
                $sender = $user;
            }

            if(is_int($messagable) && $user->id == $messagable){
                $messagable = $user;
            }
        }

        $thread = $this->findOrCreateThreadBetween($sender, $messagable);

        return $this->sendMessageToThread($thread, $sender, $message);
    }


    /**
     * Find users by Id and exchange message between them
     */
    public function findThreadByIdAndSendMessage($id, $message, $user = null)
    {
        $user = $user ?: auth()->user();

        $thread = $user->threads()->findOrFail($id);

        return $this->sendMessageToThread($thread, $user, $message);
    }


    /**
     * Find users by Id and exchange message between them
     */
    public function sendMessageToThread($thread, $sender, $message)
    {
        Participant::where('user_id', $sender->id)->where('thread_id', $thread->id)->update([
            'last_read' => (new \Carbon\Carbon)
        ]);

        $message = Message::create([
            'user_id' => $sender->id,
            'thread_id' => $thread->id,
            'body' => $message
        ]);
        
        $thread->participants()->where('users.id', '<>', $sender->id)->get()->map(function($user){
            $this->refreshNotificationsCount($user);
        });

        return $message;
    }


    /**
     * Find users by Id and exchange message between them
     */
    public function refreshNotificationsCount($user = null)
    {
        $user = is_null($user) ? auth()->user() : $user;

        $count = $user->threads()->get()->filter(function($thread) {

            if(strtotime($thread->pivot->last_read) < strtotime($thread->updated_at)) {
                return true;
            }

            return false;

        })->count();

        $user->notifications_count = $count;

        $user->save();
    }

}