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

// 后台路由组
Route::group([
    'prefix'        => config('quark.route.prefix'),
    'middleware'    => config('quark.route.middleware'),
], function ($router) {
    $router->get('admin/wechatMenu/index', 'Admin\\WechatMenuController@index')->name('api/admin/wechatMenu/index');
});

// 不需要登录认证的路由组
Route::group(['prefix' => 'wechat'], function ($router) {
    $router->any('login', 'Auth\\LoginController@login')->name('wechat/login');
    $router->get('logout', 'Auth\\LoginController@logout')->name('wechat/logout');
    $router->any('callback', 'Auth\\LoginController@callback')->name('wechat/callback');
    $router->get('register', 'Auth\\RegisterController@register')->name('wechat/register');
    $router->get('bindAccount', 'Auth\\RegisterController@bindAccount')->name('wechat/bindAccount');
    $router->any('server/token', 'ServerController@token')->name('wechat/server/token');
});

// 需要登录，但不需要绑定WEB账号的路由组
Route::group(['prefix' => 'wechat','middleware' => 'wechat'], function ($router) {

});

// 需要登录，但需要绑定WEB账号的路由组
Route::group(['prefix' => 'wechat','middleware' => 'wechat.bindaccount'], function ($router) {

});
