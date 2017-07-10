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

//Route::demoAccess('/access');

Auth::routes(); 

//Route::group(['middleware' => 'demoMode'], function () {

	Route::impersonate();

	Route::get('new-article', 'BlogController@create')->name('articles.create')->middleware('can:create-articles');
	Route::get('articles/{slug}', 'BlogController@show')->name('articles.show');
	Route::get('articles/{slug}/edit', 'BlogController@edit')->name('articles.edit')->middleware('can:create-articles');
	Route::post('articles/{slug}', 'BlogController@update')->name('articles.update')->middleware('can:create-articles');
	Route::delete('articles/{slug}', 'BlogController@destroy')->name('articles.destroy')->middleware('can:create-articles');

	Route::group([], function() {
	    Route::get('/cl{id}', 'CollectionController@find')->name('collection.find');
	});

	Route::group(['middleware' => ['auth'], 'prefix' => 'im'], function() {
	    Route::get('/{id?}', 'MessagesController@show')->name('threads');
	    Route::get('/{id}/load', 'MessagesController@load')->name('thread-load');
	    Route::post('/{id}', 'MessagesController@store')->name('thread-new-message');
	    Route::get('/with/{id}', 'MessagesController@redirectToConversation')->name('find-thread');
	});

	Route::group([], function() {
		Route::get('/@{uid}/{id}', 'ProductController@find')->name('product');
		Route::get('/new-product', 'ProductController@create')->middleware('auth')->name('product.create');
		Route::get('/products/{id}/edit', 'ProductController@edit')->middleware('auth')->name('product.edit');
		Route::post('/products/{id}/photos', 'ProductController@uploadPhoto')->middleware('auth')->name('product:photos:post');
		Route::post('/products/{id}/photos/{mid}', 'ProductController@removePhoto')->middleware('auth')->name('product:photos:remove');
		Route::post('/products/{id}', 'ProductController@update')->middleware('auth')->name('product:update');
		Route::post('/products/{id}/import', 'ProductController@import')->middleware('auth')->name('product:import');
		Route::delete('/products/{id}', 'ProductController@delete')->middleware('auth')->name('product:delete');
		Route::post('/products/{id}/status', 'ProductController@changeStatus')->middleware('auth')->name('product:update:status');
		Route::post('/products/{id}/schedule', 'ProductController@schedule')->middleware('auth')->name('product:schedule');
		Route::post('/products/{id}/like', 'ProductController@like')->middleware('auth')->name('product:like');
		Route::match(['get', 'post'], 'products/{id}/order', 'ProductController@order')->middleware('auth')->name('order');
		Route::post('/orders/{id}/commit', 'ProductController@commitOrder')->name('order.commit');
		Route::get('/stock', 'ProductController@stock')->name('stock');
	});

	Route::group(['prefix' => 'comments'], function() {
	    Route::post('/', 'CommentController@store');
	    Route::get('/', 'CommentController@index');
	    Route::delete('/{id}', 'CommentController@destroy');
	});

	Route::group(['prefix' => 'media'], function() {
		Route::get('/{id}/{type}/{place}.jpg', 'MediaController@generate')->name('photo');
		Route::delete('/{id}', 'MediaController@destroy');
	});

	Route::group([], function() {
	    Route::match(['get', 'post'], '/', 'StreamController@index')->name('feed');
	});

	Route::group([], function() {
		
		Route::match(['get', 'post'], '/@{id}', 'UserController@find')->name('user');
		Route::post('@{id}/follow', 'UserController@follow')->name('user.follow');
		Route::post('@{id}/photos', 'UserController@uploadPhoto')->name('user.uploadPhoto');

		Route::get('/avatars/{id}/{place}', 'UserController@avatar')->name('avatar');

		Route::get('/auth/{provider}', 'SocialAuthController@redirect');
		Route::get('/auth/{provider}/callback', 'SocialAuthController@callback');

		Route::group(['prefix' => 'settings'], function() {

			Route::get('/', 'SettingsController@index');

			//Account
			Route::get('account', 'SettingsController@editAccount');
			Route::post('account', 'SettingsController@updateAccount');
			Route::post('account/deactivate', 'UserController@deactivate');
			Route::post('password', 'SettingsController@updatePassword');

			Route::get('orders', 'SettingsController@orders')->name('settings.orders');
			Route::get('analytics', 'SettingsController@analytics')->name('settings.analytics');
			Route::get('sessions', 'SettingsController@sessions')->name('settings.sessions');

			//Locale
			Route::post('locale', 'SettingsController@updateLocale');

		});

	});

	Route::group(['middleware' => ['auth'], 'prefix' => 'settings'], function()
	{
		//Shipping rules
		Route::get('shipping', 'ShippingController@index')->name('shipping.settings');
		Route::post('shipping/locations', 'ShippingController@store')->name('shipping.settings.locations.create');
		Route::post('shipping/locations/{id}', 'ShippingController@update')->name('shipping.settings.locations.update');
		Route::post('shipping/general', 'ShippingController@updateGeneral')->name('shipping.settings.general');
		Route::post('shipping/payment', 'ShippingController@updatePayment')->name('shipping.settings.payment');
	});

	Route::get('/about', 'BlogController@welcome');
	Route::get('/help', 'HelpController@index');
	//Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');

	Route::get('/monitor', function(){

		$monitors = app(\App\Monitors\MonitorFactory::class)->get();

		return $monitors['fields'];

	});

	Route::get('/scrap', function(){
		return app(\App\Crawler\Crawler::class)->all(request('url'));
	});

//});
