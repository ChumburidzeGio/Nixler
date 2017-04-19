<?php

Route::group(['middleware' => 'web', 'prefix' => 'media', 'namespace' => 'Modules\Media\Http\Controllers'], function()
{
	Route::get('/{id}/{type}/{place}.jpg', 'MediaController@generate')->name('photo');
	Route::delete('/{id}', 'MediaController@destroy');
});
