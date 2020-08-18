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

Route::group(['prefix' => 'wechat'], function ($router) {
    $router->any('login', 'Auth\\LoginController@login')->name('wechat/login');
    $router->get('logout', 'Auth\\LoginController@logout')->name('wechat/logout');
    $router->any('callback', 'Auth\\LoginController@callback')->name('wechat/callback');
    $router->get('register', 'Auth\\RegisterController@register')->name('wechat/register');
    $router->get('bindAccount', 'Auth\\RegisterController@bindAccount')->name('wechat/bindAccount');
    $router->any('token', 'ServerController@token')->name('wechat/server/token');
});

Route::group(['prefix' => 'wechat','middleware' => 'wechat'], function ($router) {

});

Route::group(['prefix' => 'wechat','middleware' => 'wechat.bindaccount'], function ($router) {

});
