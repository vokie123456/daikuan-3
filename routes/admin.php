<?php

/*
|--------------------------------------------------------------------------
| 后台管理路由
|--------------------------------------------------------------------------
|
| All route names are prefixed with 'admin.'.
|
*/


Route::post('getadmininfo', 'AdminController@getInfo');

Route::view('{a?}/{b?}', 'admin');

// Route::redirect('/', '/admin/a', 301);
// Route::get('home', 'HomeController@index')->name('home');
