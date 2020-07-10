<?php

/*
|--------------------------------------------------------------------------
| Mall Web Routes
|--------------------------------------------------------------------------
|
| 商城路由组
|
*/

// 后台管理
Route::group([
    'prefix'        => config('quark.route.prefix'),
    'namespace'     => config('quark.route.namespace'),
    'middleware'    => config('quark.route.middleware'),
], function ($router) {

    $router->get('admin/printer/index', 'PrinterController@index')->name('api/admin/printer/index');
    $router->get('admin/printer/show', 'PrinterController@show')->name('api/admin/printer/show');
    $router->get('admin/printer/create', 'PrinterController@create')->name('api/admin/printer/create');
    $router->post('admin/printer/store', 'PrinterController@store')->name('api/admin/printer/store');
    $router->get('admin/printer/edit', 'PrinterController@edit')->name('api/admin/printer/edit');
    $router->post('admin/printer/update', 'PrinterController@update')->name('api/admin/printer/update');
    $router->any('admin/printer/action', 'PrinterController@action')->name('api/admin/printer/action');
    $router->post('admin/printer/destroy', 'PrinterController@destroy')->name('api/admin/printer/destroy');

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
    $router->any('admin/goods/action', 'GoodsController@action')->name('api/admin/goods/action');
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

    $router->get('admin/goodsComment/index', 'GoodsCommentController@index')->name('api/admin/goodsComment/index');
    $router->get('admin/goodsComment/show', 'GoodsCommentController@show')->name('api/admin/goodsComment/show');
    $router->get('admin/goodsComment/create', 'GoodsCommentController@create')->name('api/admin/goodsComment/create');
    $router->post('admin/goodsComment/store', 'GoodsCommentController@store')->name('api/admin/goodsComment/store');
    $router->get('admin/goodsComment/edit', 'GoodsCommentController@edit')->name('api/admin/goodsComment/edit');
    $router->post('admin/goodsComment/update', 'GoodsCommentController@update')->name('api/admin/goodsComment/update');
    $router->any('admin/goodsComment/action', 'GoodsCommentController@action')->name('api/admin/goodsComment/action');
    $router->post('admin/goodsComment/destroy', 'GoodsCommentController@destroy')->name('api/admin/goodsComment/destroy');
    
    $router->get('admin/goodsAfterSale/index', 'GoodsAfterSaleController@index')->name('api/admin/goodsAfterSale/index');
    $router->get('admin/goodsAfterSale/show', 'GoodsAfterSaleController@show')->name('api/admin/goodsAfterSale/show');
    $router->get('admin/goodsAfterSale/edit', 'GoodsAfterSaleController@edit')->name('api/admin/goodsAfterSale/edit');
    $router->post('admin/goodsAfterSale/update', 'GoodsAfterSaleController@update')->name('api/admin/goodsAfterSale/update');

});

// 前台
Route::prefix('mall')->group(function() {
    Route::get('/', 'MallController@index');
});
