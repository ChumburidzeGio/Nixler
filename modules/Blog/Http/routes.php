<?php

Route::group(['middleware' => 'web', 'prefix' => 'articles', 'namespace' => 'Modules\Blog\Http\Controllers'], function()
{
    Route::get('/{slug}', 'BlogController@show');
});
