<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Stream\Http\Controllers'], function()
{
    Route::match(['get', 'post'], '/feed', 'StreamController@index')->name('feed');
    Route::match(['get'], '/discover', 'StreamController@discover')->name('discover');
});
