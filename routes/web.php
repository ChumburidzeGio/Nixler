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

Route::group(['prefix' => (new Nixler\People\Services\LocationService)->segment()], function() {
	Route::get('/', 'HomeController@welcome')->middleware('lastLoginTracker');
});

Auth::routes();

Route::get('/@{id}', 'User\UserController@find')->middleware('lastLoginTracker');
Route::get('/product/{id}', 'Product\ProductController@find')->middleware('lastLoginTracker');


Route::get('/policy', function(){

	$service = new Nixler\People\Services\LocationService;

	return [
		'get' => $service->get(),
		'getTLD' => $service->getTLD(),
	];

	return view('policy.page');
});

Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');


Route::get('/management/dashboard', function(){
	return view('admin.dashboard');
});

Route::get('/management/users', function(){
	return view('admin.users');
});

Route::get('/auth/{provider}', '\Nixler\People\Controllers\SocialAuthController@redirect');
Route::get('/auth/{provider}/callback', '\Nixler\People\Controllers\SocialAuthController@callback');

Route::get('/settings/account', '\Nixler\People\Controllers\SettingsController@editAccount')->middleware('lastLoginTracker');

Route::get('/settings/emails', '\Nixler\People\Controllers\SettingsController@editEmail')->middleware('lastLoginTracker');

Route::get('/settings/password', '\Nixler\People\Controllers\SettingsController@editPassword')->middleware('lastLoginTracker');

Route::get('/settings', '\Nixler\People\Controllers\SettingsController@general');
Route::post('/settings/account', '\Nixler\People\Controllers\SettingsController@updateAccount');

Route::get('/settings/emails/{id}/verify', '\Nixler\People\Controllers\SettingsController@verifyEmail');
Route::post('/settings/emails/{id}/code', '\Nixler\People\Controllers\SettingsController@codeEmail');
Route::get('/settings/emails/{id}/default', '\Nixler\People\Controllers\SettingsController@defaultEmail');
Route::get('/settings/emails/{id}/delete', '\Nixler\People\Controllers\SettingsController@deleteEmail');
Route::post('/settings/emails', '\Nixler\People\Controllers\SettingsController@createEmail');

Route::post('/settings/password', '\Nixler\People\Controllers\SettingsController@updatePassword');

Route::post('/settings/locale', '\Nixler\People\Controllers\SettingsController@updateLocale');



Route::get('/avatars/{id}/{place}', '\Nixler\People\Controllers\PhotosController@avatar');
Route::get('/media/{id}/{type}/{place}.jpg', '\Nixler\People\Controllers\PhotosController@photo');