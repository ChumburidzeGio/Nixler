<?php

Route::group(['middleware' => 'web', 'namespace' => 'Modules\User\Http\Controllers'], function()
{
	Route::get('/@{id}', 'UserController@find')->name('user');
	Route::post('@{id}/follow', 'UserController@follow');
	Route::post('@{id}/photos', 'UserController@uploadPhoto');

	Route::get('/avatars/{id}/{place}', 'UserController@avatar')->name('avatar');

	Route::get('/auth/{provider}', 'SocialAuthController@redirect');
	Route::get('/auth/{provider}/callback', 'SocialAuthController@callback');

	Route::group(['prefix' => 'settings'], function() {

		Route::get('/', 'SettingsController@general');

		//Account
		Route::get('account', 'SettingsController@editAccount');
		Route::post('account', 'SettingsController@updateAccount');

		//Password
		Route::get('password', 'SettingsController@editPassword');
		Route::post('password', 'SettingsController@updatePassword');

		//Social
		Route::get('social', 'SettingsController@editSocial');
		Route::post('social', 'SettingsController@updateSocial');

		//Email
		Route::get('emails', 'SettingsController@editEmail');
		Route::get('emails/{id}/verify', 'SettingsController@verifyEmail');
		Route::post('emails/{id}/code', 'SettingsController@codeEmail');
		Route::get('emails/{id}/default', 'SettingsController@defaultEmail');
		Route::get('emails/{id}/delete', 'SettingsController@deleteEmail');
		Route::post('emails', 'SettingsController@createEmail');

		//Locale
		Route::post('locale', 'SettingsController@updateLocale');

	});

});
