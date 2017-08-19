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

//Products & Orders
Route::group([], function() {

	Route::match(['get', 'post'], '/', 'StreamController@index')->name('feed');

	Route::get('/@{uid}/{id}', 'ProductController@show')->name('product');

	//Product Create/Update
	Route::group(['middleware' => ['auth']], function() {

		Route::get('/new-product', 'ProductController@create')->name('product.create');

		Route::get('/products/{id}/edit', 'ProductController@edit')->name('product.edit');

		Route::post('/products/{id}/photos', 'ProductController@uploadPhoto')->name('product:photos:post');

		Route::post('/products/{id}/photos/{mid}', 'ProductController@removePhoto')->name('product:photos:remove');

		Route::post('/products/{id}', 'ProductController@update')->name('product:update');

		Route::post('/products/{id}/import', 'ProductController@import')->name('product:import');

		Route::delete('/products/{id}', 'ProductController@delete')->name('product:delete');

		Route::post('/products/{id}/status', 'ProductController@changeStatus')->name('product:update:status');

		Route::post('/products/{id}/schedule', 'ProductController@schedule')->name('product:schedule');

		Route::post('/products/{id}/like', 'ProductController@like')->name('product:like');

	});

	Route::get('products/{id}/order', 'OrderController@create')->middleware('auth')->name('order');

	Route::post('products/{id}/order', 'OrderController@store')->middleware('auth')->name('order.store');

	Route::post('/orders/{id}/commit', 'ProductController@commitOrder')->name('order.commit');

	Route::get('/orders', 'OrderController@index')->name('orders.index');

	Route::get('/orders/{id}', 'OrderController@show')->name('orders.show');

	Route::get('/stock', 'ProductController@stock')->name('stock');

	Route::get('/sitemap/products', 'ProductController@sitemap')->name('sitemap.products');

	Route::get('/p{id}', 'ProductController@shortlink')->name('products.shortlink');

});

//Collections
Route::group(['prefix' => 'cl'], function() {

	Route::get('/{id}', 'CollectionsController@show')->name('collections.show')->where('id', '[0-9]+');

	Route::get('/create', 'CollectionsController@update')->name('collections.create');

	Route::get('/update/{id}', 'CollectionsController@update')->name('collections.update');

	Route::post('/store', 'CollectionsController@store')->name('collections.store');

	Route::post('/delete', 'CollectionsController@delete')->name('collections.delete');

	Route::get('/', 'CollectionsController@index')->name('collections.index');

	Route::post('/productSearch', 'CollectionsController@productSearch')->name('collections.productSearch');

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
Route::group(['middleware' => ['auth'], 'prefix' => 'management'], function() {

	Route::get('users', 'ManagementController@users')->name('management.users');

	Route::get('products', 'ManagementController@products')->name('management.products');

	Route::get('orders', 'ManagementController@orders')->name('management.orders');

});

Route::get('/about', 'BlogController@welcome');

Route::get('/help', 'HelpController@index');

Route::get('/testing', function(){

	$user = factory(App\Entities\User::class)->create();

    $merchant = factory(App\Entities\User::class)->create();

    $products = factory(App\Entities\Product::class, 3)->create([
        'owner_id' => $merchant->id,
        'owner_username' => $merchant->username
    ]);

    $products->each(function($product) {

    	if(rand(0,1)) 
            {
                $variants = factory(App\Entities\ProductVariant::class, rand(1, 100))->make([
                    'product_id' => $product->id
                ]);

        return $product->setRelation('variants', $variants);
            }
    });

      return $products;
});
