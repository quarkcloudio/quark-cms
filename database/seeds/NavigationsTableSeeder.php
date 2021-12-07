<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('navigations')->insert([
            ['id' => 1,'pid' => '0','uid' => '0','title' => '首页','url_type' => 4,'url' => '/index/index','sort' => 0,'status' => 1,'created_at' => date('Y-m-d H:i:s',time()),'updated_at' => date('Y-m-d H:i:s',time())],
            ['id' => 2,'pid' => '0','uid' => '0','title' => '默认分类','url_type' => 4,'url' => '/article/list?name=default','sort' => 0,'status' => 1,'created_at' => date('Y-m-d H:i:s',time()),'updated_at' => date('Y-m-d H:i:s',time())],
            ['id' => 3,'pid' => '0','uid' => '0','title' => '关于我们','url_type' => 4,'url' => '/page/index?name=aboutus','sort' => 0,'status' => 1,'created_at' => date('Y-m-d H:i:s',time()),'updated_at' => date('Y-m-d H:i:s',time())],
       ]);
    }
}
