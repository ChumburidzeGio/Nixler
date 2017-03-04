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
	Route::get('/', 'HomeController@welcome');
});

Auth::routes();

Route::get('/@{id}', 'User\UserController@find');
Route::get('/@{id}/products', 'User\UserController@products');
Route::get('/@{id}/followers', 'User\UserController@followers');
Route::get('/@{id}/followings', 'User\UserController@followings');
Route::get('/@{id}/photos', 'User\UserController@media');

Route::get('/@{uid}/{id}', 'Product\ProductController@find');
Route::get('/new-product', 'Product\ProductController@create')->middleware('auth');
Route::get('/products/{id}/edit', 'Product\ProductController@edit')->middleware('auth');
Route::post('/products/{id}/photos', 'Product\ProductController@uploadPhoto')->middleware('auth');
Route::post('/products/{id}/photos/{mid}', 'Product\ProductController@removePhoto')->middleware('auth');
Route::post('/products/{id}', 'Product\ProductController@update')->middleware('auth');
Route::delete('/products/{id}', 'Product\ProductController@delete')->middleware('auth');
Route::post('/products/{id}/status', 'Product\ProductController@changeStatus')->middleware('auth');
Route::post('/products/{id}/schedule', 'Product\ProductController@schedule')->middleware('auth');
Route::post('/products/{id}/like', 'Product\ProductController@like')->middleware('auth');


Route::get('/policy', function(){
	return view('policy.page');
});

Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');
