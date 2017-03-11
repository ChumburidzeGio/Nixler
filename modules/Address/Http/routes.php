<?php

Route::group(['middleware' => 'web', 'prefix' => 'address', 'namespace' => 'Modules\Address\Http\Controllers'], function()
{
    Route::get('/', 'AddressController@index');
});
