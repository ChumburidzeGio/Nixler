<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Services\LocationService;
use App\Services\PhoneService;
use App\Services\UserAgentService;
use App\Repositories\UserRepository;
use App\Entities\Order;
use stdClass, Lava, DB;
use Carbon\Carbon;

class SettingsController extends Controller
{

    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repository)
    {
        parent::__construct();
        $this->middleware('auth', ['except' => 'updateLocale']);
        $this->repository = $repository;
    }


    public function index()
    {
        return redirect('settings/account', 301);
    }

    public function editAccount()
    {
        $user = auth()->user();

        $cities = $user->country()->with('cities.translations')->first()->cities;

        return view('users.settings.account', compact('user', 'cities'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
              'username' => ['required', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
              'name' => 'required|string|max:255',
              'headline' => 'sometimes|max:255',
              'website' => 'nullable|url',
              'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
              'phone' => ['phone:'.$user->country, 'phone_unique:'.$user->country],
              'city_id' => 'required',
              'pcode' => 'numeric|digits:6',
        ]);

        $this->repository->update($request->all());

        return redirect('settings/account')->with('status', 
                        trans('users.settings.account.updated_status'));
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
              'new_password' => 'required|min:6|confirmed',
              'current_password' => [($user->password ? 'required' : 'sometimes'), 'ownpass']
        ]);

        $this->repository->setPassword($request->input('new_password'));

        return redirect()->back()->with('status', trans('users.settings.password.updated_status'));
    }
    
    public function updateLocale(Request $request)
    {
        $locale = $request->input('locale');
        
        (new LocationService)->updateLocaleByKey($locale);
        
        return redirect()->back();
    }

    /**
     * Show user sessions
     *
     * @return Response
     */
    public function sessions(Request $request)
    {
        $sessions = DB::table('sessions')->where('user_id', auth()->id())->whereNotNull('user_id')->latest('last_activity')->get();
        
        $sessions = $sessions->map(function($session) {

            $carbon = Carbon::createFromTimestamp($session->last_activity);

            $geoip = geoip($session->ip_address);

            return [
                'id' => $session->id,
                'location' => array_get($geoip, 'country'). ', ' .array_get($geoip, 'city'),
                'user_agent' => app(UserAgentService::class)->forHumans($session->user_agent),
                'ip_address' => $session->ip_address,
                'time' => $carbon->diffForHumans(),
                'is_current' => (session()->getId() == $session->id)
            ];
        });
        
        return view('users.settings.sessions', compact('sessions'));
    }

}