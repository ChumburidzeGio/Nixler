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

//Route::get('/', 'HomeController@welcome');

Route::get('/policy', function(){
	return view('policy.page');
});

Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');
