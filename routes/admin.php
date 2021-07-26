<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'admin',
    'namespace' => 'App\\Admin\\Controllers'
], function (Router $router) {
    $router->any('admin/sms/import', 'SmsController@import')->name('api/admin/sms/import');
    $router->post('admin/sms/sendImportSms', 'SmsController@sendImportSms')->name('api/admin/sms/sendImportSms');
});