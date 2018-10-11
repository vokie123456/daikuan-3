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
    
    return redirect()->route('admin');

    // event(new \App\Events\PusherEvent([
    //     'name' => 'test',
    //     'message' => 'now time: ' . date('Y-m-d H:i:s'),
    // ]));

    return view('welcome');
});

// Route::get('/register', function () {
//     // 该路由优先级低注册操作器的操作, 需要重定向请直接在注册控制器里修改
//     // 路径: app/Http/Controllers/Auth/RegisterController.php
// });

// 重定位之前的链接
Route::get('/register/index.html', function () {
    return redirect('register');
});

Route::get('/register/download', function () {
    return redirect('/register' . config('app.register_theme') . '/download.php');
});


Route::get('/pusher', function () {
    return view('pusher');
});


Route::get('/agents/login/{id?}', 'Agent\LoginController@index')->name('showAgentLogin');
Route::post('/agents/login', 'Agent\LoginController@form')->name('agentLogin');
Route::get('/agentlogout', 'Agent\LoginController@logout');
Route::get('/agents/myurl', 'Agent\AgentController@myurl');
Route::get('/agents/teams', 'Agent\AgentController@teams');
Route::post('/agents/teamdata', 'Agent\AgentController@teamdata');
Route::get('/agents/create', 'Agent\AgentController@create');
Route::post('/agents/create', 'Agent\AgentController@createForm')->name('createAgentForm');
Route::get('/agents/promotes', 'Agent\AgentController@promotes');
Route::post('/agents/promotedata', 'Agent\AgentController@getPromoteDatas');


Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('sendmail', 'HomeController@sendmail');
