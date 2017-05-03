<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\User\Entities\User;
use Modules\User\Repositories\UserRepository;

class UserController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(UserRepository $repository){
        $this->repository = $repository;
    }


    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function find($id, Request $req)
    {   
        $tab_whitelist = ['products', 'followers', 'followings', 'photos'];

        $tab = $req->has('tab') && in_array($req->input('tab'), $tab_whitelist) ? $req->input('tab') : 'profile';

        $data = $this->repository->find($id, $tab);

        return view('user::profile.'.$data['view'], $data);
    }




    /**
     * Follow user
     *
     * @return \Illuminate\Http\Response
     */
    public function follow($id, Request $request)
    {   
        return redirect($this->repository->follow($id)->link());
    }



    /**
     * Upload photo for user
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadPhoto($id, Request $request)
    {   
        $this->validate($request, [
              '_s' => 'required|image',
              '_t' => 'required|numeric',
        ]);

        $target = User::whereUsername($id)->firstOrFail();
        $user = auth()->user();
        
        if($user->id != $target->id){
            return redirect()->back();
        }

        $user->uploadPhoto($request->file('_s'), ($request->input('_t') == 1 ? 'avatar' : 'cover'));

        return redirect($user->link());
    }



    /**
     * Redirect to users avatar
     *
     * @return \Illuminate\Http\Response
     */
    public function avatar($uid, $place, Request $request)
    {
        $user = app()->make(config('auth.providers.users.model'))->find($uid);

        $media = $user->getMedia('avatar')->first();
        $id = $media ? $media->id : '-';
        $ts = isset($media->created_at) ? strtotime($media->created_at) : 0;

        return redirect('/media/'.$id.'/avatar/'.$place.'.jpg?='.$ts, 302);
    }



    /**
     * Deactivate user account
     */
    public function deactivate(Request $request)
    {
        $deactivate = $this->repository->deactivate();
        return compact('deactivate');
    }

}
