<?php

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

Route::get('/', function () {
    // event(new \App\Events\PusherEvent([
    //     'name' => 'test',
    //     'message' => 'now time: ' . date('Y-m-d H:i:s'),
    // ]));    
    return view('welcome');
});


Route::get('/pusher', function () {
    return view('pusher');
});


Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('sendmail', 'HomeController@sendmail');
