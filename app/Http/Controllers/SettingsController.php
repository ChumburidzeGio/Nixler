<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Services\LocationService;
use App\Services\PhoneService;
use App\Repositories\UserRepository;
use App\Entities\Order;
use stdClass;

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

        return redirect('settings/password')->with('status', 
                            trans('users.settings.password.updated_status'));
    }
    
    public function updateLocale(Request $request)
    {
        $locale = $request->input('locale');
        
        (new LocationService)->updateLocaleByKey($locale);
        
        return redirect()->back();
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
       
        if($request->has('id')) {
            $order = Order::where([
                'id' => $request->input('id'),
                'user_id' => $user->id
            ])->orWhere([
                'id' => $request->input('id'),
                'merchant_id' => $user->id
            ])->with('product', 'product.owner')->firstOrFail();
        } else {
            $orders = Order::where('user_id', $user->id)->orWhere('merchant_id', $user->id)->with('product')->latest()->paginate();
        }
        
        return view('users.settings.orders', compact('user', 'order', 'orders'));
    }

}