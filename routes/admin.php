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
Route::get('getappcompanies', 'CompanyController@getAppCompanies');
Route::post('addappcompany', 'CompanyController@addAppCompany');
Route::post('updatecompany', 'CompanyController@updateCompany');

Route::view('{a?}/{b?}', 'admin');

// Route::redirect('/', '/admin/a', 301);
// Route::get('home', 'HomeController@index')->name('home');
