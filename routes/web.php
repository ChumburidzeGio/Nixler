<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@welcome');

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/@{id}', 'User\UserController@find');
Route::get('/product/{id}', 'Product\ProductController@find');


Route::get('/policy', function(){
	return view('policy.page');
});

Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');


Route::get('/management/dashboard', function(){
	return view('admin.dashboard');
});

Route::get('/management/users', function(){
	return view('admin.users');
});

Route::get('/foo', function(){
	$socialite = new \Overtrue\Socialite\SocialiteManager(config('services'));
	return $socialite->driver('facebook')->fields([
                'first_name', 'last_name', 'email', 'gender', 'birthday'
            ])->scopes(['email', 'user_birthday'])->redirect();
});

Route::get('/goo', function(){
	$socialite = new \Overtrue\Socialite\SocialiteManager(config('services'));
	$socialUser = Socialite::driver($provider)->fields([
                'first_name', 'last_name', 'email', 'gender', 'birthday'
            ])->user();

	dd($socialUser);
});