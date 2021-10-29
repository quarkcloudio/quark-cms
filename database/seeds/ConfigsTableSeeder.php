<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->insert([
            ['title' => '服务器地址','type' => 'text','name' => 'EMAIL_HOST','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['title' => '服务器端口','type' => 'text','name' => 'EMAIL_PORT','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['title' => '加密方式','type' => 'text','name' => 'MAIL_ENCRYPTION','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['title' => '发件人邮箱','type' => 'text','name' => 'EMAIL_USERNAME','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['title' => '发件人密码','type' => 'text','name' => 'EMAIL_PASSWORD','group_name' => '邮件','value' => '','remark' => '','status' => 1],

            ['title' => 'AppKey','type' => 'text','name' => 'ALIDAYU_APP_KEY','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],
            ['title' => 'AppSecret','type' => 'text','name' => 'ALIDAYU_APP_SECRET','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],
            ['title' => '签名','type' => 'text','name' => 'ALIDAYU_SIGNNAME','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],
            ['title' => '模板代码','type' => 'text','name' => 'ALIDAYU_TEMPLATE_CODE','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],

            ['title' => '用户ID','type' => 'text','name' => 'SIOO_UID','group_name' => '希奥短信','value' => '','remark' => '','status' => 1],
            ['title' => '用户密码','type' => 'text','name' => 'SIOO_PASSWORD','group_name' => '希奥短信','value' => '','remark' => '','status' => 1],

            ['title' => '签名类型','type' => 'text','name' => 'ALIPAY_SIGN_TYPE','group_name' => '支付宝','value' => 'RSA2','remark' => '','status' => 1],
            ['title' => '应用ID','type' => 'text','name' => 'ALIPAY_APP_ID','group_name' => '支付宝','value' => '','remark' => '','status' => 1],
            ['title' => '商户私钥','type' => 'textarea','name' => 'ALIPAY_PRIVATE_KEY','group_name' => '支付宝','value' => '','remark' => '','status' => 1],
            ['title' => '支付宝公钥','type' => 'textarea','name' => 'ALIPAY_PUBLIC_KEY','group_name' => '支付宝','value' => '','remark' => '','status' => 1]
        ]);
    }
}
