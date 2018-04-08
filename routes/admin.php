<?php

/*
|--------------------------------------------------------------------------
| 后台管理路由
|--------------------------------------------------------------------------
|
| All route names are prefixed with 'admin.'.
|
*/


Route::post('getadmininfo', 'Admin\AdminController@getInfo');
Route::get('getappcompanies', 'CompanyController@index');
Route::post('addappcompany', 'CompanyController@create');
Route::post('updatecompany', 'CompanyController@update');
Route::post('delcompany', 'CompanyController@delete');
Route::post('appstore', 'AppController@store');

Route::view('{a?}/{b?}/{c?}', 'admin');

// Route::redirect('/', '/admin/a', 301);
// Route::get('home', 'HomeController@index')->name('home');
