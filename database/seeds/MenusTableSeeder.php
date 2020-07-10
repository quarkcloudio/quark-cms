<?php

use Illuminate\Database\Seeder;

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
            ['id' => 4,'name' => '文章列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 3,'sort' => 0,'path' => '/quark/engine?api=admin/article/index&component=table','show'  => 1,'status' => 1],
            ['id' => 5,'name' => '发布文章','guard_name' => 'admin','icon' => '','type'=>'form','pid' => 3,'sort' => 0,'path' => '/quark/engine?api=admin/article/create&component=form','show'  => 1,'status' => 1],
            
            ['id' => 7,'name' => '单页管理','guard_name' => 'admin','icon' => 'icon-page','type'=>'default','pid' => 0,'sort' => 0,'path' => '/page','show'  => 1,'status' => 1],
            ['id' => 8,'name' => '单页列表','guard_name' => 'admin','icon' => '','type'=>'default','pid' => 7,'sort' => 0,'path' => '/quark/engine?api=admin/page/index&component=table','show'  => 1,'status' => 1],
            ['id' => 9,'name' => '添加单页','guard_name' => 'admin','icon' => '','type'=>'default','pid' => 7,'sort' => 0,'path' => '/quark/engine?api=admin/page/create&component=form','show'  => 1,'status' => 1],
            
            ['id' => 10,'name' => '会员管理','guard_name' => 'admin','icon' => 'icon-user','type'=>'default','pid' => 0,'sort' => 0,'path' => '/user','show'  => 1,'status' => 1],
            ['id' => 11,'name' => '会员列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 10,'sort' => 0,'path' => '/quark/engine?api=admin/user/index&component=table','show'  => 1,'status' => 1],
            ['id' => 12,'name' => '添加会员','guard_name' => 'admin','icon' => '','type'=>'form','pid' => 10,'sort' => 0,'path' => '/quark/engine?api=admin/user/create&component=form','show'  => 1,'status' => 1],
            
            ['id' => 18,'name' => '广告管理','guard_name' => 'admin','icon' => 'icon-banner','type'=>'default','pid' => 0,'sort' => 0,'path' => '/banner','show'  => 1,'status' => 1],
            ['id' => 19,'name' => '广告列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 18,'sort' => 0,'path' => '/quark/engine?api=admin/banner/index&component=table','show'  => 1,'status' => 1],
            ['id' => 20,'name' => '广告位列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 18,'sort' => 0,'path' => '/quark/engine?api=admin/bannerCategory/index&component=table','show'  => 1,'status' => 1],
            
            ['id' => 21,'name' => '应用插件','guard_name' => 'admin','icon' => 'icon-plugin','type'=>'default','pid' => 0,'sort' => 0,'path' => '/plugin','show'  => 1,'status' => 1],
            ['id' => 22,'name' => '评论管理','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 21,'sort' => 0,'path' => '/quark/engine?api=admin/comment/index&component=table','show'  => 1,'status' => 1],
            ['id' => 23,'name' => '友情链接','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 21,'sort' => 0,'path' => '/quark/engine?api=admin/link/index&component=table','show'  => 1,'status' => 1],

            ['id' => 29,'name' => '所有导航','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 25,'sort' => 0,'path' => '/quark/engine?api=admin/navigation/index&component=table','show'  => 1,'status' => 1],
            ['id' => 30,'name' => '分类列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 25,'sort' => 0,'path' => '/quark/engine?api=admin/category/index&component=table','show'  => 1,'status' => 1],
            ['id' => 31,'name' => '短信列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 25,'sort' => 0,'path' => '/quark/engine?api=admin/sms/index&component=table','show'  => 1,'status' => 1],
           
            ['id' => 61,'name' => '分类列表','guard_name' => 'admin','icon' => '','type'=>'table','pid' => 3,'sort' => 0,'path' => '/quark/engine?api=admin/articleCategory/index&component=table','show'  => 1,'status' => 1],
        ]);
    }
}
