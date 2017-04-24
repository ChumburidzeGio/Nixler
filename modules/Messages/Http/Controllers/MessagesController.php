<?php

namespace Modules\Messages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Notifications\MessageRecieved;
use stdClass;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
//return $user->message('Be how are you?', $user2);
        $threads = auth()->user()->hermes()->take(10)->get()->map(function($item){
            $message = !is_null($item->latestMessage) ? $item->latestMessage->first() : null;
            $ps = $item->participants;

            return [
                'link' => route('thread', ['id' => $item->id]),
                'photo' => $ps->first() ? $ps->first()->avatar('comments') : null,
                'name' => $ps->pluck('name')->implode(', '),
                'message' => $message ? $message->body : '',
                'last_replied' =>$message ? !!($message->user_id == auth()->user()->id) : false,
                'unread' => $item->unread
            ];
        });

        //Message to thread
        //$shop->messageIn($threads->first()->id, 'Hey hey');
        //find thread by id
        //$thread = $user->thread($threads->first()->id);
        //thread messages
        //$messages = $thread->messages()->take(20)->get();

        return view('messages::index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('messages::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store($id, Request $request)
    {
        $new_msg = auth()->user()->messageIn($id, $request->input('message'));

        return [
            'id' => $new_msg->id,
            'photo' => auth()->user()->avatar('message'),
            'body' => $new_msg->body,
            'own' => true
        ];
        //$user->message('Hey how are you?', $user2);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $rThread = auth()->user()->thread($id);

        $thread = new stdClass;
        $thread->id = $rThread->id;
        $thread->title = $rThread->subject ? : $rThread->participants->filter(function($user){
            return ($user->id != auth()->user()->id);
        })->pluck('name')->implode(', ');

        $participantsKeyed = $rThread->participants->keyBy('id');

        $thread->messages = $rThread->messages->map(function($item) use ($participantsKeyed) {
            return [
                'id' => $item->id,
                'photo' => array_get($participantsKeyed, $item->user_id)->avatar('message'),
                'body' => $item->body,
                'time' => $item->created_at->format('c'),
                'author' => array_get($participantsKeyed, $item->user_id)->name,
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

        return view('messages::show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('messages::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function load($id, Request $request)
    {
        $thread = auth()->user()->threads()->findOrFail($id);

        $pcps = $thread->participants->keyBy('id');

        $last_id = $request->input('id');
        $dir = $request->input('dir');

        return $thread->messages()->where('id', ($dir =='-1'?'<':'>'), $last_id)->take(15)->latest()->get()->map(function($item) use ($pcps) {
            return [
                'id' => $item->id,
                'photo' => array_get($pcps, $item->user_id)->avatar('message'),
                'body' => $item->body,
                //'time' => $item->created_at->format('c'),
                'own' => $item->is_own
            ];
        });
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function with($id)
    {
        $target = \Modules\User\Entities\User::findOrFail($id);
        $thread = auth()->user()->findOrCreateThreadWith($target);
        return redirect()->route('thread', ['id' => $thread->id]);
    }
}
