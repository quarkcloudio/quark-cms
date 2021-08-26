<?php

namespace Modules\Wechat\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
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

        // 定义对象
        $query = User::where('wechat_fwh_openid', $wechatUser['original']['openid']);

        if(isset($wechatUser['original']['unionid'])) {
            $query->orWhere('wechat_unionid', $wechatUser['original']['unionid']);
        }

        $getWechatUser = $query->first();

        if(empty($getWechatUser)) {

            // 将微信头像保存到服务器
            $avatarInfo = download_picture_to_storage($wechatUser['avatar']);

            if($avatarInfo['status'] == 'error') {
                return $avatarInfo;
            }

            $wechatUser['nickname'] = filter_emoji($wechatUser['nickname']);
            $wechatUser['avatar'] = json_encode($avatarInfo['data']);
            
            if(!isset($wechatUser['original']['unionid'])) {
                $wechatUser['original']['unionid'] = null;
            }

            session(['wechat_user' => $wechatUser]);
            
            // 没有用户跳转到注册流程
            return redirect(url('wechat/register'));
        }

        // 快捷登录
        Auth::loginUsingId($getWechatUser['id']);

        $targetUrl = session('target_url');

        // 跳转
        if($targetUrl) {
            return redirect(url($targetUrl));
        }
    }
}