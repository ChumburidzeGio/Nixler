<?php

Route::group(['middleware' => 'web', 'prefix' => 'search', 'namespace' => 'Modules\Search\Http\Controllers'], function()
{
    Route::get('/', 'SearchController@index');
});
