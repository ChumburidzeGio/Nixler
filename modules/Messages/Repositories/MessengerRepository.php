<?php

namespace Modules\Messages\Repositories;

use App\Repositories\BaseRepository;
use Modules\User\Entities\User;
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
    public function findThreadById($id)
    {
        $user = auth()->user();

        $rThread = $user->thread($id);

        $thread = new stdClass;
        $thread->id = $rThread->id;
        $thread->title = $rThread->subject ? : $rThread->participants->filter(function($user){
            return ($user->id != auth()->user()->id);
        })->pluck('name')->implode(', ');

        $participantsKeyed = $rThread->participants->keyBy('id');

        $messages = $this->groupMessages($rThread->messages);

        $thread->messages = $messages->map(function($item) use ($participantsKeyed) {
            return [
                'id' => $item->id,
                'photo' => array_get($participantsKeyed, $item->user_id)->avatar('message'),
                'body' => nl2br($item->body),
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

        $thread->messages_count = $rThread->messages()->count();

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

            $messages = $thread->latestFiveMessage->toArray();

            $messages = $this->groupMessages($messages);

            $times = [];

            $messages = array_values(array_filter($messages));

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

}