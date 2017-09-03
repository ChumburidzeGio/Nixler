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

Route::impersonate();

//Articles
Route::group([], function() {

	Route::get('new-article', 'BlogController@create')->name('articles.create')->middleware('can:create-articles');

	Route::get('articles/{slug}', 'BlogController@show')->name('articles.show');
	
	Route::get('articles/{slug}/edit', 'BlogController@edit')->name('articles.edit')->middleware('can:create-articles');

	Route::post('articles/{slug}', 'BlogController@update')->name('articles.update')->middleware('can:create-articles');

	Route::delete('articles/{slug}', 'BlogController@destroy')->name('articles.destroy')->middleware('can:create-articles');

});

//Messages
Route::group(['middleware' => ['auth'], 'prefix' => 'im'], function() {

	Route::get('/{id?}', 'MessagesController@show')->name('threads');

	Route::get('/{id}/load', 'MessagesController@load')->name('thread-load');

	Route::post('/{id}', 'MessagesController@store')->name('thread-new-message');

	Route::get('/with/{id}', 'MessagesController@redirectToConversation')->name('find-thread');

});

//Products
Route::group([], function() {

	Route::match(['get', 'post'], '/', 'StreamController@index')->name('feed');

	Route::get('/@{uid}/{id}', 'Product\Show')->name('product');

	//Product Create/Update
	Route::group(['middleware' => ['auth']], function() {

		Route::get('/new-product', 'ProductController@create')->name('product.create');

		Route::get('/products/{id}/edit', 'Product\Edit')->name('product.edit');

		Route::post('/products/{id}', 'Product\Store')->name('product:update');

		Route::post('/products/{id}/photos', 'Product\EditMediaUpload')->name('product:photos:post');

		Route::post('/products/{id}/photos/{mid}', 'Product\EditMediaRemove')->name('product:photos:remove');

		Route::post('/products/{id}/import', 'ProductController@import')->name('product:import');

		Route::delete('/products/{id}', 'Product\Delete')->name('product:delete');

		Route::post('/products/{id}/status', 'ProductController@changeStatus')->name('product:update:status');

		Route::post('/products/{id}/schedule', 'ProductController@schedule')->name('product:schedule');

		Route::post('/products/{id}/like', 'Product\Like')->name('product:like');

	});

	Route::get('products/{id}/order', 'OrderController@create')->middleware('auth')->name('order');

	Route::post('products/{id}/order', 'Order\StoreOrder')->middleware('auth')->name('order.store');

	Route::get('/stock', 'ProductController@stock')->name('stock');

	Route::get('/sitemap/products', 'Product\Sitemap')->name('sitemap.products');

	Route::get('/p{id}', 'ProductController@shortlink')->name('products.shortlink');

});

//Orders
Route::group(['middleware' => ['auth'], 'prefix' => '/orders', 'as' => 'orders.'], function() {

	Route::get('/', 'OrderController@index')->name('index');

	Route::get('/payments/cartubank/callback', 'Order\Payment\CartuCallback')->name('payments.cartu.callback');

	Route::get('/{id}', 'OrderController@show')->name('show');

	Route::post('/{id}/commit', 'ProductController@commitOrder')->name('commit');

});

//Collections
Route::group(['prefix' => 'cl'], function() {

	Route::get('/{id}', 'Collection\Show')->name('collections.show')->where('id', '[0-9]+');

	Route::get('/create', 'Collection\Edit')->name('collections.create');

	Route::get('/update/{id}', 'Collection\Edit')->name('collections.update');

	Route::post('/store', 'Collection\Store')->name('collections.store');

	Route::post('/delete', 'Collection\Delete')->name('collections.delete');

	Route::get('/', 'Collection\Index')->name('collections.index');

	Route::post('/productSearch', 'Collection\EditProductSearch')->name('collections.productSearch');

});

//Comments
Route::group(['prefix' => 'comments'], function() {

	Route::post('/', 'CommentController@store');

	Route::get('/', 'CommentController@index');

	Route::delete('/{id}', 'CommentController@destroy');

});

//Media
Route::group(['prefix' => 'media'], function() {

	Route::get('/{id}/{type}/{place}.jpg', 'MediaController@generate')->name('photo');

	Route::delete('/{id}', 'MediaController@destroy');

});

//User & Auth & Settings
Route::group([], function() {

	Route::match(['get', 'post'], '/@{id}', 'UserController@find')->name('user');

	Route::post('@{id}/follow', 'UserController@follow')->name('user.follow');

	Route::post('@{id}/photos', 'UserController@uploadPhoto')->name('user.uploadPhoto');

	Route::get('/avatars/{id}/{place}', 'UserController@avatar')->name('avatar');

	Route::get('/auth/{provider}', 'SocialAuthController@redirect');

	Route::get('/auth/{provider}/callback', 'SocialAuthController@callback');

	Route::group(['prefix' => 'settings'], function() {

		Route::get('/', 'SettingsController@index');

		Route::get('account', 'SettingsController@editAccount');

		Route::post('account', 'SettingsController@updateAccount');

		Route::post('account/deactivate', 'UserController@deactivate');

		Route::post('password', 'SettingsController@updatePassword');

		Route::get('analytics', 'SettingsController@analytics')->name('settings.analytics');

		Route::get('sessions', 'SettingsController@sessions')->name('settings.sessions');

		Route::post('locale', 'SettingsController@updateLocale');

		Route::group(['middleware' => ['auth'], 'prefix' => 'shipping'], function() {

			Route::get('/', 'ShippingController@index')->name('shipping.settings');

			Route::post('/locations', 'ShippingController@store')->name('shipping.settings.locations.create');

			Route::post('/locations/{id}', 'ShippingController@update')->name('shipping.settings.locations.update');

			Route::post('/general', 'ShippingController@updateGeneral')->name('shipping.settings.general');

			Route::post('/payment', 'ShippingController@updatePayment')->name('shipping.settings.payment');

		});

	});

	Route::get('/sitemap/users', 'UserController@sitemap')->name('sitemap.users');

});

//Management
Route::group(['middleware' => ['auth'], 'prefix' => 'management', 'as' => 'management.'], function() {

	Route::get('users', 'ManagementController@users')->name('users');

	Route::get('products', 'ManagementController@products')->name('products');

	Route::get('orders', 'ManagementController@orders')->name('orders');

	Route::get('articles', 'ManagementController@articles')->name('articles');

	Route::get('calculators', 'ManagementController@calculators')->name('calculators');

});

//Pages
Route::group(['as' => 'pages.'], function() {

	Route::get('/about', 'Pages\About')->name('about');

	Route::get('/help', 'Pages\Help')->name('help');

	Route::get('/sell', 'Pages\Sell')->name('sell');

});

Route::get('/sd/{id}', 'Order\Payment\CartuRedirect');