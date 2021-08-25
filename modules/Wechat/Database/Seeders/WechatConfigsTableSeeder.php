<?php

namespace Modules\Wechat\Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class WechatConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wechat_configs')->insert([
            ['id' => 1,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_DYH_APPID','group_name' => '微信订阅号','value' => '','remark' => 'AppID','status' => 1],
            ['id' => 2,'title' => '开发者密码','type' => 'text','name' => 'WECHAT_DYH_APPSECRET','group_name' => '微信订阅号','value' => '','remark' => 'AppSecret','status' => 1],
            ['id' => 3,'title' => '令牌','type' => 'text','name' => 'WECHAT_DYH_TOKEN','group_name' => '微信订阅号','value' => '','remark' => 'Token（服务器地址：https://website.com/wechat/server/token?type=dyh）','status' => 1],
            ['id' => 4,'title' => '消息密钥','type' => 'textarea','name' => 'WECHAT_DYH_ENCODINGAESKEY','group_name' => '微信订阅号','value' => '','remark' => 'EncodingAESKey','status' => 1],

            ['id' => 5,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_FWH_APPID','group_name' => '微信服务号','value' => '','remark' => 'AppID','status' => 1],
            ['id' => 6,'title' => '开发者密码','type' => 'text','name' => 'WECHAT_FWH_APPSECRET','group_name' => '微信服务号','value' => '','remark' => 'AppSecret','status' => 1],
            ['id' => 7,'title' => '令牌','type' => 'text','name' => 'WECHAT_FWH_TOKEN','group_name' => '微信服务号','value' => '','remark' => 'Token（服务器地址：https://website.com/wechat/server/token?type=fwh）','status' => 1],
            ['id' => 8,'title' => '消息密钥','type' => 'textarea','name' => 'WECHAT_FWH_ENCODINGAESKEY','group_name' => '微信服务号','value' => '','remark' => 'EncodingAESKey','status' => 1],

            ['id' => 9,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_MP_APPID','group_name' => '微信小程序','value' => '','remark' => 'AppID','status' => 1],
            ['id' => 10,'title' => '开发者密码','type' => 'text','name' => 'WECHAT_MP_APPSECRET','group_name' => '微信小程序','value' => '','remark' => 'AppSecret','status' => 1],

            ['id' => 11,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_PAY_APP_ID','group_name' => '微信支付','value' => '','remark' => 'AppID（JSAPI支付授权目录，例如：http://www.web.com/wechat/wechat/）','status' => 1],
            ['id' => 12,'title' => '商户号','type' => 'text','name' => 'WECHAT_PAY_MERCHANTID','group_name' => '微信支付','value' => '','remark' => '商户平台（商户号）','status' => 1],
            ['id' => 13,'title' => 'API密钥','type' => 'text','name' => 'WECHAT_PAY_KEY','group_name' => '微信支付','value' => '','remark' => '商户平台（API密钥）','status' => 1],
            ['id' => 14,'title' => '商户证书','type' => 'file','name' => 'WECHAT_PAY_APICLIENT_CERT','group_name' => '微信支付','value' => null,'remark' => 'apiclient_cert.pem','status' => 1],
            ['id' => 15,'title' => '证书密钥','type' => 'file','name' => 'WECHAT_PAY_APICLIENT_KEY','group_name' => '微信支付','value' => null,'remark' => 'apiclient_key.pem','status' => 1],

            ['id' => 16,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_APP_PAY_APP_ID','group_name' => '微信APP支付','value' => '','remark' => 'AppID（需要在开放平台申请）','status' => 1],
            ['id' => 17,'title' => '商户号','type' => 'text','name' => 'WECHAT_APP_PAY_MERCHANTID','group_name' => '微信APP支付','value' => '','remark' => '商户平台（商户号）','status' => 1],
            ['id' => 18,'title' => 'API密钥','type' => 'text','name' => 'WECHAT_APP_PAY_KEY','group_name' => '微信APP支付','value' => '','remark' => '商户平台（API密钥）','status' => 1],
            ['id' => 19,'title' => '商户证书','type' => 'file','name' => 'WECHAT_APP_PAY_APICLIENT_CERT','group_name' => '微信APP支付','value' => null,'remark' => 'apiclient_cert.pem','status' => 1],
            ['id' => 20,'title' => '证书密钥','type' => 'file','name' => 'WECHAT_APP_PAY_APICLIENT_KEY','group_name' => '微信APP支付','value' => null,'remark' => 'apiclient_key.pem','status' => 1],
        ]);
    }
}
