<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'im', 'namespace' => 'Modules\Messages\Http\Controllers'], function()
{
    Route::get('/', 'MessagesController@index')->name('threads');
    Route::get('/{id}', 'MessagesController@show')->name('thread');
    Route::get('/{id}/load', 'MessagesController@load')->name('thread-load');
    Route::post('/{id}', 'MessagesController@store')->name('thread-new-message');
    Route::get('/with/{id}', 'MessagesController@with')->name('find-thread');
});
