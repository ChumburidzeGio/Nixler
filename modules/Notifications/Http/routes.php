<?php

Route::group(['middleware' => 'web', 'prefix' => 'notifications', 'namespace' => 'Modules\Notifications\Http\Controllers'], function()
{
    Route::get('/', 'NotificationsController@index');
});
