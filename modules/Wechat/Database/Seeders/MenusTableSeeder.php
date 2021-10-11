<?php

namespace Modules\Wechat\Database\Seeders;

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
        $getId = DB::table('menus')->insertGetId(['name' => '微信管理','guard_name' => 'admin','icon' => 'icon-shop','type'=>'default','pid' => '0','sort' => '-1','path' => '/wechat','show'  => '1','status' => '1']);

        DB::table('menus')->insert([
            ['name' => '公众号配置','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => $getId,'sort' => '0','path' => 'admin/wechatConfig/setting-form','show'  => '1','status' => '1'],
            ['name' => '同步用户','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => $getId,'sort' => '0','path' => 'admin/wechatSyncUserTask/index','show'  => '1','status' => '1'],
            ['name' => '订阅号菜单','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => $getId,'sort' => '0','path' => 'admin/dyhMenu/index','show'  => '1','status' => '1'],
            ['name' => '服务号菜单','guard_name' => 'admin','icon' => '','type'=>'engine','pid' => $getId,'sort' => '0','path' => 'admin/fwhMenu/index','show'  => '1','status' => '1'],
        ]);
    }
}