<?php

Route::group(['middleware' => 'web', 'prefix' => 'media', 'namespace' => 'Modules\Media\Http\Controllers'], function()
{
	Route::get('/{id}/{type}/{place}.jpg', 'MediaController@generate');
	Route::delete('/{id}', 'MediaController@destroy');
});
