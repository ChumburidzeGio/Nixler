<?php

namespace Modules\User\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Modules\User\Repositories\PhoneRepository;

class PhonesController extends Controller
{

    /**
     * @var PhoneRepository
     */
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PhoneRepository $repository)
    {
        $this->middleware('auth');
        $this->repository = $repository;
    }


    /**
     * Show user's all phones
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $user = auth()->user();

        $phones = $user->phones()->get(); 

        return view('user::settings.phones', compact('phones'));
    }


    /**
     * Store in database new phone number
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $this->validate($request, [
            'phone' => ['required', 'phone:'.$request->user()->country, 'phone_unique:'.$request->user()->country]
        ]);

        $phone = $this->repository->create($request->input('phone'));

        if(!$phone){
            return redirect('settings/phones')->withErrors([
                'phone' => trans('user::settings.phones.created_error_status')
            ]);
        }

        return redirect('settings/phones')->with('status', 
                    trans('user::settings.phones.created_status'));
    }


    /**
     * Send verification code on phone number
     *
     * @return \Illuminate\Http\Response
     */
    public function verify($id, Request $request)
    {
        $user = auth()->user();
        $phone = $user->phones()->find($id);

        if($phone && !$phone->is_verified && $phone->verify()){
            return redirect('settings/phones')->with('status', 
                        trans('user::settings.phones.code_sent_status'));
        }

        return redirect('settings/phones')->with('status', trans('user::settings.phones.created_error_status'));
    }


    /**
     * Check if verification code is correct.
     *
     * @return \Illuminate\Http\Response
     */
    public function verificationCheck($id, Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
              $id.'code' => 'required|numeric|digits:6',
        ], [trans('user::settings.phones.wrong_code_status')]);

        $verified = $this->repository->verificationCheck($id, $request->input($id.'code'));

        return $verified
                ? redirect('settings/phones')->with('status', 
                    trans('user::settings.phones.verified_status'))
                : redirect('settings/phones')->with('status', 
                    trans('user::settings.phones.wrong_code_status'));
    }


    /**
     * Mark phone as default
     *
     * @return \Illuminate\Http\Response
     */
    public function makeDefault($id, Request $request)
    {
        $user = auth()->user();
        $phone = $user->phones()->find($id);

        if($phone->makeDefault()){
            return redirect('settings/phones')->with('status', 
                        trans('user::settings.phones.default_status'));
        } else {
            return redirect('settings/phones')->with('status', 
                        trans('user::settings.phones.nonverified_error_status'));
        }
    }


    /**
     * Delete phone number
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id, Request $request)
    {
        $user = auth()->user();

        $phone = $user->phones()->find($id);
        $phone->delete();

        return redirect('settings/phones')->with('status', 
                        trans('user::settings.phones.deleted_status'));
    }
}