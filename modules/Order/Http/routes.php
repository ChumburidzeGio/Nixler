<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Order\Http\Controllers'], function()
{
    Route::get('/order', 'OrderController@create')->name('order');
    Route::post('/order', 'OrderController@store')->name('order.store');
    Route::get('/orders', 'OrderController@index')->name('orders');
    Route::get('/orders/{id}', 'OrderController@show')->name('order.show');
    Route::post('/orders/{id}/commit', 'OrderController@update')->name('order.commit');
});
