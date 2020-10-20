<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

            ['id' => 3,'name' => '文章管理','guard_name' => 'admin','icon' => 'icon-article','type'=>'default','pid' => 0,'sort' => 0,'path' => '/article','show'  => 1,'status' => 1],
            ['id' => 4,'name' => '文章列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 3,'sort' => 0,'path' => 'admin/article/index','show'  => 1,'status' => 1],
            ['id' => 5,'name' => '发布文章','guard_name' => 'admin','icon' => '','type'=>'form','pid' => 3,'sort' => 0,'path' => 'admin/article/create','show'  => 1,'status' => 1],
            
            ['id' => 7,'name' => '单页管理','guard_name' => 'admin','icon' => 'icon-page','type'=>'default','pid' => 0,'sort' => 0,'path' => '/page','show'  => 1,'status' => 1],
            ['id' => 8,'name' => '单页列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 7,'sort' => 0,'path' => 'admin/page/index','show'  => 1,'status' => 1],
            ['id' => 9,'name' => '添加单页','guard_name' => 'admin','icon' => '','type'=>'form','pid' => 7,'sort' => 0,'path' => 'admin/page/create','show'  => 1,'status' => 1],
            
            ['id' => 10,'name' => '会员管理','guard_name' => 'admin','icon' => 'icon-user','type'=>'default','pid' => 0,'sort' => 0,'path' => '/user','show'  => 1,'status' => 1],
            ['id' => 11,'name' => '会员列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 10,'sort' => 0,'path' => 'admin/user/index','show'  => 1,'status' => 1],
            ['id' => 12,'name' => '添加会员','guard_name' => 'admin','icon' => '','type'=>'form','pid' => 10,'sort' => 0,'path' => 'admin/user/create','show'  => 1,'status' => 1],
            
            ['id' => 18,'name' => '广告管理','guard_name' => 'admin','icon' => 'icon-banner','type'=>'default','pid' => 0,'sort' => 0,'path' => '/banner','show'  => 1,'status' => 1],
            ['id' => 19,'name' => '广告列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 18,'sort' => 0,'path' => 'admin/banner/index','show'  => 1,'status' => 1],
            ['id' => 20,'name' => '广告位列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 18,'sort' => 0,'path' => 'admin/bannerCategory/index','show'  => 1,'status' => 1],
            
            ['id' => 21,'name' => '应用插件','guard_name' => 'admin','icon' => 'icon-plugin','type'=>'default','pid' => 0,'sort' => 0,'path' => '/plugin','show'  => 1,'status' => 1],
            ['id' => 22,'name' => '评论管理','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 21,'sort' => 0,'path' => 'admin/comment/index','show'  => 1,'status' => 1],
            ['id' => 23,'name' => '友情链接','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 21,'sort' => 0,'path' => 'admin/link/index','show'  => 1,'status' => 1],

            ['id' => 29,'name' => '所有导航','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 25,'sort' => 0,'path' => 'admin/navigation/index','show'  => 1,'status' => 1],
            ['id' => 30,'name' => '分类列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 25,'sort' => 0,'path' => 'admin/category/index','show'  => 1,'status' => 1],
            ['id' => 31,'name' => '短信列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 25,'sort' => 0,'path' => 'admin/sms/index','show'  => 1,'status' => 1],
           
            ['id' => 61,'name' => '分类列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 3,'sort' => 0,'path' => 'admin/articleCategory/index','show'  => 1,'status' => 1],
        ]);
    }
}
