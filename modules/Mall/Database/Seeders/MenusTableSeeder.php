<?php

namespace Modules\Mall\Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([

            ['id' =>'24','name' => '打印机管理','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => 21,'sort' => 0,'path' => 'admin/printer/index','show'  => 1,'status' => 1],
                       
            ['id' =>'38','name' => '商城管理','guard_name' => 'admin','icon' => 'icon-shop','type'=>'default','pid' => '0','sort' => '-1','path' => '/mall','show'  => '0','status' => '1'],
            ['id' =>'39','name' => '商家管理','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '38','sort' => '0','path' => '/mall/shop','show'  => '1','status' => '1'],
            ['id' =>'40','name' => '商家列表','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '39','sort' => '0','path' => 'admin/shop/index','show'  => '1','status' => '1'],
            ['id' =>'43','name' => '商家分类','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '39','sort' => '0','path' => 'admin/shopCategory/index','show'  => '1','status' => '1'],

            ['id' =>'46','name' => '商品管理','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '38','sort' => '0','path' => '/mall/goods','show'  => '1','status' => '1'],
            ['id' =>'47','name' => '商品列表','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '46','sort' => '0','path' => '/goods/index','show'  => '1','status' => '1'],
            ['id' =>'48','name' => '商品分类','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '46','sort' => '0','path' => 'admin/goodsCategory/index','show'  => '1','status' => '1'],
            ['id' =>'49','name' => '品牌管理','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '38','sort' => '0','path' => '/mall/goodsBrand','show'  => '1','status' => '1'],
            ['id' =>'50','name' => '品牌列表','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '49','sort' => '0','path' => 'admin/goodsBrand/index','show'  => '1','status' => '1'],
            ['id' =>'51','name' => '商品类型','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '46','sort' => '0','path' => 'admin/goodsType/index','show'  => '1','status' => '1'],
            ['id' =>'52','name' => '商品订单','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '38','sort' => '0','path' => '/mall/goodsOrder','show'  => '1','status' => '1'],
            ['id' =>'53','name' => '订单列表','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '52','sort' => '0','path' => '/goodsOrder/index','show'  => '1','status' => '1'],
            ['id' =>'54','name' => '虚拟订单','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '52','sort' => '0','path' => '/goodsOrder/virtualOrderIndex','show'  => '1','status' => '1'],
            ['id' =>'55','name' => '发货单列表','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '52','sort' => '0','path' => '/goodsOrder/deliveryIndex','show'  => '1','status' => '1'],
            ['id' =>'56','name' => '退款订单','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '52','sort' => '0','path' => '/goodsOrder/refundIndex','show'  => '0','status' => '1'],
            ['id' =>'57','name' => '售后订单','guard_name' => 'admin','icon' => '','type'=>'default','pid' => '52','sort' => '0','path' => '/mall/goodsOrder/afterSaleIndex','show'  => '1','status' => '1'],
            ['id' =>'58','name' => '评价管理','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '52','sort' => '0','path' => 'admin/goodsComment/index','show'  => '1','status' => '1'],
            ['id' =>'59','name' => '商品单位','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '46','sort' => '0','path' => 'admin/goodsUnit/index','show'  => '1','status' => '1'],
            ['id' =>'60','name' => '详情版式','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => '46','sort' => '0','path' => 'admin/goodsLayout/index','show'  => '1','status' => '1'],
        ]);
    }
}
