<?php

namespace Modules\Mall\Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class GoodsExpresseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('goods_expresses')->insert([
            [
                'id' => 1,
                'name' => '顺丰快递',
                'status' =>1,
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ],
            [
                'id' => 2,
                'name' => '韵达快递',
                'status' =>1,
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ],
            [
                'id' => 3,
                'name' => '申通快递',
                'status' =>1,
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ],
            [
                'id' => 4,
                'name' => '圆通快递',
                'status' =>1,
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ],
            [
                'id' => 5,
                'name' => '百世快递',
                'status' =>1,
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ]
       ]);
    }
}
