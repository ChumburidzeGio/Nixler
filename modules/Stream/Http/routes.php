<?php

Route::group(['middleware' => ['web'], 'namespace' => 'Modules\Stream\Http\Controllers'], function()
{
    Route::match(['get', 'post'], '/', 'StreamController@index')->name('feed');
    Route::match(['get'], '/discover', 'StreamController@discover')->name('discover')->middleware('auth');
});