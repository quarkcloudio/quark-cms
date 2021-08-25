<?php

namespace Modules\Wechat\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Modules\Wechat\Models\WechatUser;
use Str;
use DB;

class RegisterController extends Controller
{
    /**
     * 用户注册
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $wechatUser = session('wechat_user');

        if(empty($wechatUser)) {
            return error('未获取到微信用户信息！');
        }

        if(session('need_bind_account')) {

            // 跳转到绑定用户信息页面，完善用户名、手机号、密码等信息
            return redirect(url('wechat/bindAccount'));
        } else {

            // 不需要绑定用户信息，系统自动填写随机用户信息
            $uid = $this->createUser($wechatUser);

            if(empty($uid)) {
                return error('创建用户失败！');
            }

            // 快捷登录
            Auth::loginUsingId($uid);

            $targetUrl = session('target_url');

            // 跳转
            if($targetUrl) {
                return redirect(url($targetUrl));
            }
        }
    }

    protected function createUser($wechatUser)
    {
        // 启动事务
        DB::beginTransaction();

        try {
            $data['username'] = Str::random(8) . '-' . time(); // 临时用户名
            $data['email'] = Str::random(8) . '-' . time(); // 临时邮箱
            $data['phone'] = Str::random(8) . '-' . time(); // 临时手机号
            $data['nickname'] = $wechatUser['nickname'];
            $data['sex'] = $wechatUser['original']['sex'];
            $data['password'] = bcrypt(env('APP_KEY'));
            $data['avatar'] = $wechatUser['avatar'];
            $data['wechat_fwh_openid'] = $wechatUser['original']['openid'];
            $data['wechat_unionid'] = $wechatUser['original']['unionid'];
            $data['last_login_ip'] = request()->ip();
            $data['last_login_time'] = date('Y-m-d H:i:s');

            // 插入用户表
            $uid = User::insertGetId($data);
    
            $updateData['phone'] = Str::random(8) . '-' . $uid;
            $updateData['email'] = Str::random(8) . '-' . $uid;
            $updateData['username'] = Str::random(8) . '-' . $uid;

            // 更新用户表
            User::where('id',$uid)->update($updateData);

            $result = $uid;

             // 提交事务
            DB::commit();	
        } catch (\Exception $e) {
            // 回滚事务
            $result = false;

            DB::rollback();
        }

        return $result;
    }

    /**
     * 绑定账户信息，分为存在WEB用户、不存在WEB用户两种情况；
     * 存在用户的情况：可以通过账号登录或者手机号快捷登录绑定用户；
     * 不存在用户的情况：完善用户名、密码、邮箱、手机号等。
     */
    public function bindAccount(Request $request)
    {
        $wechatUser = session('wechat_user');
        // todo
    }
}