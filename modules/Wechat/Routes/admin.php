<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/wechat', function (Request $request) {
//     return $request->user();
// });

// 后台路由组
Route::group([
    'prefix' => 'api',
    'middleware' => 'admin',
], function ($router) {
    $router->get('admin/wechatMenu/index', 'Admin\\WechatMenuController@index')->name('api/admin/wechatMenu/index');
});