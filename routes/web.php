<?php

use \NlpTools\Tokenizers\WhitespaceTokenizer;
use \NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;
use \NlpTools\Tokenizers\ClassifierBasedTokenizer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['prefix' => (new Modules\Address\Services\LocationService)->segment()], function() {
	Route::get('/', 'HomeController@welcome');
});


Route::get('/policy', function(){
	return view('policy.page');
});

Route::post('/marketing/subscribe', 'Marketing\NewsletterController@subscribe');

Route::get('qw', function(){

});