<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'Api\LoginController@login');
Route::post('register/getcode', 'Api\RegisterController@sendCode');
Route::post('register', 'Api\RegisterController@register');
Route::post('findpassword/getcode', 'Api\RegisterController@sendFindPasswordCode');
Route::post('findpassword', 'Api\RegisterController@findPassword');
Route::get('moudle/home', 'Api\MoudleController@homeDatas');
Route::get('moudle/loan', 'Api\MoudleController@loanDatas');
Route::get('moudle/secloan', 'Api\MoudleController@secloanDatas');
Route::get('category/apps/{id}', 'Api\AppController@getDatas')->where('id', '[0-9]+');
Route::get('getapp/{id}', 'Api\AppController@getApp')->where('id', '[0-9]+');
Route::post('devices', 'Api\DeviceController@save');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('user/getinfo', 'Api\UserController@getInfo');
    Route::post('user/updatepassword', 'Api\UserController@reset_password');
    Route::post('user/updatemobile/getcode', 'Api\UserController@update_telephone_send_code');
    Route::post('user/updatemobile', 'Api\UserController@update_telephone');
    Route::post('user/update', 'Api\UserController@update');
    Route::post('app/togglecollection/{id}', 'Api\UserCollController@toggleCollection')->where('id', '[0-9]+');
    Route::get('getcollections', 'Api\UserCollController@getCollections');
    Route::get('getrecords', 'Api\UserRecController@getRecords');
});
