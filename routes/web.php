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