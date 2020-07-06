<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\WechatUser;
use App\Models\ActionLog;
use EasyWeChat\Factory;
use Str;
use Session;

class WxLoginController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

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

        $app = Factory::officialAccount(wechat_config());
        $oauth = $app->oauth;

        $user = Auth::user();
        // 未登录
        if (empty($user)) {
            session(['target_url'=> $targetUrl]);
            return $oauth->redirect();
        }
    }

    /**
     * 授权回调方法
     *
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        $app = Factory::officialAccount(wechat_config('FWH'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $wechatUser = $oauth->user()->toArray();

        if(!isset($wechatUser['original']['unionid'])) {
            return error('unionid不能为空，请前往微信开放平台绑定！');
        }

        $openid = $wechatUser['original']['openid'];
        $unionid = $wechatUser['original']['unionid'];
        $nickname = $wechatUser['nickname'];
        $sex = $wechatUser['original']['sex'];
        $avatar = $wechatUser['avatar'];

        if(empty($openid) || empty($unionid) || empty($nickname) || empty($avatar)) {
            return error('参数错误！');
        }

        $wechatUser = WechatUser::where('unionid', $unionid)->first();

        // 不存在用户的情况，则为新用户
        if(empty($wechatUser)) {

            // 组合数组
            $wechatUserData['nickname'] = $nickname;
            $wechatUserData['sex'] = $sex;

            // 将微信头像保存到服务器
            $avatarInfo = download_picture_to_storage($avatar);

            if($avatarInfo['status'] == 'error') {
                return $avatarInfo;
            }

            $wechatUserData['avatar'] = $avatarInfo['data']['id'];
            $wechatUserData['openid'] = $openid;
            $wechatUserData['unionid'] = $unionid;
            $wechatUserData['type'] = 'FWH';

            // 写入wechat_users表
            $wechatUserId = WechatUser::insertGetId($wechatUserData);

            if(empty($wechatUserId)) {
                return error('写入wechatuser表出错！');
            }

            // 存储微信用户表
            session(['wechat_user_id'=> $wechatUserId]);

            // 注册账户
            return redirect(url('wxRegister'));
        } else {

            $hasThisWechatUser = WechatUser::where('unionid', $unionid)
            ->where('openid',$openid)
            ->where('type','FWH')
            ->first();
            
            // 不存在本次登录的openid时，插入WechatUser
            if(!$hasThisWechatUser) {

                // 写入wechat_users表
                $wechatUserData['nickname'] = $nickname;
                $wechatUserData['sex'] = $sex;

                // 将微信头像保存到服务器
                $avatarInfo = download_picture_to_storage($avatar);

                if($avatarInfo['status'] == 'error') {
                    return $avatarInfo;
                }

                $wechatUserData['avatar'] = $avatarInfo['data']['id'];
                $wechatUserData['openid'] = $openid;
                $wechatUserData['unionid'] = $unionid;
                $wechatUserData['type'] = 'FWH';

                // 写入wechat_users表
                $wechatUserId = WechatUser::insertGetId($wechatUserData);

                if(empty($wechatUserId)) {
                    return error('写入wechatuser表出错！');
                }

                // 存储微信用户表
                session(['wechat_user_id'=> $wechatUserId]);
            }

            // 注册账户
            if(empty($wechatUser['uid'])) {
                return redirect(url('wxRegister'));
            }

            $uid = $wechatUser['uid'];

            // 快捷登录
            $loginResult = Auth::loginUsingId($uid);

            if($loginResult) {
                return success('登录成功！');
            } else {
                return error('登录失败，请重试！');
            }
        }

        $targetUrl = session('target_url');

        // 跳转
        if($targetUrl) {
            return redirect(url($targetUrl));
        }
    }
}
