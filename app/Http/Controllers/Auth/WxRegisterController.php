<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\WechatUser;
use App\Models\ActionLog;
use EasyWeChat\Factory;
use Str;
use Session;

class WxRegisterController extends Controller
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
        $this->middleware('guest');
    }

    /**
     * 注册账户，分为两种情况：1、如果已经通过PC或者其他客户端注册了账户，则提示进行账户绑定；2、如果没有注册账户，则填写账户信息注册
     */
    public function register(Request $request)
    {
        $wechatUserId = Session::get('wechat_user_id');

        if(empty($wechatUserId)) {
            return error('参数错误！');
        }

        $wechatUser = WechatUser::where('id',$wechatUserId)->first();

        return view('auth/wxRegister',$wechatUser);
    }

    /**
     * 从注册账户页面，选择跳转到绑定账户页面；用tab控件，可以选择手机号短信验证码登录绑定或者账户密码登录绑定
     */
    public function bindAccount(Request $request)
    {
        $wechatUserId = Session::get('wechat_user_id');

        if(empty($wechatUserId)) {
            return error('参数错误！');
        }

        $wechatUser = WechatUser::where('id',$wechatUserId)->first();

        return view('auth/wxBindAccount',$wechatUser);
    }
}
