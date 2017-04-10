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

Auth::routes(); 

Route::group(['prefix' => (new Modules\Address\Services\LocationService)->segment()], function() {
	Route::get('/', 'HomeController@welcome');
});

Route::get('/policy', function(){
	return filter_var("AR3,373.31", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	return view('policy.page');
});

Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');
