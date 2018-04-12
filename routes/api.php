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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'Api\LoginController@login');
Route::get('moudle/home', 'Api\MoudleController@homeDatas');
Route::get('moudle/loan', 'Api\MoudleController@loanDatas');
Route::get('moudle/secloan', 'Api\MoudleController@secloanDatas');
Route::get('category/apps/{id}', 'Api\AppListController@getDatas')->where('id', '[0-9]+');

Route::group(['middleware' => 'auth:api'], function(){
});
