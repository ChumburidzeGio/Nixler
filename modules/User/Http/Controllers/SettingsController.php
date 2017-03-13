<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\User\Events\UsernameChanged;
use Illuminate\Validation\Rule;
use Modules\Address\Services\LocationService;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\ShippingPrice;
use stdClass;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'updateLocale']);
    }


    public function general()
    {
        return redirect('settings/account', 301);
    }

    public function editAccount()
    {
        return view('user::settings.account', ['user' => auth()->user()]);
    }

    public function updateAccount(Request $request)
    {
    	$user = auth()->user();

        $this->validate($request, [
              'username' => ['required', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
              'name' => 'required|string|max:255',
              'headline' => 'sometimes|max:255'
        ]);

        $oldUsername = $user->username;
    	$user->username = $request->input('username');
    	$user->name = $request->input('name');
    	$user->setMeta('headline', $request->input('headline'));
    	$user->save();
        
        if($oldUsername != $user->username){
            event(new UsernameChanged($user, $oldUsername));
		}

        return redirect('settings/account')->with('status', 
                        trans('user::settings.account.updated_status'));
    }

    public function editPassword()
    {
        return view('user::settings.password');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
              'new_password' => 'required|min:6|confirmed',
              'current_password' => [($user->password ? 'required' : 'sometimes'), 'ownpass']
        ]);

        $user->password = bcrypt($request->input('new_password'));
        $user->save();

        return redirect('settings/password')->with('status', 
                            trans('user::settings.password.updated_status'));
    }

    public function editEmail()
    {   
        $user = auth()->user();
        $emails = $user->emails()->orderBy('is_default', 'desc')->get();

        if(!count($emails) && $user->email){

            $user->emails()->create([
                'address' => $user->email
            ]);

            $emails = $user->emails()->get();
        }

        return view('user::settings.emails', compact('emails'));
    }

    public function createEmail(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
              'email' => ['required', 'email', Rule::unique('users'), Rule::unique('user_emails', 'address')]
        ]);

        $email = $user->emails()->create([
            'address' => $request->input('email')
        ]);

        if(!$email->verify()){
            $email->delete();
            return redirect('settings/emails')->withErrors([
                'email' => trans('user::settings.emails.created_error_status')
            ]);
        }

        return redirect('settings/emails')->with('status', 
                    trans('user::settings.emails.created_status'));
        
    }

    public function verifyEmail($id, Request $request)
    {
        $user = auth()->user();
        $email = $user->emails()->find($id);

        if($email && !$email->is_verified && $email->verify()){
            return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.code_sent_status'));
        }

        return redirect('settings/emails')->with('status', trans('user::settings.emails.created_error_status'));
    }

    public function codeEmail($id, Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
              $id.'code' => 'required|numeric|digits:6',
        ], [trans('user::settings.emails.wrong_code_status')]);

        $email = $user->emails()->find($id);

        if($email->makeVerified($request->input($id.'code'))){
            return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.verified_status'));
        } else {
            return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.wrong_code_status'));
        }

    }

    public function deleteEmail($id, Request $request)
    {
        $user = auth()->user();

        if($user->emails()->count() < 2){
            return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.last_email_status'));
        }

        $email = $user->emails()->find($id);
        $email->delete();

        return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.deleted_status'));
    }

    public function defaultEmail($id, Request $request)
    {
        $user = auth()->user();
        $email = $user->emails()->find($id);
        if($email->makeDefault()){
            return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.default_status'));
        } else {
            return redirect('settings/emails')->with('status', 
                        trans('user::settings.emails.nonverified_error_status'));
        }
        
    }

    public function updateLocale(Request $request)
    {
        $locale = $request->input('locale');
        
        (new LocationService)->updateLocaleByKey($locale);
        
        return redirect()->back();
    }

    public function editSocial(Request $request)
    {
        $user = auth()->user()->where('id', auth()->user()->id)->with('meta')->first();

        $links = new stdClass();
        $links->facebook = $user->getMeta('facebook');
        $links->twitter = $user->getMeta('twitter');
        $links->linkedin = $user->getMeta('linkedin');
        $links->vk = $user->getMeta('vk');
        $links->blog = $user->getMeta('blog');
        $links->website = $user->getMeta('website');

        return view('user::settings.social', compact('links'));
    }

    public function updateSocial(Request $request)
    {
        $this->validate($request, [
              'facebook' => 'nullable|url',
              'twitter' => 'nullable|url',
              'linkedin' => 'nullable|url',
              'vk' => 'nullable|url',
              'blog' => 'nullable|url',
              'website' => 'nullable|url',
        ]);
        
        $user = auth()->user();

        $user->setMeta('facebook', $request->input('facebook'));
        $user->setMeta('twitter', $request->input('twitter'));
        $user->setMeta('linkedin', $request->input('linkedin'));
        $user->setMeta('vk', $request->input('vk'));
        $user->setMeta('blog', $request->input('blog'));
        $user->setMeta('website', $request->input('website'));

        return redirect('settings/social')->with('status', 
                        trans('user::settings.social.updated_status'));
    }




    public function editShipping(Request $request)
    {
        $country = Country::where('iso_code', auth()->user()->country)->with('cities')->first();

        $prices = ShippingPrice::where('user_id', auth()->id())->where('type', 'city')->with('city')->get();

        return view('address::settings.shipping', compact('prices', 'country'));
    }



    public function saveShipping(Request $request)
    {
        $this->validate($request, [
              'location_id' => 'required',
              'price' => 'required|numeric|between:0,150000',
              'window_from' => 'required|numeric|between:0,99',
              'window_to' => 'required|numeric|min:'.$request->input('window_from').'|max:99',
        ]);

        $country = ShippingPrice::updateOrCreate([
            'user_id' => auth()->id(),
            'location_id' => $request->input('location_id'),
            'type' => 'city'
        ], [
            'price' => $request->input('price'),
            'window_from' => $request->input('window_from'),
            'window_to' => $request->input('window_to'),
        ]);

        return redirect()->route('shipping.settings');
    }
    
}