<?php

Route::group(['middleware' => 'web', 'namespace' => 'Modules\Product\Http\Controllers'], function()
{
	Route::get('/@{uid}/{id}', 'ProductController@find')->name('product');
	Route::get('/new-product', 'ProductController@create')->middleware('auth')->name('product:create');
	Route::get('/products/{id}/edit', 'ProductController@edit')->middleware('auth')->name('product:edit');
	Route::post('/products/{id}/photos', 'ProductController@uploadPhoto')->middleware('auth')->name('product:photos:post');
	Route::post('/products/{id}/photos/{mid}', 'ProductController@removePhoto')->middleware('auth')->name('product:photos:remove');
	Route::post('/products/{id}', 'ProductController@update')->middleware('auth')->name('product:update');
	Route::delete('/products/{id}', 'ProductController@delete')->middleware('auth')->name('product:delete');
	Route::post('/products/{id}/status', 'ProductController@changeStatus')->middleware('auth')->name('product:update:status');
	Route::post('/products/{id}/schedule', 'ProductController@schedule')->middleware('auth')->name('product:schedule');
	Route::post('/products/{id}/like', 'ProductController@like')->middleware('auth')->name('product:like');
});
