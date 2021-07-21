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
            ['id' => 9,'title' => '服务器地址','type' => 'text','name' => 'EMAIL_HOST','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['id' => 10,'title' => '服务器端口','type' => 'text','name' => 'EMAIL_PORT','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['id' => 11,'title' => '发件人邮箱','type' => 'text','name' => 'EMAIL_USERNAME','group_name' => '邮件','value' => '','remark' => '','status' => 1],
            ['id' => 12,'title' => '发件人密码','type' => 'text','name' => 'EMAIL_PASSWORD','group_name' => '邮件','value' => '','remark' => '','status' => 1],

            ['id' => 13,'title' => 'AppKey','type' => 'text','name' => 'ALIDAYU_APP_KEY','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],
            ['id' => 14,'title' => 'AppSecret','type' => 'text','name' => 'ALIDAYU_APP_SECRET','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],
            ['id' => 15,'title' => '签名','type' => 'text','name' => 'ALIDAYU_SIGNNAME','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],
            ['id' => 16,'title' => '模板代码','type' => 'text','name' => 'ALIDAYU_TEMPLATE_CODE','group_name' => '阿里云通信','value' => '','remark' => '','status' => 1],

            ['id' => 23,'title' => '用户ID','type' => 'text','name' => 'SIOO_UID','group_name' => '希奥短信','value' => '','remark' => '','status' => 1],
            ['id' => 25,'title' => '用户密码','type' => 'text','name' => 'SIOO_PASSWORD','group_name' => '希奥短信','value' => '','remark' => '','status' => 1],

            ['id' => 26,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_PAY_APP_ID','group_name' => '微信支付','value' => '','remark' => 'AppID（JSAPI支付授权目录，例如：http://www.web.com/wechat/wechat/）','status' => 1],
            ['id' => 27,'title' => '商户号','type' => 'text','name' => 'WECHAT_PAY_MERCHANTID','group_name' => '微信支付','value' => '','remark' => '商户平台（商户号）','status' => 1],
            ['id' => 28,'title' => 'API密钥','type' => 'text','name' => 'WECHAT_PAY_KEY','group_name' => '微信支付','value' => '','remark' => '商户平台（API密钥）','status' => 1],
            ['id' => 29,'title' => '商户证书','type' => 'file','name' => 'WECHAT_PAY_APICLIENT_CERT','group_name' => '微信支付','value' => null,'remark' => 'apiclient_cert.pem','status' => 1],
            ['id' => 30,'title' => '证书密钥','type' => 'file','name' => 'WECHAT_PAY_APICLIENT_KEY','group_name' => '微信支付','value' => null,'remark' => 'apiclient_key.pem','status' => 1],

            ['id' => 31,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_APP_PAY_APP_ID','group_name' => '微信APP支付','value' => '','remark' => 'AppID（需要在开放平台申请）','status' => 1],
            ['id' => 32,'title' => '商户号','type' => 'text','name' => 'WECHAT_APP_PAY_MERCHANTID','group_name' => '微信APP支付','value' => '','remark' => '商户平台（商户号）','status' => 1],
            ['id' => 33,'title' => 'API密钥','type' => 'text','name' => 'WECHAT_APP_PAY_KEY','group_name' => '微信APP支付','value' => '','remark' => '商户平台（API密钥）','status' => 1],
            ['id' => 34,'title' => '商户证书','type' => 'file','name' => 'WECHAT_APP_PAY_APICLIENT_CERT','group_name' => '微信APP支付','value' => null,'remark' => 'apiclient_cert.pem','status' => 1],
            ['id' => 35,'title' => '证书密钥','type' => 'file','name' => 'WECHAT_APP_PAY_APICLIENT_KEY','group_name' => '微信APP支付','value' => null,'remark' => 'apiclient_key.pem','status' => 1],

            ['id' => 36,'title' => '签名类型','type' => 'text','name' => 'ALIPAY_SIGN_TYPE','group_name' => '支付宝','value' => 'RSA2','remark' => '','status' => 1],
            ['id' => 37,'title' => '应用ID','type' => 'text','name' => 'ALIPAY_APP_ID','group_name' => '支付宝','value' => '','remark' => '','status' => 1],
            ['id' => 38,'title' => '商户私钥','type' => 'textarea','name' => 'ALIPAY_PRIVATE_KEY','group_name' => '支付宝','value' => '','remark' => '','status' => 1],
            ['id' => 39,'title' => '支付宝公钥','type' => 'textarea','name' => 'ALIPAY_PUBLIC_KEY','group_name' => '支付宝','value' => '','remark' => '','status' => 1],

            ['id' => 40,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_DYH_APPID','group_name' => '微信订阅号','value' => '','remark' => 'AppID','status' => 1],
            ['id' => 41,'title' => '开发者密码','type' => 'text','name' => 'WECHAT_DYH_APPSECRET','group_name' => '微信订阅号','value' => '','remark' => 'AppSecret','status' => 1],
            ['id' => 42,'title' => '令牌','type' => 'text','name' => 'WECHAT_DYH_TOKEN','group_name' => '微信订阅号','value' => '','remark' => 'Token（服务器地址：https://website.com/wechat/server/token?type=dyh）','status' => 1],
            ['id' => 43,'title' => '消息密钥','type' => 'textarea','name' => 'WECHAT_DYH_ENCODINGAESKEY','group_name' => '微信订阅号','value' => '','remark' => 'EncodingAESKey','status' => 1],

            ['id' => 44,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_FWH_APPID','group_name' => '微信服务号','value' => '','remark' => 'AppID','status' => 1],
            ['id' => 45,'title' => '开发者密码','type' => 'text','name' => 'WECHAT_FWH_APPSECRET','group_name' => '微信服务号','value' => '','remark' => 'AppSecret','status' => 1],
            ['id' => 46,'title' => '令牌','type' => 'text','name' => 'WECHAT_FWH_TOKEN','group_name' => '微信服务号','value' => '','remark' => 'Token（服务器地址：https://website.com/wechat/server/token?type=fwh）','status' => 1],
            ['id' => 47,'title' => '消息密钥','type' => 'textarea','name' => 'WECHAT_FWH_ENCODINGAESKEY','group_name' => '微信服务号','value' => '','remark' => 'EncodingAESKey','status' => 1],

            ['id' => 48,'title' => '开发者ID','type' => 'text','name' => 'WECHAT_MP_APPID','group_name' => '微信小程序','value' => '','remark' => 'AppID','status' => 1],
            ['id' => 49,'title' => '开发者密码','type' => 'text','name' => 'WECHAT_MP_APPSECRET','group_name' => '微信小程序','value' => '','remark' => 'AppSecret','status' => 1],
        ]);
    }
}
