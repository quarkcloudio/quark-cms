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

    $router->get('admin/example/index', 'ExampleController@index')->name('api/admin/example/index');
    $router->get('admin/example/show', 'ExampleController@show')->name('api/admin/example/show');
    $router->get('admin/example/create', 'ExampleController@create')->name('api/admin/example/create');
    $router->post('admin/example/store', 'ExampleController@store')->name('api/admin/example/store');
    $router->get('admin/example/edit', 'ExampleController@edit')->name('api/admin/example/edit');
    $router->post('admin/example/update', 'ExampleController@update')->name('api/admin/example/update');
    $router->any('admin/example/action', 'ExampleController@action')->name('api/admin/example/action');
    $router->post('admin/example/destroy', 'ExampleController@destroy')->name('api/admin/example/destroy');

    $router->get('admin/article/index', 'ArticleController@index')->name('api/admin/article/index');
    $router->get('admin/article/create', 'ArticleController@create')->name('api/admin/article/create');
    $router->post('admin/article/store', 'ArticleController@store')->name('api/admin/article/store');
    $router->get('admin/article/edit', 'ArticleController@edit')->name('api/admin/article/edit');
    $router->post('admin/article/save', 'ArticleController@save')->name('api/admin/article/save');
    $router->post('admin/article/changeStatus', 'ArticleController@changeStatus')->name('api/admin/article/changeStatus');
    $router->get('admin/article/myPublished', 'ArticleController@myPublished')->name('api/admin/article/myPublished');
    
    $router->get('admin/article/categoryIndex', 'ArticleController@categoryIndex')->name('api/admin/article/categoryIndex');
    $router->get('admin/article/categoryCreate', 'ArticleController@categoryCreate')->name('api/admin/article/categoryCreate');
    $router->post('admin/article/categoryStore', 'ArticleController@categoryStore')->name('api/admin/article/categoryStore');
    $router->get('admin/article/categoryEdit', 'ArticleController@categoryEdit')->name('api/admin/article/categoryEdit');
    $router->post('admin/article/categorySave', 'ArticleController@categorySave')->name('api/admin/article/categorySave');
    $router->post('admin/article/categoryChangeStatus', 'ArticleController@categoryChangeStatus')->name('api/admin/article/categoryChangeStatus');

    $router->get('admin/video/index', 'VideoController@index')->name('api/admin/video/index');
    $router->get('admin/video/create', 'VideoController@create')->name('api/admin/video/create');
    $router->post('admin/video/store', 'VideoController@store')->name('api/admin/video/store');
    $router->get('admin/video/edit', 'VideoController@edit')->name('api/admin/video/edit');
    $router->post('admin/video/save', 'VideoController@save')->name('api/admin/video/save');
    $router->post('admin/video/changeStatus', 'VideoController@changeStatus')->name('api/admin/video/changeStatus');
    $router->get('admin/video/myPublished', 'VideoController@myPublished')->name('api/admin/video/myPublished');

    $router->get('admin/page/index', 'PageController@index')->name('api/admin/page/index');
    $router->get('admin/page/create', 'PageController@create')->name('api/admin/page/create');
    $router->post('admin/page/store', 'PageController@store')->name('api/admin/page/store');
    $router->get('admin/page/edit', 'PageController@edit')->name('api/admin/page/edit');
    $router->post('admin/page/save', 'PageController@save')->name('api/admin/page/save');
    $router->post('admin/page/changeStatus', 'PageController@changeStatus')->name('api/admin/page/changeStatus');
    
    $router->get('admin/user/index', 'UserController@index')->name('api/admin/user/index');
    $router->get('admin/user/create', 'UserController@create')->name('api/admin/user/create');
    $router->post('admin/user/store', 'UserController@store')->name('api/admin/user/store');
    $router->get('admin/user/edit', 'UserController@edit')->name('api/admin/user/edit');
    $router->post('admin/user/save', 'UserController@save')->name('api/admin/user/save');
    $router->post('admin/user/changeStatus', 'UserController@changeStatus')->name('api/admin/user/changeStatus');
    $router->any('admin/user/recharge', 'UserController@recharge')->name('api/admin/user/recharge');
    $router->any('admin/user/suggest', 'UserController@suggest')->name('api/admin/user/suggest');

    $router->get('admin/banner/index', 'BannerController@index')->name('api/admin/banner/index');
    $router->get('admin/banner/create', 'BannerController@create')->name('api/admin/banner/create');
    $router->post('admin/banner/store', 'BannerController@store')->name('api/admin/banner/store');
    $router->get('admin/banner/edit', 'BannerController@edit')->name('api/admin/banner/edit');
    $router->post('admin/banner/save', 'BannerController@save')->name('api/admin/banner/save');
    $router->post('admin/banner/changeStatus', 'BannerController@changeStatus')->name('api/admin/banner/changeStatus');

    $router->get('admin/bannerCategory/index', 'BannerCategoryController@index')->name('api/admin/bannerCategory/index');
    $router->get('admin/bannerCategory/create', 'BannerCategoryController@create')->name('api/admin/bannerCategory/create');
    $router->post('admin/bannerCategory/store', 'BannerCategoryController@store')->name('api/admin/bannerCategory/store');
    $router->get('admin/bannerCategory/edit', 'BannerCategoryController@edit')->name('api/admin/bannerCategory/edit');
    $router->post('admin/bannerCategory/save', 'BannerCategoryController@save')->name('api/admin/bannerCategory/save');
    $router->post('admin/bannerCategory/changeStatus', 'BannerCategoryController@changeStatus')->name('api/admin/bannerCategory/changeStatus');
    
    $router->get('admin/comment/index', 'CommentController@index')->name('api/admin/comment/index');
    $router->get('admin/comment/edit', 'CommentController@edit')->name('api/admin/comment/edit');
    $router->post('admin/comment/save', 'CommentController@save')->name('api/admin/comment/save');
    $router->post('admin/comment/changeStatus', 'CommentController@changeStatus')->name('api/admin/comment/changeStatus');
    
    $router->get('admin/link/index', 'LinkController@index')->name('api/admin/link/index');
    $router->get('admin/link/create', 'LinkController@create')->name('api/admin/link/create');
    $router->post('admin/link/store', 'LinkController@store')->name('api/admin/link/store');
    $router->get('admin/link/edit', 'LinkController@edit')->name('api/admin/link/edit');
    $router->post('admin/link/save', 'LinkController@save')->name('api/admin/link/save');
    $router->post('admin/link/changeStatus', 'LinkController@changeStatus')->name('api/admin/link/changeStatus');

    $router->get('admin/printer/index', 'PrinterController@index')->name('api/admin/printer/index');
    $router->get('admin/printer/create', 'PrinterController@create')->name('api/admin/printer/create');
    $router->post('admin/printer/store', 'PrinterController@store')->name('api/admin/printer/store');
    $router->get('admin/printer/edit', 'PrinterController@edit')->name('api/admin/printer/edit');
    $router->post('admin/printer/save', 'PrinterController@save')->name('api/admin/printer/save');
    $router->post('admin/printer/changeStatus', 'PrinterController@changeStatus')->name('api/admin/printer/changeStatus');

    $router->get('admin/navigation/index', 'NavigationController@index')->name('api/admin/navigation/index');
    $router->get('admin/navigation/create', 'NavigationController@create')->name('api/admin/navigation/create');
    $router->post('admin/navigation/store', 'NavigationController@store')->name('api/admin/navigation/store');
    $router->get('admin/navigation/edit', 'NavigationController@edit')->name('api/admin/navigation/edit');
    $router->post('admin/navigation/save', 'NavigationController@save')->name('api/admin/navigation/save');
    $router->post('admin/navigation/changeStatus', 'NavigationController@changeStatus')->name('api/admin/navigation/changeStatus');

    $router->get('admin/category/index', 'CategoryController@index')->name('api/admin/category/index');
    $router->get('admin/category/create', 'CategoryController@create')->name('api/admin/category/create');
    $router->post('admin/category/store', 'CategoryController@store')->name('api/admin/category/store');
    $router->get('admin/category/edit', 'CategoryController@edit')->name('api/admin/category/edit');
    $router->post('admin/category/save', 'CategoryController@save')->name('api/admin/category/save');
    $router->post('admin/category/changeStatus', 'CategoryController@changeStatus')->name('api/admin/category/changeStatus');

    $router->get('admin/sms/index', 'SmsController@index')->name('api/admin/sms/index');
    $router->post('admin/sms/changeStatus', 'SmsController@destroy')->name('api/admin/sms/changeStatus');
    $router->any('admin/sms/import', 'SmsController@import')->name('api/admin/sms/import');
    $router->post('admin/sms/sendImportSms', 'SmsController@sendImportSms')->name('api/admin/sms/sendImportSms');
    $router->post('admin/sms/sendSms', 'SmsController@sendSms')->name('api/admin/sms/sendSms');

    // 商城
    $router->get('admin/shop/index', 'ShopController@index')->name('api/admin/shop/index');
    $router->get('admin/shop/create', 'ShopController@create')->name('api/admin/shop/create');
    $router->post('admin/shop/store', 'ShopController@store')->name('api/admin/shop/store');
    $router->get('admin/shop/edit', 'ShopController@edit')->name('api/admin/shop/edit');
    $router->post('admin/shop/save', 'ShopController@save')->name('api/admin/shop/save');
    $router->post('admin/shop/changeStatus', 'ShopController@changeStatus')->name('api/admin/shop/changeStatus');

    $router->get('admin/shop/categoryIndex', 'ShopController@categoryIndex')->name('api/admin/shop/categoryIndex');
    $router->get('admin/shop/categoryCreate', 'ShopController@categoryCreate')->name('api/admin/shop/categoryCreate');
    $router->post('admin/shop/categoryStore', 'ShopController@categoryStore')->name('api/admin/shop/categoryStore');
    $router->get('admin/shop/categoryEdit', 'ShopController@categoryEdit')->name('api/admin/shop/categoryEdit');
    $router->post('admin/shop/categorySave', 'ShopController@categorySave')->name('api/admin/shop/categorySave');
    $router->post('admin/shop/categoryChangeStatus', 'ShopController@categoryChangeStatus')->name('api/admin/shop/categoryChangeStatus');

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

    $router->get('admin/goods/categoryIndex', 'GoodsController@categoryIndex')->name('api/admin/goods/categoryIndex');
    $router->get('admin/goods/categoryCreate', 'GoodsController@categoryCreate')->name('api/admin/goods/categoryCreate');
    $router->post('admin/goods/categoryStore', 'GoodsController@categoryStore')->name('api/admin/goods/categoryStore');
    $router->get('admin/goods/categoryEdit', 'GoodsController@categoryEdit')->name('api/admin/goods/categoryEdit');
    $router->post('admin/goods/categorySave', 'GoodsController@categorySave')->name('api/admin/goods/categorySave');
    $router->post('admin/goods/categoryChangeStatus', 'GoodsController@categoryChangeStatus')->name('api/admin/goods/categoryChangeStatus');

    $router->get('admin/goods/typeIndex', 'GoodsController@typeIndex')->name('api/admin/goods/typeIndex');
    $router->get('admin/goods/typeCreate', 'GoodsController@typeCreate')->name('api/admin/goods/typeCreate');
    $router->post('admin/goods/typeStore', 'GoodsController@typeStore')->name('api/admin/goods/typeStore');
    $router->get('admin/goods/typeEdit', 'GoodsController@typeEdit')->name('api/admin/goods/typeEdit');
    $router->post('admin/goods/typeSave', 'GoodsController@typeSave')->name('api/admin/goods/typeSave');
    $router->post('admin/goods/typeChangeStatus', 'GoodsController@typeChangeStatus')->name('api/admin/goods/typeChangeStatus');

    // 商品属性
    $router->get('admin/goods/attributeIndex', 'GoodsController@attributeIndex')->name('api/admin/goods/attributeIndex');
    $router->get('admin/goods/attributeCreate', 'GoodsController@attributeCreate')->name('api/admin/goods/attributeCreate');
    $router->post('admin/goods/attributeStore', 'GoodsController@attributeStore')->name('api/admin/goods/attributeStore');
    $router->get('admin/goods/attributeEdit', 'GoodsController@attributeEdit')->name('api/admin/goods/attributeEdit');
    $router->post('admin/goods/attributeSave', 'GoodsController@attributeSave')->name('api/admin/goods/attributeSave');
    $router->post('admin/goods/attributeChangeStatus', 'GoodsController@attributeChangeStatus')->name('api/admin/goods/attributeChangeStatus');

    // 商品规格
    $router->get('admin/goods/specificationIndex', 'GoodsController@specificationIndex')->name('api/admin/goods/specificationIndex');
    $router->get('admin/goods/specificationCreate', 'GoodsController@specificationCreate')->name('api/admin/goods/specificationCreate');
    $router->post('admin/goods/specificationStore', 'GoodsController@specificationStore')->name('api/admin/goods/specificationStore');
    $router->get('admin/goods/specificationEdit', 'GoodsController@specificationEdit')->name('api/admin/goods/specificationEdit');
    $router->post('admin/goods/specificationSave', 'GoodsController@specificationSave')->name('api/admin/goods/specificationSave');
    $router->post('admin/goods/specificationChangeStatus', 'GoodsController@specificationChangeStatus')->name('api/admin/goods/specificationChangeStatus');

    $router->get('admin/goods/unitIndex', 'GoodsController@unitIndex')->name('api/admin/goods/unitIndex');
    $router->get('admin/goods/unitCreate', 'GoodsController@unitCreate')->name('api/admin/goods/unitCreate');
    $router->post('admin/goods/unitStore', 'GoodsController@unitStore')->name('api/admin/goods/unitStore');
    $router->get('admin/goods/unitEdit', 'GoodsController@unitEdit')->name('api/admin/goods/unitEdit');
    $router->post('admin/goods/unitSave', 'GoodsController@unitSave')->name('api/admin/goods/unitSave');
    $router->post('admin/goods/unitChangeStatus', 'GoodsController@unitChangeStatus')->name('api/admin/goods/unitChangeStatus');

    $router->get('admin/goods/layoutIndex', 'GoodsController@layoutIndex')->name('api/admin/goods/layoutIndex');
    $router->get('admin/goods/layoutCreate', 'GoodsController@layoutCreate')->name('api/admin/goods/layoutCreate');
    $router->post('admin/goods/layoutStore', 'GoodsController@layoutStore')->name('api/admin/goods/layoutStore');
    $router->get('admin/goods/layoutEdit', 'GoodsController@layoutEdit')->name('api/admin/goods/layoutEdit');
    $router->post('admin/goods/layoutSave', 'GoodsController@layoutSave')->name('api/admin/goods/layoutSave');
    $router->post('admin/goods/layoutChangeStatus', 'GoodsController@layoutChangeStatus')->name('api/admin/goods/layoutChangeStatus');

    $router->get('admin/goodsBrand/index', 'GoodsBrandController@index')->name('api/admin/goodsBrand/index');
    $router->get('admin/goodsBrand/create', 'GoodsBrandController@create')->name('api/admin/goodsBrand/create');
    $router->post('admin/goodsBrand/store', 'GoodsBrandController@store')->name('api/admin/goodsBrand/store');
    $router->get('admin/goodsBrand/edit', 'GoodsBrandController@edit')->name('api/admin/goodsBrand/edit');
    $router->post('admin/goodsBrand/save', 'GoodsBrandController@save')->name('api/admin/goodsBrand/save');
    $router->post('admin/goodsBrand/changeStatus', 'GoodsBrandController@changeStatus')->name('api/admin/goodsBrand/changeStatus');

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
