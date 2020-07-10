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

// Home未登录路由组
$router->get('/', 'Home\\IndexController@index')->name('/');
$router->get('index', 'Home\\IndexController@index')->name('index');
$router->get('index/index', 'Home\\IndexController@index')->name('index/index');
$router->get('page/index', 'Home\\PageController@index')->name('page/index');
$router->get('article/index', 'Home\\ArticleController@index')->name('article/index');
$router->get('article/list', 'Home\\ArticleController@list')->name('article/list');
$router->get('article/detail', 'Home\\ArticleController@detail')->name('article/detail');
$router->get('video/index', 'Home\\VideoController@index')->name('video/index');
$router->get('video/list', 'Home\\VideoController@list')->name('video/list');
$router->get('video/detail', 'Home\\VideoController@detail')->name('video/detail');
$router->any('search/index', 'Home\\SearchController@index')->name('search/index');

// Home已登录路由组
Route::group(['middleware' => ['auth']],function ($router) {
    $router->get('user/index', 'Home\\UserController@index')->name('user/index');
});