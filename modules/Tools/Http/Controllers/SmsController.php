<?php

namespace Modules\Tools\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sms;
use Session;

class SmsController extends Controller
{
    /**
     * 发送短信验证码
     * @param  integer
     * @return string
     */
    public function send(Request $request)
    {
        $phone = $request->input('phone');
        $captcha = $request->input('captcha');
        $type = $request->input('type');
        $getCaptcha = session('captcha');

        // 图形验证码
        if (empty($captcha) || ($captcha != $getCaptcha)) {
            return error('验证码错误！');
        }

        if(empty($phone)) {
            return error('手机号不能为空！');
        }

        if(!preg_match("/^1[3456789]\d{9}$/", $phone)) {
            return error('手机号格式不正确！');
        }

        $sms = Sms::where('phone',$phone)
        ->orderBy('id', 'desc')
        ->first();

        // 每隔60秒才能发送短信
        if(!empty($sms)) {
            if((time() - strtotime($sms->created_at)) < 60 ) {
                return error('抱歉，您短信发送过于频繁！');
            }
        }

        $sendDayCount = Sms::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
        ->where('phone',$phone)->count();

        // 每天最多发送15条短信
        if($sendDayCount >15) {
            return error('抱歉，每个手机号一天最多获取十五条短信！');
        }

        // 生成验证码
        $code = mt_rand(100000,999999);
        $content = '验证码：'.$code.'，请及时输入完成验证。如非本人操作，请忽略。';

        switch ($type) {
            case 'sioo': // 希奥发送短信验证码
                $result = sioo_send_sms($phone,$content);
                break;
            
            default: // 默认阿里大鱼短信验证码
                $templateCode = Helper::config('ALIDAYU_TEMPLATE_CODE');
                $smsParam = [ 'code' => $code];
                $result = alidayu_send_sms($templateCode,$phone,$smsParam);
                break;
        }

        $data['phone'] = $phone;
        $data['code'] = $code;
        $data['content'] = $content;

        if($result) {
            $data['status'] = 1;
            Sms::create($data);
            return success('短信已发送，请注意查收！');
        } else {
            $data['status'] = 2;
            Sms::create($data);
            return error('短信发送失败！');
        }
    }
}
