<?php

Route::group(['middleware' => 'web', 'prefix' => 'blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function()
{
    Route::get('/შექმნა', 'BlogController@index');
    Route::get('/a', 'BlogController@show');
});
