<?php

use Illuminate\Routing\Router;

Quark::routes();

Route::group([
    'prefix'        => config('quark.route.prefix'),
    'namespace'     => config('quark.route.namespace'),
    'middleware'    => config('quark.route.middleware'),
], function (Router $router) {

    $router->get('admin/dashboard/index', 'DashboardController@index')->name('api/admin/dashboard/index');

    $router->get('admin/upgrade/index', 'UpgradeController@index')->name('api/admin/upgrade/index');
    $router->get('admin/upgrade/download', 'UpgradeController@download')->name('api/admin/upgrade/download');
    $router->get('admin/upgrade/extract', 'UpgradeController@extract')->name('api/admin/upgrade/extract');
    $router->get('admin/upgrade/updateFile', 'UpgradeController@updateFile')->name('api/admin/upgrade/updateFile');
    $router->get('admin/upgrade/updateDatabase', 'UpgradeController@updateDatabase')->name('api/admin/upgrade/updateDatabase');
    $router->get('admin/upgrade/finish', 'UpgradeController@finish')->name('api/admin/upgrade/finish');

    $router->get('admin/article/index', 'ArticleController@index')->name('api/admin/article/index');
    $router->get('admin/article/show', 'ArticleController@show')->name('api/admin/article/show');
    $router->get('admin/article/create', 'ArticleController@create')->name('api/admin/article/create');
    $router->post('admin/article/store', 'ArticleController@store')->name('api/admin/article/store');
    $router->get('admin/article/edit', 'ArticleController@edit')->name('api/admin/article/edit');
    $router->post('admin/article/update', 'ArticleController@update')->name('api/admin/article/update');
    $router->any('admin/article/action', 'ArticleController@action')->name('api/admin/article/action');
    $router->post('admin/article/destroy', 'ArticleController@destroy')->name('api/admin/article/destroy');
    
    $router->get('admin/articleCategory/index', 'ArticleCategoryController@index')->name('api/admin/articleCategory/index');
    $router->get('admin/articleCategory/show', 'ArticleCategoryController@show')->name('api/admin/articleCategory/show');
    $router->get('admin/articleCategory/create', 'ArticleCategoryController@create')->name('api/admin/articleCategory/create');
    $router->post('admin/articleCategory/store', 'ArticleCategoryController@store')->name('api/admin/articleCategory/store');
    $router->get('admin/articleCategory/edit', 'ArticleCategoryController@edit')->name('api/admin/articleCategory/edit');
    $router->post('admin/articleCategory/update', 'ArticleCategoryController@update')->name('api/admin/articleCategory/update');
    $router->any('admin/articleCategory/action', 'ArticleCategoryController@action')->name('api/admin/articleCategory/action');
    $router->post('admin/articleCategory/destroy', 'ArticleCategoryController@destroy')->name('api/admin/articleCategory/destroy');

    $router->get('admin/banner/index', 'BannerController@index')->name('api/admin/banner/index');
    $router->get('admin/banner/show', 'BannerController@show')->name('api/admin/banner/show');
    $router->get('admin/banner/create', 'BannerController@create')->name('api/admin/banner/create');
    $router->post('admin/banner/store', 'BannerController@store')->name('api/admin/banner/store');
    $router->get('admin/banner/edit', 'BannerController@edit')->name('api/admin/banner/edit');
    $router->post('admin/banner/update', 'BannerController@update')->name('api/admin/banner/update');
    $router->any('admin/banner/action', 'BannerController@action')->name('api/admin/banner/action');
    $router->post('admin/banner/destroy', 'BannerController@destroy')->name('api/admin/banner/destroy');

    $router->get('admin/bannerCategory/index', 'BannerCategoryController@index')->name('api/admin/bannerCategory/index');
    $router->get('admin/bannerCategory/show', 'BannerCategoryController@show')->name('api/admin/bannerCategory/show');
    $router->get('admin/bannerCategory/create', 'BannerCategoryController@create')->name('api/admin/bannerCategory/create');
    $router->post('admin/bannerCategory/store', 'BannerCategoryController@store')->name('api/admin/bannerCategory/store');
    $router->get('admin/bannerCategory/edit', 'BannerCategoryController@edit')->name('api/admin/bannerCategory/edit');
    $router->post('admin/bannerCategory/update', 'BannerCategoryController@update')->name('api/admin/bannerCategory/update');
    $router->any('admin/bannerCategory/action', 'BannerCategoryController@action')->name('api/admin/bannerCategory/action');
    $router->post('admin/bannerCategory/destroy', 'BannerCategoryController@destroy')->name('api/admin/bannerCategory/destroy');

    $router->get('admin/video/index', 'VideoController@index')->name('api/admin/video/index');
    $router->get('admin/video/show', 'VideoController@show')->name('api/admin/video/show');
    $router->get('admin/video/create', 'VideoController@create')->name('api/admin/video/create');
    $router->post('admin/video/store', 'VideoController@store')->name('api/admin/video/store');
    $router->get('admin/video/edit', 'VideoController@edit')->name('api/admin/video/edit');
    $router->post('admin/video/update', 'VideoController@update')->name('api/admin/video/update');
    $router->any('admin/video/action', 'VideoController@action')->name('api/admin/video/action');
    $router->post('admin/video/destroy', 'VideoController@destroy')->name('api/admin/video/destroy');

    $router->get('admin/page/index', 'PageController@index')->name('api/admin/page/index');
    $router->get('admin/page/show', 'PageController@show')->name('api/admin/page/show');
    $router->get('admin/page/create', 'PageController@create')->name('api/admin/page/create');
    $router->post('admin/page/store', 'PageController@store')->name('api/admin/page/store');
    $router->get('admin/page/edit', 'PageController@edit')->name('api/admin/page/edit');
    $router->post('admin/page/update', 'PageController@update')->name('api/admin/page/update');
    $router->any('admin/page/action', 'PageController@action')->name('api/admin/page/action');
    $router->post('admin/page/destroy', 'PageController@destroy')->name('api/admin/page/destroy');
    
    $router->get('admin/user/index', 'UserController@index')->name('api/admin/user/index');
    $router->get('admin/user/show', 'UserController@show')->name('api/admin/user/show');
    $router->get('admin/user/create', 'UserController@create')->name('api/admin/user/create');
    $router->post('admin/user/store', 'UserController@store')->name('api/admin/user/store');
    $router->get('admin/user/edit', 'UserController@edit')->name('api/admin/user/edit');
    $router->post('admin/user/update', 'UserController@update')->name('api/admin/user/update');
    $router->any('admin/user/action', 'UserController@action')->name('api/admin/user/action');
    $router->post('admin/user/destroy', 'UserController@destroy')->name('api/admin/user/destroy');
    $router->any('admin/user/recharge', 'UserController@recharge')->name('api/admin/user/recharge');
    $router->any('admin/user/suggest', 'UserController@suggest')->name('api/admin/user/suggest');

    $router->get('admin/comment/index', 'CommentController@index')->name('api/admin/comment/index');
    $router->get('admin/comment/show', 'CommentController@show')->name('api/admin/comment/show');
    $router->get('admin/comment/create', 'CommentController@create')->name('api/admin/comment/create');
    $router->post('admin/comment/store', 'CommentController@store')->name('api/admin/comment/store');
    $router->get('admin/comment/edit', 'CommentController@edit')->name('api/admin/comment/edit');
    $router->post('admin/comment/update', 'CommentController@update')->name('api/admin/comment/update');
    $router->any('admin/comment/action', 'CommentController@action')->name('api/admin/comment/action');
    $router->post('admin/comment/destroy', 'CommentController@destroy')->name('api/admin/comment/destroy');
    
    $router->get('admin/link/index', 'LinkController@index')->name('api/admin/link/index');
    $router->get('admin/link/show', 'LinkController@show')->name('api/admin/link/show');
    $router->get('admin/link/create', 'LinkController@create')->name('api/admin/link/create');
    $router->post('admin/link/store', 'LinkController@store')->name('api/admin/link/store');
    $router->get('admin/link/edit', 'LinkController@edit')->name('api/admin/link/edit');
    $router->post('admin/link/update', 'LinkController@update')->name('api/admin/link/update');
    $router->any('admin/link/action', 'LinkController@action')->name('api/admin/link/action');
    $router->post('admin/link/destroy', 'LinkController@destroy')->name('api/admin/link/destroy');

    $router->get('admin/printer/index', 'PrinterController@index')->name('api/admin/printer/index');
    $router->get('admin/printer/show', 'PrinterController@show')->name('api/admin/printer/show');
    $router->get('admin/printer/create', 'PrinterController@create')->name('api/admin/printer/create');
    $router->post('admin/printer/store', 'PrinterController@store')->name('api/admin/printer/store');
    $router->get('admin/printer/edit', 'PrinterController@edit')->name('api/admin/printer/edit');
    $router->post('admin/printer/update', 'PrinterController@update')->name('api/admin/printer/update');
    $router->any('admin/printer/action', 'PrinterController@action')->name('api/admin/printer/action');
    $router->post('admin/printer/destroy', 'PrinterController@destroy')->name('api/admin/printer/destroy');

    $router->get('admin/navigation/index', 'NavigationController@index')->name('api/admin/navigation/index');
    $router->get('admin/navigation/show', 'NavigationController@show')->name('api/admin/navigation/show');
    $router->get('admin/navigation/create', 'NavigationController@create')->name('api/admin/navigation/create');
    $router->post('admin/navigation/store', 'NavigationController@store')->name('api/admin/navigation/store');
    $router->get('admin/navigation/edit', 'NavigationController@edit')->name('api/admin/navigation/edit');
    $router->post('admin/navigation/update', 'NavigationController@update')->name('api/admin/navigation/update');
    $router->any('admin/navigation/action', 'NavigationController@action')->name('api/admin/navigation/action');
    $router->post('admin/navigation/destroy', 'NavigationController@destroy')->name('api/admin/navigation/destroy');

    $router->get('admin/category/index', 'CategoryController@index')->name('api/admin/category/index');
    $router->get('admin/category/show', 'CategoryController@show')->name('api/admin/category/show');
    $router->get('admin/category/create', 'CategoryController@create')->name('api/admin/category/create');
    $router->post('admin/category/store', 'CategoryController@store')->name('api/admin/category/store');
    $router->get('admin/category/edit', 'CategoryController@edit')->name('api/admin/category/edit');
    $router->post('admin/category/update', 'CategoryController@update')->name('api/admin/category/update');
    $router->any('admin/category/action', 'CategoryController@action')->name('api/admin/category/action');
    $router->post('admin/category/destroy', 'CategoryController@destroy')->name('api/admin/category/destroy');

    $router->get('admin/sms/index', 'SmsController@index')->name('api/admin/sms/index');
    $router->any('admin/sms/action', 'SmsController@action')->name('api/admin/sms/action');
    $router->any('admin/sms/import', 'SmsController@import')->name('api/admin/sms/import');
    $router->post('admin/sms/sendImportSms', 'SmsController@sendImportSms')->name('api/admin/sms/sendImportSms');
    $router->post('admin/sms/sendSms', 'SmsController@sendSms')->name('api/admin/sms/sendSms');

    // 商城
    $router->get('admin/shop/index', 'ShopController@index')->name('api/admin/shop/index');
    $router->get('admin/shop/show', 'ShopController@show')->name('api/admin/shop/show');
    $router->get('admin/shop/create', 'ShopController@create')->name('api/admin/shop/create');
    $router->post('admin/shop/store', 'ShopController@store')->name('api/admin/shop/store');
    $router->get('admin/shop/edit', 'ShopController@edit')->name('api/admin/shop/edit');
    $router->post('admin/shop/update', 'ShopController@update')->name('api/admin/shop/update');
    $router->any('admin/shop/action', 'ShopController@action')->name('api/admin/shop/action');
    $router->post('admin/shop/destroy', 'ShopController@destroy')->name('api/admin/shop/destroy');

    $router->get('admin/shopCategory/index', 'ShopCategoryController@index')->name('api/admin/shopCategory/index');
    $router->get('admin/shopCategory/show', 'ShopCategoryController@show')->name('api/admin/shopCategory/show');
    $router->get('admin/shopCategory/create', 'ShopCategoryController@create')->name('api/admin/shopCategory/create');
    $router->post('admin/shopCategory/store', 'ShopCategoryController@store')->name('api/admin/shopCategory/store');
    $router->get('admin/shopCategory/edit', 'ShopCategoryController@edit')->name('api/admin/shopCategory/edit');
    $router->post('admin/shopCategory/update', 'ShopCategoryController@update')->name('api/admin/shopCategory/update');
    $router->any('admin/shopCategory/action', 'ShopCategoryController@action')->name('api/admin/shopCategory/action');
    $router->post('admin/shopCategory/destroy', 'ShopCategoryController@destroy')->name('api/admin/shopCategory/destroy');

    $router->get('admin/goods/index', 'GoodsController@index')->name('api/admin/goods/index');
    $router->get('admin/goods/create', 'GoodsController@create')->name('api/admin/goods/create');
    $router->post('admin/goods/store', 'GoodsController@store')->name('api/admin/goods/store');
    $router->get('admin/goods/edit', 'GoodsController@edit')->name('api/admin/goods/edit');
    $router->post('admin/goods/save', 'GoodsController@save')->name('api/admin/goods/save');
    
    $router->post('admin/goods/imageStore', 'GoodsController@imageStore')->name('api/admin/goods/imageStore');
    $router->get('admin/goods/imageEdit', 'GoodsController@imageEdit')->name('api/admin/goods/imageEdit');
    $router->post('admin/goods/imageSave', 'GoodsController@imageSave')->name('api/admin/goods/imageSave');
    $router->get('admin/goods/complete', 'GoodsController@complete')->name('api/admin/goods/complete');

    $router->post('admin/goods/changeStatus', 'GoodsController@changeStatus')->name('api/admin/goods/changeStatus');
    $router->get('admin/goods/attribute', 'GoodsController@attribute')->name('api/admin/goods/attribute');

    $router->get('admin/goodsCategory/index', 'GoodsCategoryController@index')->name('api/admin/goodsCategory/index');
    $router->get('admin/goodsCategory/show', 'GoodsCategoryController@show')->name('api/admin/goodsCategory/show');
    $router->get('admin/goodsCategory/create', 'GoodsCategoryController@create')->name('api/admin/goodsCategory/create');
    $router->post('admin/goodsCategory/store', 'GoodsCategoryController@store')->name('api/admin/goodsCategory/store');
    $router->get('admin/goodsCategory/edit', 'GoodsCategoryController@edit')->name('api/admin/goodsCategory/edit');
    $router->post('admin/goodsCategory/save', 'GoodsCategoryController@save')->name('api/admin/goodsCategory/save');
    $router->any('admin/goodsCategory/action', 'GoodsCategoryController@action')->name('api/admin/goodsCategory/action');
    $router->post('admin/goodsCategory/destroy', 'GoodsCategoryController@destroy')->name('api/admin/goodsCategory/destroy');

    $router->get('admin/goodsType/index', 'GoodsTypeController@index')->name('api/admin/goodsType/index');
    $router->get('admin/goodsType/show', 'GoodsTypeController@show')->name('api/admin/goodsType/show');
    $router->get('admin/goodsType/create', 'GoodsTypeController@create')->name('api/admin/goodsType/create');
    $router->post('admin/goodsType/store', 'GoodsTypeController@store')->name('api/admin/goodsType/store');
    $router->get('admin/goodsType/edit', 'GoodsTypeController@edit')->name('api/admin/goodsType/edit');
    $router->post('admin/goodsType/update', 'GoodsTypeController@update')->name('api/admin/goodsType/update');
    $router->any('admin/goodsType/action', 'GoodsTypeController@action')->name('api/admin/goodsType/action');
    $router->post('admin/goodsType/destroy', 'GoodsTypeController@destroy')->name('api/admin/goodsType/destroy');

    // 商品属性
    $router->get('admin/goodsAttribute/index', 'GoodsAttributeController@index')->name('api/admin/goodsAttribute/index');
    $router->get('admin/goodsAttribute/create', 'GoodsAttributeController@create')->name('api/admin/goodsAttribute/create');
    $router->post('admin/goodsAttribute/store', 'GoodsAttributeController@store')->name('api/admin/goodsAttribute/store');
    $router->get('admin/goodsAttribute/edit', 'GoodsAttributeController@edit')->name('api/admin/goodsAttribute/edit');
    $router->post('admin/goodsAttribute/save', 'GoodsAttributeController@save')->name('api/admin/goodsAttribute/save');
    $router->any('admin/goodsAttribute/action', 'GoodsAttributeController@action')->name('api/admin/goodsAttribute/action');
    $router->post('admin/goodsAttribute/destroy', 'GoodsAttributeController@destroy')->name('api/admin/goodsAttribute/destroy');
    $router->get('admin/goodsAttribute/search', 'GoodsAttributeController@search')->name('api/admin/goodsAttribute/search');

    // 商品规格
    $router->get('admin/goodsSpecification/index', 'GoodsSpecificationController@index')->name('api/admin/goodsSpecification/index');
    $router->get('admin/goodsSpecification/create', 'GoodsSpecificationController@create')->name('api/admin/goodsSpecification/create');
    $router->post('admin/goodsSpecification/store', 'GoodsSpecificationController@store')->name('api/admin/goodsSpecification/store');
    $router->get('admin/goodsSpecification/edit', 'GoodsSpecificationController@edit')->name('api/admin/goodsSpecification/edit');
    $router->post('admin/goodsSpecification/save', 'GoodsSpecificationController@save')->name('api/admin/goodsSpecification/save');
    $router->any('admin/goodsSpecification/action', 'GoodsSpecificationController@action')->name('api/admin/goodsSpecification/action');
    $router->post('admin/goodsSpecification/destroy', 'GoodsSpecificationController@destroy')->name('api/admin/goodsSpecification/destroy');
    $router->get('admin/goodsSpecification/search', 'GoodsSpecificationController@search')->name('api/admin/goodsSpecification/search');


    $router->get('admin/goodsUnit/index', 'GoodsUnitController@index')->name('api/admin/goodsUnit/index');
    $router->get('admin/goodsUnit/create', 'GoodsUnitController@create')->name('api/admin/goodsUnit/create');
    $router->post('admin/goodsUnit/store', 'GoodsUnitController@store')->name('api/admin/goodsUnit/store');
    $router->get('admin/goodsUnit/edit', 'GoodsUnitController@edit')->name('api/admin/goodsUnit/edit');
    $router->post('admin/goodsUnit/update', 'GoodsUnitController@update')->name('api/admin/goodsUnit/update');
    $router->any('admin/goodsUnit/action', 'GoodsUnitController@action')->name('api/admin/goodsUnit/action');
    $router->post('admin/goodsUnit/destroy', 'GoodsUnitController@destroy')->name('api/admin/goodsUnit/destroy');

    $router->get('admin/goodsLayout/index', 'GoodsLayoutController@index')->name('api/admin/goodsLayout/index');
    $router->get('admin/goodsLayout/create', 'GoodsLayoutController@create')->name('api/admin/goodsLayout/create');
    $router->post('admin/goodsLayout/store', 'GoodsLayoutController@store')->name('api/admin/goodsLayout/store');
    $router->get('admin/goodsLayout/edit', 'GoodsLayoutController@edit')->name('api/admin/goodsLayout/edit');
    $router->post('admin/goodsLayout/update', 'GoodsLayoutController@update')->name('api/admin/goodsLayout/update');
    $router->any('admin/goodsLayout/action', 'GoodsLayoutController@action')->name('api/admin/goodsLayout/action');
    $router->post('admin/goodsLayout/destroy', 'GoodsLayoutController@destroy')->name('api/admin/goodsLayout/destroy');

    $router->get('admin/goodsBrand/index', 'GoodsBrandController@index')->name('api/admin/goodsBrand/index');
    $router->get('admin/goodsBrand/create', 'GoodsBrandController@create')->name('api/admin/goodsBrand/create');
    $router->post('admin/goodsBrand/store', 'GoodsBrandController@store')->name('api/admin/goodsBrand/store');
    $router->get('admin/goodsBrand/edit', 'GoodsBrandController@edit')->name('api/admin/goodsBrand/edit');
    $router->post('admin/goodsBrand/update', 'GoodsBrandController@update')->name('api/admin/goodsBrand/update');
    $router->any('admin/goodsBrand/action', 'GoodsBrandController@action')->name('api/admin/goodsBrand/action');
    $router->post('admin/goodsBrand/destroy', 'GoodsBrandController@destroy')->name('api/admin/goodsBrand/destroy');

    $router->get('admin/goodsOrder/index', 'GoodsOrderController@index')->name('api/admin/goodsOrder/index');
    $router->get('admin/goodsOrder/export', 'GoodsOrderController@export')->name('api/admin/goodsOrder/export');
    $router->get('admin/goodsOrder/info', 'GoodsOrderController@info')->name('api/admin/goodsOrder/info');
    $router->get('admin/goodsOrder/quickDelivery', 'GoodsOrderController@quickDelivery')->name('api/admin/goodsOrder/quickDelivery');
    $router->post('admin/goodsOrder/send', 'GoodsOrderController@send')->name('api/admin/goodsOrder/send');
    
    $router->get('admin/goodsOrder/virtualOrderIndex', 'GoodsOrderController@virtualOrderIndex')->name('api/admin/goodsOrder/virtualOrderIndex');
    $router->get('admin/goodsOrder/deliveryIndex', 'GoodsOrderController@deliveryIndex')->name('api/admin/goodsOrder/deliveryIndex');
    $router->get('admin/goodsOrder/deliveryInfo', 'GoodsOrderController@deliveryInfo')->name('api/admin/goodsOrder/deliveryInfo');
    $router->get('admin/goodsOrder/deliveryEdit', 'GoodsOrderController@deliveryEdit')->name('api/admin/goodsOrder/deliveryEdit');
    $router->post('admin/goodsOrder/deliverySave', 'GoodsOrderController@deliverySave')->name('api/admin/goodsOrder/deliverySave');

    $router->get('admin/goodsOrder/commentIndex', 'GoodsOrderController@commentIndex')->name('api/admin/goodsOrder/commentIndex');
    $router->get('admin/goodsOrder/commentEdit', 'GoodsOrderController@commentEdit')->name('api/admin/goodsOrder/commentEdit');
    $router->post('admin/goodsOrder/commentSave', 'GoodsOrderController@commentSave')->name('api/admin/goodsOrder/commentSave');
    $router->post('admin/goodsOrder/commentChangeStatus', 'GoodsOrderController@commentChangeStatus')->name('api/admin/goodsOrder/commentChangeStatus');
    
    $router->get('admin/goodsOrder/afterSaleIndex', 'GoodsOrderController@afterSaleIndex')->name('api/admin/goodsOrder/afterSaleIndex');
    $router->get('admin/goodsOrder/afterSaleEdit', 'GoodsOrderController@afterSaleEdit')->name('api/admin/goodsOrder/afterSaleEdit');
    $router->post('admin/goodsOrder/afterSaleSave', 'GoodsOrderController@afterSaleSave')->name('api/admin/goodsOrder/afterSaleSave');
    $router->post('admin/goodsOrder/afterSaleChangeStatus', 'GoodsOrderController@afterSaleChangeStatus')->name('api/admin/goodsOrder/afterSaleChangeStatus');
    
});
