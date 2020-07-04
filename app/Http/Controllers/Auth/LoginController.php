<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\Models\ActionLog;
use App\Models\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $username;

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
     * 通用登录
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function login(Request $request)
    {
        $type = $request->input('type');
        if($request->isMethod('post')) {
            $username = $request->input('username');
            $password = $request->input('password');
            $captcha = $request->input('captcha');
            
            // 一天内累计6次登录错误，则必须开启验证码
            $loginErrorCount = ActionLog::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
            ->where('remark','用户'.$username.'尝试登录出错！')
            ->count();
            
            if($loginErrorCount > 6) {
                $getCaptcha = session('captcha');
                if(empty($captcha) || ($captcha != $getCaptcha)) {
                    return $this->error('验证码错误！');
                }
            }

            if(empty($username) || empty($password)) {
                return error('用户名或密码不能为空！');
            }

            // 用户名登录
            $nameLoginSuccess = Auth::guard('web')->attempt(['username' => $username, 'password' => $password]);

            // 邮箱登录
            $emailLoginSuccess = Auth::guard('web')->attempt(['email' => $username, 'password' => $password]);

            // 手机登录
            $phoneLoginSuccess = Auth::guard('web')->attempt(['phone' => $username, 'password' => $password]);
            
            if ($nameLoginSuccess || $emailLoginSuccess || $phoneLoginSuccess) {
                $id = auth('web')->user()->id;

                // 更新登录信息
                $data['last_login_ip'] = $_SERVER["REMOTE_ADDR"];
                $data['last_login_time'] = date('Y-m-d H:i:s');
                User::where('id',$id)->update($data);

                // 根据ip获取地址
                $getAddress = get_address($_SERVER["REMOTE_ADDR"]);
                $remark = '浏览器 '.$getAddress['province'].' '.$getAddress['city'];
                action_log($id,$remark,'USER');

                if(is_mobile()) {
                    return success('登录成功','mobile/index');
                } else {
                    return success('登录成功','index');
                }
                
            } else {

                // 记录登录日志
                $remark = '用户'.$username.'尝试登录出错！';
                action_log(0,$remark,'USER');

                // 清除验证码
                session(['captcha'=>null]);
                
                return error('用户名或密码错误！');
            }
        } else {
            if(is_mobile()) {
                return view('auth.mobile.login');
            } else {
                return view('auth.login');
            }
        }
    }

    /**
     * 快捷登录
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function quickLogin(Request $request)
    {

        $phone = $request->input('phone');
        $code = $request->input('code');

        $validateStatus = validate_sms_code($phone,$code);

        if($validateStatus != 'ok') {
            return error($validateStatus);
        }

        $hasPhone = User::where('phone',$phone)->first();

        if(empty($hasPhone)) {
            $data['phone'] = $phone;
            $data['password'] = bcrypt(env('APP_KEY'));
            $data['money'] = 0;
            $data['point'] = 0;
            $data['status'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');

            $uid = User::insertGetId($data);
            if($uid) {
                $updateData['username'] = Str::random(8).'-ID'.$uid;
                $updateData['nickname'] = Str::random(8).'-ID'.$uid;
                User::where('id',$uid)->update($updateData);
            } else {
                return error('注册失败，请重试！');
            }
        } else {
            $uid = $hasPhone->id;
        }

        // 通过uid登录
        $quickLoginSuccess = Auth::loginUsingId($uid);

        if ($quickLoginSuccess) {
            $id = auth('web')->user()->id;

            // 更新登录信息
            $loginData['last_login_ip'] = $_SERVER["REMOTE_ADDR"];
            $loginData['last_login_time'] = date('Y-m-d H:i:s');
            User::where('id',$id)->update($loginData);

            // 根据ip获取地址
            $getAddress = get_address($_SERVER["REMOTE_ADDR"]);

            // 记录登录日志
            $remark = '浏览器 '.$getAddress['province'].' '.$getAddress['city'];
            
            action_log($id,$remark,'USER');

            return success('登录成功','mobile/index/index');
        } else {

            // 记录登录日志
            $remark = '用户'.$username.'尝试登录出错！';
            action_log(0,$remark,'USER');

            return error('用户名或密码错误！');
        }
    }

    /**
     * 获取登录错误次数
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function loginErrorTimes(Request $request)
    {
        $username = $request->input('username');

        // 一天内累计6次登录错误，则必须开启验证码
        $loginErrorCount = ActionLog::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
        ->where('remark','用户'.$username.'尝试登录出错！')
        ->count();

        return success($loginErrorCount);
    }

    /**
     * 退出
     * @author  tangtanglove <dai_hang_love@126.com>
     */
    public function logout()
    {
        $result = Auth::guard('web')->logout();
        return redirect('login');
    }
}