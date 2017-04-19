<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Address\Http\Controllers'], function()
{
	Route::group(['prefix' => 'settings'], function() {

		//User addresses
		Route::get('addresses', 'AddressController@index')->name('settings.addresses');
		Route::post('addresses', 'AddressController@store')->name('settings.addresses.store');
		Route::get('addresses/{id}', 'AddressController@edit')->name('settings.addresses.edit');
		Route::post('addresses/{id}', 'AddressController@update')->name('settings.addresses.update');
		Route::delete('addresses/{id}', 'AddressController@destroy')->name('settings.addresses.delete');

		//Shipping rules
		Route::get('shipping', 'ShippingController@index')->name('shipping.settings');
		Route::post('shipping/locations', 'ShippingController@store')->name('shipping.settings.locations.create');
		Route::post('shipping/locations/{id}', 'ShippingController@update')->name('shipping.settings.locations.update');
		Route::post('shipping/general', 'ShippingController@updateGeneral')->name('shipping.settings.general');

	});

});
