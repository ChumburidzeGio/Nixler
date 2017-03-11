<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Stream\Http\Controllers'], function()
{
    Route::get('/feed', 'StreamController@index')->name('feed');
});
