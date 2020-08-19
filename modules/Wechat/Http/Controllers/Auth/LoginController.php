<?php

namespace Modules\Wechat\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Wechat\Models\WechatUser;
use EasyWeChat\Factory;

class LoginController extends Controller
{
    /**
     * 用户退出方法
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function logout()
    {
        $result = Auth::logout();

        if($result !== false) {
            return success('已退出！');
        } else {
            return error('错误！');
        }
    }

    /**
     * 授权方法
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $targetUrl   = $request->input('targetUrl');

        $app = Factory::officialAccount(wechat_config('fwh'));
        $oauth = $app->oauth;

        $user = Auth::user();
        // 未登录
        if (empty($user)) {
            session(['target_url' => $targetUrl]);
            return $oauth->redirect();
        }
    }

    /**
     * 授权回调方法
     *
     * @return \Illuminate\Http\Response
     */
    public function callback()
    {
        $app = Factory::officialAccount(wechat_config('fwh'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $wechatUser = $oauth->user()->toArray();

        if(empty($wechatUser)) {
            return error('授权错误！');
        }

        session(['wechat_user' => $wechatUser]);

        // 定义对象
        $query = WechatUser::query()
        ->where('openid', $wechatUser['original']['openid'])
        ->where('type','FWH');

        if(isset($wechatUser['original']['unionid'])) {
            $query->orWhere('unionid', $wechatUser['original']['unionid']);
        }

        $getWechatUser = $query->first();

        if(empty($getWechatUser)) {

            // 没有用户跳转到注册流程
            return redirect(url('wechat/register'));
        }

        if(empty($getWechatUser['uid'])) {
            // 记录微信用户id
            session(['wechat_user_id' => $getWechatUser['id']]);

            // 跳转到绑定用户信息页面，完善用户名、手机号、密码等信息
            return redirect(url('wechat/bindAccount'));
        }

        // 快捷登录
        Auth::loginUsingId($getWechatUser['uid']);

        $targetUrl = session('target_url');

        // 跳转
        if($targetUrl) {
            return redirect(url($targetUrl));
        }
    }
}