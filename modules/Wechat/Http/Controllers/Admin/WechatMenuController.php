<?php

namespace Modules\Wechat\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Quark;

class WechatMenuController extends QuarkController
{
    public function index(Request $request)
    {
        $app = Factory::officialAccount(wechat_config('fwh'));

        $buttons = [
            [
                "type" => "view",
                "name" => "业务窗口",
                "url"  => "http://yjt.xingo.cn/index/index"
            ],
            [
                "type" => "view",
                "name" => "预约服务",
                "url"  => "http://yjt.xingo.cn/page/index?name=yyfw"
            ],
            [
                "type" => "view",
                "name" => "意见建议",
                "url"  => "http://yjt.xingo.cn/page/index?name=yjjy"
            ]
        ];

        $app->menu->create($buttons);
    }
}
