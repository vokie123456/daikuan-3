<?php

/*
|--------------------------------------------------------------------------
| 后台管理路由
|--------------------------------------------------------------------------
|
| All route names are prefixed with 'admin.'.
|
*/

Route::view('/', 'admin')->name('home');
Route::view('home', 'admin')->name('home');

Route::post('getadmininfo', 'AdminController@getInfo');

// Route::redirect('/', '/admin/a', 301);
// Route::get('home', 'HomeController@index')->name('home');
