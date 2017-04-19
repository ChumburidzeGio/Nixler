<?php

Route::group(['middleware' => 'web', 'prefix' => 'comments', 'namespace' => 'Modules\Comment\Http\Controllers'], function()
{
    Route::post('/', 'CommentController@store');
    Route::get('/', 'CommentController@index');
    Route::delete('/{id}', 'CommentController@destroy');
});
