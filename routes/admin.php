<?php

/*
|--------------------------------------------------------------------------
| 后台管理路由
|--------------------------------------------------------------------------
|
| All route names are prefixed with 'admin.'.
|
*/


Route::get('login','Admin\Auth\LoginController@showLoginForm')
    ->middleware('guest:admin')
    ->name('admin.login.show');

Route::post('login','Admin\Auth\LoginController@postLogin')
    ->middleware('guest:admin')
    ->name('admin.login.post');

Route::post('logout','Admin\Auth\LoginController@postLogout')
    ->middleware('auth:admin')
    ->name('admin.logout');

Route::get('home','Admin\HomeController@index')->name('admin.home');

Route::group([
    // 'namespace' => 'Admin',
    // 'prefix' => 'admin',
    'middleware' => 'auth:admin'
], function () {
    Route::view('/', 'admin')->name('admin');

    Route::post('admin/info', 'Admin\AdminController@getInfo');
    Route::get('companies', 'CompanyController@index');
    Route::post('company/create', 'CompanyController@create');
    Route::post('company/update', 'CompanyController@update');
    Route::get('company/delete/{id}', 'CompanyController@delete');
    Route::get('getapps', 'AppController@index');
    Route::get('getapp/{id}', 'AppController@show')->where('id', '[0-9]+');
    Route::post('app/store', 'AppController@store');
    Route::post('app/update', 'AppController@update');
    Route::post('appstatus/update', 'AppController@updateStatus');
    Route::get('app/delete/{id}', 'AppController@destroy')->where('id', '[0-9]+');
    Route::get('getcategories', 'CategoryController@index');
    Route::get('getcategories/group', 'CategoryController@getAllToGroup');
    Route::post('category/create', 'CategoryController@store');
    Route::post('category/update', 'CategoryController@update');
    Route::get('getcategory/{id}', 'CategoryController@show')->where('id', '[0-9]+');
    Route::post('categorystatus/update', 'CategoryController@updateStatus');
    Route::get('category/delete/{id}', 'CategoryController@destroy')->where('id', '[0-9]+');
    Route::get('getcategoryapps/{id?}', 'CategoryAppController@index')->where('id', '[0-9]+');
    Route::post('setcategoryapps', 'CategoryAppController@migrate');
    Route::post('banner/create', 'BannerController@store');
    Route::post('banner/update', 'BannerController@update');
    Route::get('getbanners', 'BannerController@index');
    Route::get('getbanner/{id}', 'BannerController@show')->where('id', '[0-9]+');
    Route::post('bannerstatus/update', 'BannerController@updateStatus');
    Route::get('banner/delete/{id}', 'BannerController@destroy')->where('id', '[0-9]+');

    Route::view('{a?}/{b?}/{c?}/{d?}', 'admin');
});


// Route::redirect('/', '/admin/a', 301);
// Route::get('home', 'HomeController@index')->name('home');
