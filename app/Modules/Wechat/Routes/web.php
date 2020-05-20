<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['prefix' => 'wechat'], function ($router) {

    $router->any('wechat/server/token', 'ServerController@token')->name('wechat/server/token');

    Route::get('/', function () {
        dd('This is the Wechat module index page. Build something great!');
    });
});
