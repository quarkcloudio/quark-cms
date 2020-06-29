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

Route::group(['prefix' => 'tools'], function ($router) {
    $router->get('captcha/getImage', 'CaptchaController@getImage')->name('tools/captcha/getImage');
    $router->get('qrcode/getQrcode', 'QrcodeController@getQrcode')->name('tools/qrcode/getQrcode');
    $router->any('sms/send', 'SmsController@send')->name('tools/sms/send');
    $router->get('picture/getPicture', 'PictureController@getPicture')->name('tools/picture/getPicture');
    $router->post('picture/upload', 'PictureController@upload')->name('tools/picture/upload');
    $router->post('picture/base64Upload', 'PictureController@base64Upload')->name('tools/picture/base64Upload');
    $router->get('picture/urlUpload', 'PictureController@urlUpload')->name('tools/picture/urlUpload');
    $router->get('picture/insert', 'PictureController@insert')->name('tools/picture/insert');
    $router->post('file/upload', 'FileController@upload')->name('tools/file/upload');
    $router->any('git/webhook', 'GitController@webhook')->name('tools/git/webhook');
});