<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'fabric'], function(){
    Route::get('get', 'App\Http\Controllers\FabricController@index');
    Route::post('store', 'App\Http\Controllers\FabricController@store');
    Route::get('getFabricAssetsImage', 'App\Http\Controllers\FabricController@getFabricPicturesById');
});

