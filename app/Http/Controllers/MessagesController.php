<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Notifications\MessageRecieved;
use App\Repositories\MessengerRepository;
use App\Entities\User;
use stdClass;

class MessagesController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(MessengerRepository $repository){
        parent::__construct();
        $this->repository = $repository;
    }


    /**
     * @return Response
     */
    public function index()
    {
        $threads = $this->repository->getAllThreads();

        return view('messages.index', compact('threads'));
    }

    /**
     * @param  Request $request
     * @param  int $id
     * @return array
     */
    public function store($id, Request $request)
    {
        $message = $this->repository->findThreadByIdAndSendMessage($id, $request->input('message'));

        return [
            'id' => $message->id,
            'photo' => auth()->user()->avatar('message'),
            'body' => $message->body_parsed,
            'author' => auth()->user()->name,
            'link' => auth()->user()->link(),
            'time' => $message->created_at->format('c'),
            'own' => true
        ];
    }

    /**
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $thread = $this->repository->findThreadById($id);

        return view('messages.show', compact('thread'));
    }


    /**
     * @return Response
     */
    public function load($id, Request $request)
    {
        $thread = auth()->user()->threads()->findOrFail($id);

        $pcps = $thread->participants->keyBy('id');

        $last_id = $request->input('id');
        $dir = $request->input('dir');

        $rMessages = $thread->messages()->where('id', ($dir =='-1'?'<':'>'), $last_id)->take(15)->latest()->get();

        $messages = $rMessages->map(function($item) use ($pcps) {
            return [
                'id' => $item->id,
                'photo' => array_get($pcps, $item->user_id)->avatar('message'),
                'body' => $item->body_parsed,
                'time' => $item->created_at->format('c'),
                'author' => array_get($pcps, $item->user_id)->name,
                'link' => array_get($pcps, $item->user_id)->link(),
                'own' => $item->is_own
            ];
        });

        return $messages;
    }

    /**
     * @return Response
     */
    public function redirectToConversation($id)
    {
        $target = User::findOrFail($id);

        $user = auth()->user();

        $thread = $this->repository->findOrCreateThreadBetween($user, $target);

        return redirect()->route('thread', ['id' => $thread->id]);
    }
}
